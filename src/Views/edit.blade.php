@extends(config('ogformbuilder.layout'))

@section('title', 'Edit Form')

@push('styles')
<style>
    .action-buttons {
        display: flex;
        gap: 10px;
    }
    .card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        padding: 20px;
        margin-bottom: 20px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-label {
        display: block;
        font-weight: 500;
        margin-bottom: 8px;
        color: #333;
    }
    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        transition: border-color 0.2s;
    }
    .form-control:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }
    .radio-group {
        display: flex;
        gap: 20px;
        padding: 10px 0;
    }
    .radio-label {
        margin-left: 5px;
        color: #555;
    }
    .error-message {
        color: #dc3545;
        font-size: 14px;
        margin-top: 5px;
    }
    #fb-editor {
        min-height: 500px;
        border: 1px solid #e9ecef;
        border-radius: 4px;
        margin: 20px 0;
        background: #fff;
    }
    .btn {
        padding: 8px 16px;
        border-radius: 4px;
        font-weight: 500;
        transition: all 0.2s;
    }
    .btn-primary {
        background: #007bff;
        border: 1px solid #0056b3;
        color: white;
    }
    .btn-info {
        background: #17a2b8;
        border: 1px solid #138496;
        color: white;
    }
    .btn-secondary {
        background: #6c757d;
        border: 1px solid #545b62;
        color: white;
    }
</style>
@endpush

