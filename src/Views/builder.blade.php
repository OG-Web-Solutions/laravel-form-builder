@extends(config('ogformbuilder.layout'))

@section('title', 'Form Builder')

@section('content')
    <div class="container">
        <a href="{{ route('formbuilder.index') }}" class="btn btn-primary mb-3">Back to Forms</a>
        <h1>Form Builder</h1>

        <form id="form-builder-form" method="POST" action="{{ route('formbuilder.store') }}">
            @csrf
            <div id="form-error-msg" class="text-danger mt-2" style="display:none;"></div>
            <div class="form-group">
                <label for="form-title">Form Title</label>
                <input type="text" name="title" class="form-control" id="form-title" value="Untitled Form">
                @error('title')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group mt-3">
                <label>Status:</label><br>
                <input type="radio" name="status" value="active" id="status-active">
                <label for="status-active">Active</label>

                <input type="radio" name="status" value="inactive" id="status-inactive" checked>
                <label for="status-inactive">Inactive</label>
            </div>

            <textarea id="form-json" name="form_json" hidden></textarea>

            <div id="fb-editor"></div>

            <button type="submit" class="btn btn-primary mt-3">Save Form</button>
        </form>
    </div>
@endsection

@push('scripts')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- jQuery UI (sortable is included) -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <!-- Form Builder JS -->
    <!-- ✅ New (versioned + latest) -->
    <script src="https://cdn.jsdelivr.net/npm/formBuilder@3.9.7/dist/form-builder.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            //console.log($.fn.formBuilder.version);
            const editorElement = document.getElementById('fb-editor');
            const baseAssetUrl = "{{ route('formbuilder.assets.image', '') }}/";
            let hasCaptchaError = false;
            // 1. Your existing settings
            const fbEditor = jQuery(editorElement).formBuilder({
                disabledActionButtons: ['data', 'save', 'clear'],
                fieldRemoveWarn: true, // defaults to false,
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
                defaultFields: [{
                        label: "Name",
                        placeholder: "Enter your name",
                        name: "name",
                        required: true,
                        type: "text",
                        className: "form-control"
                    },
                    {

                        label: "Email",
                        placeholder: "Enter your email",
                        name: "email",
                        required: true,
                        type: "text",
                        subtype: "email",
                        className: "form-control"
                    },
                    {
                        label: "Submit",
                        name: "submit",
                        required: true,
                        type: "button",
                        subtype: "submit",
                        className: "btn btn-primary"
                    }
                ],
                // ✅ Define the template for the captcha field
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

                // 2. Add field to the panel
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
                    }
                ],

                // 3. Add custom dropdown (subtype) in settings panel
                typeUserAttrs: {
                    captcha: {
                        subtype: {
                            label: 'Captcha Type',
                            options: {
                                hcaptcha: 'hCaptcha',
                                recaptcha: 'reCAPTCHA'
                            },
                            value: 'hcaptcha' // default
                        }
                    }
                },
                // 4: Your existing logic to hide type dropdown for textarea
                onOpenFieldEdit: function(editPanel) {
                    // Wait for the edit panel to be fully rendered
                    setTimeout(() => {
                        const typeInput = editPanel.querySelector('select[name="subtype"]');
                        // If it's a textarea field
                        if (typeInput && typeInput.value === 'textarea') {
                            // Find and hide the "Type" form-group
                            const allGroups = editPanel.querySelectorAll('.form-group');
                            allGroups.forEach(group => {
                                const label = group.querySelector('label');
                                if (label && label.textContent.trim().toLowerCase() ===
                                    'type') {
                                    group.style.display = 'none';
                                }
                            });
                        }
                        if (typeInput && typeInput.value === 'submit') {
                            console.log('editing submit button')
                        }
                    }, 50);
                },
                onAddField: function(_, fieldData) {
                    const errorMsg = document.getElementById('form-error-msg');
                    // Delay logic to allow field to be fully rendered and saved
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
                            //console.log(captchaFields.length);
                            if (captchaFields.length > 1) {
                                // ✅ Immediately hide captcha field in toolbox
                                document.querySelectorAll('.frmb-control li').forEach(function(
                                    li) {
                                    //console.log('looping')
                                    if (li.dataset.type === 'captcha') {
                                        //console.log('detected');
                                        li.style.display = 'none';
                                    }
                                });

                                if (errorMsg) {
                                    hasCaptchaError = true; // 🚫 Set flag to true
                                    setTimeout(() => {
                                        errorMsg.textContent =
                                            'Heads Up! Only one Captcha field is ideal.Please keep only one and delete the rest.';
                                        errorMsg.style.display = 'block';
                                        errorMsg.scrollIntoView({
                                            behavior: 'smooth',
                                            block: 'center'
                                        });

                                    }, 10);
                                }

                            } else {
                                hasCaptchaError = false; // ✅ Reset if only one captcha exists
                                if (errorMsg) {
                                    errorMsg.textContent = '';
                                    errorMsg.style.display = 'none';
                                }
                            }
                        }
                        const stageWrap = document.querySelector(
                            '.form-wrap.form-builder .stage-wrap');
                        if (stageWrap) {
                            stageWrap.style.border = '3px dashed #ccc';
                        }
                    }, 100); // Delay lets FormBuilder finalize the field rendering
                },
                onAddFieldAfter: function(fieldId, fieldData) {
                    // Check if field is of type 'button' and subtype is 'submit'
                    if (
                        fieldData.type === 'button' &&
                        fieldData.subtype === 'submit'
                    ) {
                        // Hide/remove delete button for this field
                        const fieldWrapper = document.getElementById(fieldId);
                        if (fieldWrapper) {
                            const deleteButton = fieldWrapper.querySelector('.del-button');
                            if (deleteButton) {
                                deleteButton.remove(); // Or use .style.display = 'none' to just hide
                            }
                        }
                    }
                }

            });

            // Listen to the form submit event
            const form = document.getElementById('form-builder-form');
            form.addEventListener('submit',
                function(e) {
                    e.preventDefault();

                    const titleInput = form.querySelector('input[name="title"]');
                    const jsonField = document.getElementById('form-json');
                    const formData = fbEditor.actions.getData('json');
                    const parsedData = JSON.parse(formData);
                    const errorMsg = document.getElementById('form-error-msg');

                    // Clear previous error styling
                    titleInput.style.border = '';
                    const emptyStage = document.querySelector('.form-wrap.form-builder .stage-wrap.empty');
                    if (emptyStage) {
                        emptyStage.style.border = '';
                    }

                    // ✅ Check title
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

                    // ✅ Check if at least one field is added
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

                    // ✅ All good — submit
                    errorMsg.style.display = 'none';
                    jsonField.value = formData;
                    form.submit();
                });
        });
    </script>
@endpush
