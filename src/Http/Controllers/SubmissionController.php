<?php

namespace Ogwebsolutions\FormBuilder\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Ogwebsolutions\FormBuilder\Models\OgForm;
use Ogwebsolutions\FormBuilder\Models\OgFormSubmission;
use Ogwebsolutions\FormBuilder\Models\OgFormSubmissionValue;

class SubmissionController extends Controller
{
    public function index($formId, Request $request)
    {
        $query = OgFormSubmission::with(['values.largeValue'])
            ->where('og_form_id', $formId)
            ->latest();

        if ($request->filled('search')) {
            $searchTerm = $request->search;

            $query->whereHas('values', function ($q) use ($searchTerm) {
                $q->where('label', 'like', "%{$searchTerm}%")
                    ->orWhere('value', 'like', "%{$searchTerm}%")
                    ->orWhereHas('largeValue', function ($q2) use ($searchTerm) {
                        $q2->where('value', 'like', "%{$searchTerm}%");
                    });
            });
        }

        $submissions = $query->paginate(10);
        $form = OgForm::find($formId);

        // Get all unique field labels across all submissions for this form
        $allLabels = OgFormSubmission::with('values')
            ->where('og_form_id', $formId)
            ->get()
            ->pluck('values')
            ->flatten()
            ->pluck('label')
            ->filter()
            ->unique()
            ->values();

        // Get visible labels from query param
        $visibleLabels = collect(explode(',', $request->get('visible_labels')))
            ->filter()
            ->unique()
            ->values();

        // If visible_labels are provided, filter the list
        $displayedLabels = $visibleLabels->isNotEmpty()
            ? $allLabels->filter(fn($label) => $visibleLabels->contains($label))->values()
            : $allLabels->take(5);

        return view('ogformbuilder::submissions', [
            'submissions' => $submissions,
            'form' => $form,
            'uniqueLabels' => $displayedLabels, // for table columns
            'allLabels' => $allLabels            // for modal checkboxes
        ]);
    }


    public function show($id)
    {
        $submission = OgFormSubmission::with('values.largeValue')->findOrFail($id);
        return view('ogformbuilder::submissions.show', compact('submission'));
    }

    public function destroy($id)
    {
        $submission = OgFormSubmission::findOrFail($id);
        $submission->delete();

        return back()->with('success', 'Submission deleted successfully.');
    }