@section('content')
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Edit Form: {{ $form->title }}</h1>
            <div class="d-flex align-items-center gap-2 mb-4">
                <a href="{{ route('formbuilder.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Forms
                </a>
                <a href="{{ route('formbuilder.submissions', $form->id) }}" class="btn btn-secondary">
                    <i class="fas fa-list me-2"></i>Submissions
                </a>
                <a href="{{ route('formbuilder.settings', $form->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Settings
                </a>
            </div>
        </div>

        <div class="card">
            <form id="form-builder-form" method="POST" action="{{ route('formbuilder.update', $form->id) }}">
                @csrf
                @method('PUT')

                <div id="form-error-msg" class="error-message" style="display:none;"></div>

                <div class="form-group">
                    <label class="form-label" for="form-title">Form Title</label>
                    <input type="text" name="title" class="form-control" id="form-title"
                        value="{{ old('title', $form->title) }}">
                    @error('title')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <div class="radio-group">
                        <div>
                            <input type="radio" id="status-active" name="status" value="active" {{ $form->status === 'active' ? 'checked' : '' }}>
                            <label class="radio-label" for="status-active">Active</label>
                        </div>
                        <div>
                            <input type="radio" id="status-inactive" name="status" value="inactive" {{ $form->status === 'inactive' ? 'checked' : '' }}>
                            <label class="radio-label" for="status-inactive">Inactive</label>
                        </div>
                    </div>
                </div>

                <textarea id="form-json" name="form_json" hidden></textarea>

                <div id="fb-editor"></div>

                <button type="submit" class="btn btn-primary">Update Form</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- jQuery UI (sortable is included) -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <!-- Form Builder JS -->
    <script src="https://cdn.jsdelivr.net/npm/formBuilder@3.9.7/dist/form-builder.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editorElement = document.getElementById('fb-editor');
            const baseAssetUrl = "{{ route('formbuilder.assets.image', '') }}/";
            let hasCaptchaError = false;

            const fbEditor = jQuery(editorElement).formBuilder({
                disabledActionButtons: ['data', 'save', 'clear'],
                formData: @json($form->fields),
                fieldRemoveWarn: true,
                controlOrder: [
                    'text',
                    'number',
                    'textarea',
                    'select',
                    'checkbox-group',
                    'radio-group',
                    'file',
                    'date',
                    'hidden',
                    'captcha',
                    'button',
                ],
                templates: {
                    captcha: function(fieldData) {
                        const subtype = fieldData.subtype || 'hcaptcha';
                        let html = '';
                        if (subtype === 'hcaptcha') {
                            html = `
                                <div class="captcha-wrapper">
                                    <p><strong>hCaptcha</strong></p>
                                    <div style="background:#f2f2f2;padding:10px;border:1px solid #ccc;">
                                    <img src="${baseAssetUrl}hcaptcha-icon.jpeg" alt="hCaptcha" style="margin-bottom:5px;" />
                                    </div>
                                </div>`;
                        } else if (subtype === 'recaptcha') {
                            html = `
                                <div class="captcha-wrapper">
                                    <p><strong>reCAPTCHA</strong></p>
                                    <div style="background:#f9f9f9;padding:10px;border:1px solid #aaa;">
                                    <img src="${baseAssetUrl}recaptcha-icon.png" alt="reCaptcha" style="margin-bottom:5px;" />
                                    </div>
                                </div>`;
                        }
                        return {
                            field: html
                        };
                    }
                },
                fields: [{
                    label: 'Captcha',
                    type: 'captcha',
                    icon: '🔒',
                    className: 'captcha-control-item'
                },
                {
                    label: "Email",
                    type: "text",
                    subtype: "email",
                    icon: "✉"
                }],
                typeUserAttrs: {
                    captcha: {
                        subtype: {
                            label: 'Captcha Type',
                            options: {
                                hcaptcha: 'hCaptcha',
                                recaptcha: 'reCAPTCHA'
                            },
                            value: 'hcaptcha'
                        }
                    }
                },
                onOpenFieldEdit: function(editPanel) {
                    setTimeout(() => {
                        const typeInput = editPanel.querySelector('select[name="subtype"]');
                        if (typeInput && typeInput.value === 'textarea') {
                            const allGroups = editPanel.querySelectorAll('.form-group');
                            allGroups.forEach(group => {
                                const label = group.querySelector('label');
                                if (label && label.textContent.trim().toLowerCase() === 'type') {
                                    group.style.display = 'none';
                                }
                            });
                        }
                    }, 50);
                },
                onAddField: function(_, fieldData) {
                    const errorMsg = document.getElementById('form-error-msg');
                    setTimeout(() => {
                        if (fieldData.type === 'captcha') {
                            let captchaFields = [];
                            try {
                                const currentFieldsJson = fbEditor.actions.getData('json');
                                const currentFields = JSON.parse(currentFieldsJson);
                                captchaFields = currentFields.filter(f => f.type === 'captcha');
                            } catch (e) {
                                console.warn('Could not parse form data:', e);
                            }

                            if (captchaFields.length > 1) {
                                document.querySelectorAll('.frmb-control li').forEach(function(li) {
                                    if (li.dataset.type === 'captcha') {
                                        li.style.display = 'none';
                                    }
                                });

                                if (errorMsg) {
                                    hasCaptchaError = true;
                                    setTimeout(() => {
                                        errorMsg.textContent = 'Heads Up! Only one Captcha field is ideal. Please keep only one and delete the rest.';
                                        errorMsg.style.display = 'block';
                                        errorMsg.scrollIntoView({
                                            behavior: 'smooth',
                                            block: 'center'
                                        });
                                    }, 10);
                                }
                            } else {
                                hasCaptchaError = false;
                                if (errorMsg) {
                                    errorMsg.textContent = '';
                                    errorMsg.style.display = 'none';
                                }
                            }
                        }
                        const stageWrap = document.querySelector('.form-wrap.form-builder .stage-wrap');
                        if (stageWrap) {
                            stageWrap.style.border = '3px dashed #ccc';
                        }
                    }, 100);
                }
            });

            const form = document.getElementById('form-builder-form');
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const titleInput = form.querySelector('input[name="title"]');
                const jsonField = document.getElementById('form-json');
                const formData = fbEditor.actions.getData('json');
                const parsedData = JSON.parse(formData);
                const errorMsg = document.getElementById('form-error-msg');

                titleInput.style.border = '';
                const emptyStage = document.querySelector('.form-wrap.form-builder .stage-wrap.empty');
                if (emptyStage) {
                    emptyStage.style.border = '';
                }

                if (!titleInput.value.trim()) {
                    errorMsg.textContent = 'Please enter a form title.';
                    errorMsg.style.display = 'block';
                    errorMsg.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    titleInput.style.border = '2px solid red';
                    titleInput.focus();
                    return;
                }

                if (!parsedData.length) {
                    errorMsg.textContent = 'Please add at least one field before saving.';
                    errorMsg.style.display = 'block';
                    errorMsg.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    if (emptyStage) {
                        emptyStage.style.border = '3px dashed red';
                    }
                    return;
                }

                errorMsg.style.display = 'none';
                jsonField.value = formData;
                form.submit();
            });
        });
    </script>
@endpush
