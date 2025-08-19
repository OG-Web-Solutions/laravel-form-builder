<?php

namespace Ogwebsolutions\FormBuilder\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Ogwebsolutions\FormBuilder\Models\OgForm;
use Ogwebsolutions\FormBuilder\Models\OgFormSetting;



class FormController extends Controller
{
    public function index()
    {
        $forms = OgForm::latest()->paginate(10); // Paginate 10 forms per page
        return view('ogformbuilder::index', compact('forms'));
    }

    public function create()
    {
        return view('ogformbuilder::builder');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'form_json' => 'required|json',
        ]);
        // Validate Title field
        if (!$request->filled('title')) {
            return redirect()->back()
                ->withErrors(['title' => 'Title field is required.'])
                ->withInput();
        }
        // Validate form_json
        if (!$request->filled('form_json')) {
            return redirect()->back()
                ->withErrors(['form_json' => 'Adding at least one field is required.'])
                ->withInput();
        }

       $form = OgForm::create([
            'title' => $request->input('title'),
            'fields' => json_decode($request->input('form_json'), true),
            'status' => $request->input('status', 'inactive') // default to inactive

        ]);

        // Insert default settings
         OgFormSetting::create([
            'og_form_id' => $form->id,
            'admin_email_enabled'      => false,
            'customer_email_enabled'   => false,
            'admin_email_subject'      => 'New Form Submission',
            'admin_email_body'         => 'You have received a new submission.<br> {{ all_fields }}',
            'admin_emails'             => null,
            'customer_email_subject'   => 'Thank you for your submission',
            'customer_email_body'      => 'We have received your submission and will get back to you soon.<br> {{ all_fields }}',
            'customer_emails'          => null,
            'admin_csv_enabled'        => true,
            'success_message'          => 'Your form was submitted successfully!',
            'failure_message'          => 'Something went wrong. Please try again.',
            'redirect_url'             => null,
        ]);

        return redirect()->route('formbuilder.index')->with('success', 'Form created successfully.');
    }

    public function settings($id)
    {

        $form = OgForm::findOrFail($id);
        $settings = OgFormSetting::firstOrNew(['og_form_id' => $form->id]);
        return view('ogformbuilder::settings', compact('form', 'settings'));
    }
    public function saveSettings(Request $request, $id)
    {
        $form = OgForm::findOrFail($id);

        $request->validate([
            'admin_email_enabled' => 'nullable|boolean',
            'customer_email_enabled' => 'nullable|boolean',
            'admin_email_subject' => 'nullable|string|max:255',
            'admin_email_body' => 'nullable|string',
            'admin_emails' => 'nullable|string',
            'admin_csv_enabled' => 'nullable|boolean',
            'customer_email_subject' => 'nullable|string|max:255',
            'customer_email_body' => 'nullable|string',
            'customer_emails' => 'nullable|string',
            'success_message'         => 'nullable|string',
            'failure_message'         => 'nullable|string',
            'redirect_url'            => 'nullable|url',
        ]);

        // Validate admin emails if email notifications are enabled
        if ($request->admin_email_enabled && !$request->filled('admin_emails')) {
            return redirect()->back()
                ->withErrors(['admin_emails' => 'Admin emails field is required.'])
                ->withInput();
        }

        // Validate email format and domain existence
        if ($request->admin_email_enabled) {
            if (!$this->isValidEmailList($request->admin_emails)) {
                return redirect()->back()
                    ->withErrors(['admin_emails' => 'One or more admin emails are invalid.'])
                    ->withInput();
            }

            // Check email count limit
            $emailCount = count(array_filter(explode(',', $request->admin_emails)));
            if ($emailCount > 10) {
                return redirect()->back()
                    ->withErrors(['admin_emails' => 'You can only add up to 10 email addresses.'])
                    ->withInput();
            }
        }

        // Validate customer emails if email notifications are enabled
        if ($request->customer_email_enabled && !$request->filled('admin_emails')) {
            return redirect()->back()
                ->withErrors(['admin_emails' => 'Admin emails field is required.'])
                ->withInput();
        }

        // Validate customer emails if email notifications are enabled
        if ($request->customer_email_enabled && !$request->filled('customer_emails')) {
            return redirect()->back()
                ->withErrors(['customer_emails' => 'Customer emails field is required.'])
                ->withInput();
        }

        $settings = OgFormSetting::updateOrCreate(
            ['og_form_id' => $form->id],
            [
                'admin_email_enabled'      => $request->input('admin_email_enabled', false),
                'admin_email_subject'      => $request->input('admin_email_subject'),
                'admin_email_body'         => $request->input('admin_email_body'),
                'admin_emails'             => explode(',', $request->input('admin_emails')),
                'customer_email_enabled'   => $request->input('customer_email_enabled', false),
                'customer_email_subject'   => $request->input('customer_email_subject'),
                'customer_email_body'      => $request->input('customer_email_body'),
                'customer_emails'          => $request->input('customer_emails'),
                'admin_csv_enabled' => $request->input('admin_csv_enabled'),
                // Additional Settings
                'success_message'         => $request->input('success_message'),
                'failure_message'         => $request->input('failure_message'),
                'redirect_url'            => $request->input('redirect_url'),
            ]
        );

        return redirect()->back()->with('success', 'Settings saved successfully.');
    }

    public function edit($id)
    {
        $form = OgForm::findOrFail($id);
        return view('ogformbuilder::edit', compact('form'));
    }

    public function update(Request $request, $id)
    {
        $form = OgForm::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'form_json' => 'required|json',
            'status' => 'required|in:active,inactive'
        ]);
         // Validate Title field
        if (!$request->filled('title')) {
            return redirect()->back()
                ->withErrors(['title' => 'Title field is required.'])
                ->withInput();
        }



        $form->update([
            'title' => $request->input('title'),
            'fields' => json_decode($request->input('form_json'), true),
            'status' => $request->input('status')
        ]);

        return redirect()->route('formbuilder.index')->with('success', 'Form updated successfully.');
    }

    public function destroy($id)
    {
        $form = OgForm::findOrFail($id);
        $form->delete();

        return redirect()->route('formbuilder.index')->with('success', 'Form deleted successfully.');
    }

    /**
     * Validate email list format
     *
     * @param string $emails
     * @param string $fieldName
     * @return void
     */
    protected function validateEmailList($emails, $fieldName)
    {
        foreach (explode(',', $emails) as $email) {
            if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                throw ValidationException::withMessages([
                    $fieldName => 'One or more emails are invalid.',
                ]);
            }
        }
    }

    protected function isValidEmailList($emails)
    {
        foreach (explode(',', $emails) as $email) {
            if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                return false;
            }
        }
        return true;
    }
}