    //Save Form Submission
    public function store(Request $request, OgForm $form)
    {
        try {
            // Prepare form fields structure
            $formFields = collect($form->fields); //It's an array of fields with 'name' and 'label'
            // Check if there's a CAPTCHA field in this form
            $captchaField = $formFields->firstWhere('type', 'captcha');

            if ($captchaField && isset($captchaField['subtype'])) {
                $captchaType = $captchaField['subtype'];

                if ($captchaType === 'recaptcha') {
                    $captchaResponse = $request->input('g-recaptcha-response');
                    $verify = Http::asForm()->post(config('recaptcha.verify_url'), [
                        'secret'   => config('recaptcha.secret'),
                        'response' => $captchaResponse,
                        'remoteip' => $request->ip(),
                    ]);
                } elseif ($captchaType === 'hcaptcha') {
                    $captchaResponse = $request->input('h-captcha-response');

                    $verify = Http::asForm()->post(config('hcaptcha.verify_url'), [
                        'secret'   => config('hcaptcha.secret'),
                        'response' => $captchaResponse,
                        'remoteip' => $request->ip(),
                    ]);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Unsupported CAPTCHA type.'], 422);
                }

                // Check CAPTCHA verification result
                $captchaResult = $verify->json();

                if (!($captchaResult['success'])) {
                    return response()->json([
                        'status' => 'error',
                        'message' => ucfirst($captchaType) . ' verification failed. Please try again.',
                    ], 422);
                }
            }

            $inputKeys = $formFields->filter(function ($field) {
                return in_array($field['type'], ['text', 'number', 'date', 'textarea', 'autocomplete', 'checkbox-group', 'file', 'select', 'hidden', 'radio-group']);
            });

            $requiredFields = $inputKeys->filter(function ($field) {
                return $field['required'] ?? false;
            });

            $validator = Validator::make($request->all(), $requiredFields->flatMap(function ($field) {
                return [$field['name'] => 'required'];
            })->toArray(), $requiredFields->flatMap(function ($field) {
                $label = $field['label'] ?? $field['name'];
                return ["{$field['name']}.required" => "The {$label} field is required."];
            })->toArray());

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
            }

            // Eager load settings
            $form->load('settings');

            // Create submission
            $submission = OgFormSubmission::create([
                'og_form_id' => $form->id,
                'token' => $request->input('_token'),
                'ip' => $request->ip(),
            ]);

            // Save submitted values
            foreach ($request->only($inputKeys->pluck('name')->toArray()) as $key => $value) {
                $fieldDefinition = $formFields->firstWhere('name', $key);
                $label = $fieldDefinition['label'] ?? $key;

                if ($request->hasFile($key)) {
                    $file = $request->file($key);
                    $filename = $file->store('uploads', 'public'); // saves to storage/app/public/uploads

                    $submissionValue = OgFormSubmissionValue::create([
                        'og_form_submission_id' => $submission->id,
                        'key' => $key,
                        'label' => $label,
                        'value' => $filename,
                    ]);
                } else {
                    $inputVal = is_array($value) ? implode(', ', $value) : $value;
                    $submissionValue = OgFormSubmissionValue::create([
                        'og_form_submission_id' => $submission->id,
                        'key' => $key,
                        'label' => $label,
                        'value' => strlen($inputVal) <= 255 ? $inputVal : '',
                    ]);

                    if (is_string($inputVal) && strlen($inputVal) > 255) {
                        $submissionValue->largeValue()->create(['value' => $inputVal]);
                    }
                }
            }

            $settings = $form->settings;

            // Admin Email with CSV Attachment
            if ($settings->admin_email_enabled && !empty($settings->admin_emails)) {
                $adminBody = $this->replacePlaceholders($settings->admin_email_body ?: '{{ all_fields }}', $submission, $form);

                $adminBody = is_array($adminBody) ? implode(', ', $adminBody) : $adminBody;
                $adminEmails = array_map('trim', $settings->admin_emails);

                Mail::html($adminBody, function ($message) use ($settings, $adminEmails, $submission, $form) {
                    $message->to($adminEmails)
                        ->subject($settings->admin_email_subject ?: 'New Form Submission');

                    if ($settings->admin_csv_enabled) {
                        // Build CSV content
                        $headers = ['Field Label', 'Value'];
                        $csvData = $submission->values->map(function ($item) {
                            return [
                                $item->label,
                                $item->largeValue->value ?? $item->value
                            ];
                        });

                        // Create temporary CSV file
                        $filename = 'form_' . $form->id . '_submission_' . $submission->id . '.csv';
                        $tempPath = storage_path("app/temp_{$filename}");
                        $handle = fopen($tempPath, 'w+');
                        fputcsv($handle, $headers);
                        foreach ($csvData as $row) {
                            fputcsv($handle, $row);
                        }
                        fclose($handle);

                        // Attach file
                        $message->attach($tempPath, [
                            'as' => $filename,
                            'mime' => 'text/csv',
                        ]);

                        // Cleanup after mail is sent (handled outside this closure)
                        app()->terminating(function () use ($tempPath) {
                            if (file_exists($tempPath)) {
                                unlink($tempPath);
                            }
                        });
                    }
                });
            }



            // Customer Email
            if ($settings->customer_email_enabled && !empty($settings->customer_emails)) {
                $customerBody = $this->replacePlaceholders($settings->customer_email_body ?: '{{ all_fields }}', $submission, $form);
                $customerBody = is_array($customerBody) ? implode(', ', $customerBody) : $customerBody;
                $customerEmailsRaw = $settings->customer_emails;
                // Replace any placeholder patterns like {{ field-123 }} with actual values
                $customerEmailsRaw = preg_replace_callback('/\{\{\s*([^}]+)\s*\}\}/', function ($matches) use ($submission) {
                    $fieldKey = trim($matches[1]);
                    $value = $submission->values->where('key', $fieldKey)->first();
                    return $value ? ($value->largeValue->value ?? $value->value) : $matches[0];
                }, $customerEmailsRaw);
                $customerEmails = array_map('trim', explode(',', $customerEmailsRaw));
                Mail::html($customerBody, function ($message) use ($settings, $customerEmails) {
                    $message->to($customerEmails)
                        ->subject($settings->customer_email_subject ?: 'Thank you for your submission');
                });
            }

            $redirectUrl = $settings->redirect_url ?? null;
            $successMessage = $settings->success_message ?? 'Form submitted successfully.';

            return response()->json(['status' => 'success', 'message' => $successMessage, 'redirectUrl' => $redirectUrl], 200);
        } catch (\Exception $e) {
            Log::error('Form submission error: ' . $e->getMessage());

            $failureMessage = $form->settings->failure_message ?? 'Something went wrong while submitting the form.';

            return response()->json(['status' => 'error', 'message' => $failureMessage], 500);
        }
    }

    //Export submissions to CSV
    public function exportCsv(OgForm $form)
    {
        $submissions = $form->submissions()->with('values.largeValue')->get();

        // Get unique labels across submissions
        $labels = $submissions->flatMap(function ($submission) {
            return $submission->values->pluck('label');
        })->unique()->values()->toArray();

        $headers = array_merge(['ID', 'Submitted At'], $labels);
        $rows = [];

        foreach ($submissions as $submission) {
            $row = [
                $submission->id,
                $submission->created_at->format('Y-m-d H:i'),
            ];

            foreach ($labels as $label) {
                $value = $submission->values->firstWhere('label', $label);
                $row[] = $value ? ($value->largeValue->value ?? $value->value) : '';
            }

            $rows[] = $row;
        }

        $filename = 'form_' . $form->id . '_submissions_' . now()->format('Ymd_His') . '.csv';

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $headers);
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
    //Replace placeholders in Email body
    protected function replacePlaceholders($template, OgFormSubmission $submission, OgForm $form)
    {
        $placeholders = [];

        // Build individual field placeholders
        foreach ($submission->values as $value) {
            $placeholders["{{ {$value->key} }}"] = $value->largeValue->value ?? $value->value;
        }

        // Add global placeholders
        $placeholders['{{ submission_date }}'] = now()->toDateTimeString();
        $placeholders['{{ form_title }}'] = $form->title;
        $placeholders['{{ all_fields }}'] = $submission->values->map(function ($item) {
            return "<strong>{$item->label}:</strong> " . ($item->largeValue->value ?? $item->value);
        })->implode('<br>');

        // Replace placeholders
        return strtr($template, $placeholders);
    }
}
