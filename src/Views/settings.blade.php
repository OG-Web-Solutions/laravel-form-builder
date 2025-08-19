@extends(config('ogformbuilder.layout'))

@section('title', 'Form Settings')

@section('content')
    <div class="container py-4">
        <div class="page-header">
            <h1 class="page-title">Settings for: {{ $form->title }}</h1>
            <div class="d-flex align-items-center gap-2 mb-4">
                <a href="{{ route('formbuilder.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Forms
                </a>
                <a href="{{ route('formbuilder.submissions', $form->id) }}" class="btn btn-secondary">
                    <i class="fas fa-list me-2"></i>Submissions
                </a>
                <a href="{{ route('formbuilder.edit', $form->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Edit Form
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('formbuilder.settings.save', $form->id) }}" class="needs-validation" novalidate>
                    @csrf

                    <!-- ADMIN EMAIL SETTINGS -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="mb-0">Admin Email Settings</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Enable Admin Email?</label>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" name="admin_email_enabled" id="admin_email_enabled_yes" value="1"
                                        {{ old('admin_email_enabled', $settings->admin_email_enabled) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="admin_email_enabled_yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" name="admin_email_enabled" id="admin_email_enabled_no" value="0"
                                        {{ !old('admin_email_enabled', $settings->admin_email_enabled) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="admin_email_enabled_no">No</label>
                                </div>
                            </div>

                            <div class="admin-email-settings">
                                <div class="mb-3">
                                    <label class="form-label">Admin Email Subject</label>
                                    <input type="text" name="admin_email_subject" class="form-control"
                                        value="{{ old('admin_email_subject', $settings->admin_email_subject) }}"
                                        placeholder="Default: New Form Submission">
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-label mb-0">Admin Email Body</label>
                                        <div class="position-relative mb-2">
                                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                                onclick="togglePlaceholderPopup('admin-placeholder-popup')"
                                                aria-label="Show placeholder options">
                                                <i class="fas fa-code me-1"></i>{{ '{...}' }}
                                            </button>

                                            <div id="admin-placeholder-popup" class="placeholder-popup shadow-sm" style="display: none;">
                                                <strong class="d-block mb-2">Click to copy:</strong>
                                                <ul class="list-unstyled m-0">
                                                    <li onclick="copyPlaceholder('{{ '\{\{ form_title \}\}' }}')">
                                                        <?php echo htmlspecialchars('{{ form_title }}'); ?> - Form Title
                                                    </li>
                                                    <li onclick="copyPlaceholder('{{ '\{\{ submission_date \}\}' }}')">
                                                        <?php echo htmlspecialchars('{{ submission_date }}'); ?> - Submission Date
                                                    </li>
                                                    <li onclick="copyPlaceholder('{{ '\{\{ all_fields \}\}' }}')">
                                                        <?php echo htmlspecialchars('{{ all_fields }}'); ?> - All Fields
                                                    </li>
                                                    @foreach ($form->fields as $field)
                                                        @if (($field['type'] ?? '') !== 'button' && ($field['type'] ?? '') !== 'captcha' && isset($field['name']))
                                                            <li onclick="copyPlaceholder('{{ '\{\{ ' . $field['name'] . ' \}\}' }}')">
                                                                <?php echo htmlspecialchars('{{ ' . $field['name'] . ' }}'); ?> - {{ $field['label'] ?? $field['name'] }}
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <textarea name="admin_email_body" class="form-control tinymce-editor" rows="4">{{ old('admin_email_body', $settings->admin_email_body) }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Admin Emails (comma separated)</label>
                                    <input type="text" name="admin_emails" class="form-control"
                                        value="{{ old('admin_emails', $settings->admin_emails ? implode(',', $settings->admin_emails) : '') }}"
                                        placeholder="info@example.com, info2@example.com, info3@example.com ...">
                                    @error('admin_emails')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Attach Submission as CSV?</label>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" name="admin_csv_enabled" id="admin_csv_enabled_yes" value="1"
                                            {{ old('admin_csv_enabled', $settings->admin_csv_enabled) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="admin_csv_enabled_yes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" name="admin_csv_enabled" id="admin_csv_enabled_no" value="0"
                                            {{ !old('admin_csv_enabled', $settings->admin_csv_enabled) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="admin_csv_enabled_no">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CUSTOMER EMAIL SETTINGS -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="mb-0">Customer Email Settings</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Enable Customer Email?</label>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" name="customer_email_enabled" id="customer_email_enabled_yes" value="1"
                                        {{ old('customer_email_enabled', $settings->customer_email_enabled) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="customer_email_enabled_yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" name="customer_email_enabled" id="customer_email_enabled_no" value="0"
                                        {{ !old('customer_email_enabled', $settings->customer_email_enabled) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="customer_email_enabled_no">No</label>
                                </div>
                            </div>

                            <div class="customer-email-settings">
                                <div class="mb-3">
                                    <label class="form-label">Customer Email Subject</label>
                                    <input type="text" name="customer_email_subject" class="form-control"
                                        value="{{ old('customer_email_subject', $settings->customer_email_subject) }}"
                                        placeholder="Default: Thank you for your submission">
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-label mb-0">Customer Email Body</label>
                                        <div class="position-relative mb-2">
                                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                                onclick="togglePlaceholderPopup('customer-placeholder-popup')"
                                                aria-label="Show placeholder options">
                                                <i class="fas fa-code me-1"></i>{{ '{...}' }}
                                            </button>

                                            <div id="customer-placeholder-popup" class="placeholder-popup shadow-sm" style="display: none;">
                                                <strong class="d-block mb-2">Click to copy:</strong>
                                                <ul class="list-unstyled m-0">
                                                    <li onclick="copyPlaceholder('{{ '\{\{ form_title \}\}' }}')">
                                                        <?php echo htmlspecialchars('{{ form_title }}'); ?> - Form Title
                                                    </li>
                                                    <li onclick="copyPlaceholder('{{ '\{\{ submission_date \}\}' }}')">
                                                        <?php echo htmlspecialchars('{{ submission_date }}'); ?> - Submission Date
                                                    </li>
                                                    <li onclick="copyPlaceholder('{{ '\{\{ all_fields \}\}' }}')">
                                                        <?php echo htmlspecialchars('{{ all_fields }}'); ?> - All Fields
                                                    </li>
                                                    @foreach ($form->fields as $field)
                                                        @if (($field['type'] ?? '') !== 'button' && ($field['type'] ?? '') !== 'captcha' && isset($field['name']))
                                                            <li onclick="copyPlaceholder('{{ '\{\{ ' . $field['name'] . ' \}\}' }}')">
                                                                <?php echo htmlspecialchars('{{ ' . $field['name'] . ' }}'); ?> - {{ $field['label'] ?? $field['name'] }}
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <textarea name="customer_email_body" class="form-control tinymce-editor" rows="4">{{ old('customer_email_body', $settings->customer_email_body) }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Customer Email</label>
                                    <input type="text" name="customer_emails" class="form-control"
                                        value="{{ old('customer_emails', $settings->customer_emails) }}"
                                        placeholder="Short Tag: <?php echo '{'.'{'; ?> customer_email <?php echo '}}'; ?>">
                                    @error('customer_emails')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FORM SUBMISSION BEHAVIOR -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="mb-0">Form Submission Behavior</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Success Message</label>
                                <textarea name="success_message" class="form-control tinymce-editor" rows="3">{{ old('success_message', $settings->success_message) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Failure Message</label>
                                <textarea name="failure_message" class="form-control tinymce-editor" rows="3">{{ old('failure_message', $settings->failure_message) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Redirect URL After Submission</label>
                                <input type="url" name="redirect_url" class="form-control"
                                    placeholder="https://example.com/thank-you"
                                    value="{{ old('redirect_url', $settings->redirect_url) }}">
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="clipboard-toast" class="toast-container position-fixed bottom-0 end-0 p-3"></div>
@endsection

@push('styles')
    <style>
        .placeholder-popup {
            position: absolute;
            top: 100%;
            right: 0;
            background-color: #fff;
            border: 1px solid rgba(0,0,0,.1);
            padding: 1rem;
            border-radius: 0.375rem;
            z-index: 1050;
            min-width: 240px;
            margin-top: 0.5rem;
        }

        .placeholder-popup ul li {
            cursor: pointer;
            padding: 0.5rem;
            color: var(--bs-primary);
            border-radius: 0.25rem;
            transition: all 0.2s ease-in-out;
        }

        .placeholder-popup ul li:hover {
            background-color: var(--bs-light);
            color: var(--bs-primary);
        }

        .toast-container {
            z-index: 1060;
        }

        .card {
            --bs-card-border-color: rgba(0,0,0,.1);
        }

        .form-label {
            font-weight: 500;
        }

        .btn-close {
            font-size: 0.875rem;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.tiny.cloud/1/im8ijoyq5pzl2n10hwdc1e1sy3mkikvixo9b7skons0w1l5r/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>

    <script>
        tinymce.init({
            selector: 'textarea.tinymce-editor',
            min_height: 250,
            max_height: 800,
            menubar: false,
            plugins: 'code link table lists autoresize preview fullscreen',
            toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | link table | preview fullscreen code',
            autoresize_bottom_margin: 20,
            branding: false,
            resize: true,
            statusbar: true
        });

        function toggleAdminEmailSettings() {
            const isEnabled = document.querySelector('input[name="admin_email_enabled"]:checked')?.value === "1";
            document.querySelector('.admin-email-settings').style.display = isEnabled ? 'block' : 'none';
        }

        function toggleCustomerEmailSettings() {
            const isEnabled = document.querySelector('input[name="customer_email_enabled"]:checked')?.value === "1";
            document.querySelector('.customer-email-settings').style.display = isEnabled ? 'block' : 'none';
        }

        document.addEventListener('DOMContentLoaded', function() {
            toggleAdminEmailSettings();
            toggleCustomerEmailSettings();

            document.querySelectorAll('input[name="admin_email_enabled"]').forEach(function(radio) {
                radio.addEventListener('change', toggleAdminEmailSettings);
            });

            document.querySelectorAll('input[name="customer_email_enabled"]').forEach(function(radio) {
                radio.addEventListener('change', toggleCustomerEmailSettings);
            });
        });

        function togglePlaceholderPopup(popupId) {
            const popup = document.getElementById(popupId);
            if (!popup) return;

            document.querySelectorAll('.placeholder-popup').forEach(p => {
                if (p.id !== popupId) p.style.display = 'none';
            });

            popup.style.display = (popup.style.display === 'block') ? 'none' : 'block';
        }

        function copyPlaceholder(text) {
            navigator.clipboard.writeText(text).then(() => {
                showToast('Copied to clipboard: ' + text);
                document.querySelectorAll('.placeholder-popup').forEach(p => p.style.display = 'none');
            }).catch(err => {
                console.error('Failed to copy:', err);
            });
        }

        function showToast(message) {
            const toastContainer = document.getElementById('clipboard-toast');
            const toast = document.createElement('div');
            toast.className = 'show toast align-items-center text-white bg-success border-0';
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');

            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;

            toastContainer.appendChild(toast);
            setTimeout(() => {
                toastContainer.innerHTML = '';
            }, 3000);
        }

        document.addEventListener('click', function(event) {
            document.querySelectorAll('.placeholder-popup').forEach(popup => {
                const button = document.querySelector(`button[onclick*="${popup.id}"]`);
                if (popup.style.display === 'block' &&
                    !popup.contains(event.target) &&
                    (!button || !button.contains(event.target))) {
                    popup.style.display = 'none';
                }
            });

            const toastClose = document.querySelector('[data-bs-dismiss="toast"]');
            if(toastClose && toastClose.contains(event.target)) {
                const toastContainer = document.getElementById('clipboard-toast');
                toastContainer.innerHTML = ''
            }
        });


    </script>
@endpush
