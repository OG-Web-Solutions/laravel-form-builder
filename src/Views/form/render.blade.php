@if ($form)
@php
    $hasRecaptcha = collect($form->fields)->contains(fn($f) => ($f['type'] ?? '') === 'captcha' && ($f['subtype'] ?? '') === 'recaptcha');
    $hasHcaptcha  = collect($form->fields)->contains(fn($f) => ($f['type'] ?? '') === 'captcha' && ($f['subtype'] ?? '') === 'hcaptcha');
@endphp
    <form action="{{ route('formbuilder.form.store', $form) }}" method="POST" enctype="multipart/form-data"
        class="og-builder-form" data-form="{{ $form->id }}">
        @csrf

        @foreach ($form->fields as $field)
            @include('ogformbuilder::form.fields.' . $field['type'], ['field' => $field])
        @endforeach

    </form>
    <div id="form-{{ $form->id }}-alert" class="form-{{ $form->id }}-alert" style="display: none;">
        <div class="form-{{ $form->id }}-alert-message"></div>
        <button type="button" class="form-{{ $form->id }}-alert-close"
            onclick="document.getElementById('form-{{ $form->id }}-alert').style.display='none'">&times;</button>
    </div>
<style>
    /* Base button styling (optional improvement) */
    button {
        position: relative;
        overflow: hidden;
    }

    /* Hide spinner by default */
    button .spinner {
        display: none;
        width: 1rem;
        height: 1rem;
        border: 2px solid #fff;
        border-top-color: transparent;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    /* Show spinner when button is loading */
    button.loading {
        pointer-events: none;
        opacity: 0.7;
    }

    button.loading .btn-text {
        visibility: hidden;
    }

    button.loading .spinner {
        display: inline-block;
    }

    @keyframes spin {
        to {
            transform: translate(-50%, -50%) rotate(360deg);
        }
    }

    .error {
        color: red;
        font-size: 0.9rem;
        margin-top: 4px;
        display: block;
    }

    .is-invalid {
        border-color: red;
    }

    /* Base Alert */
    .form-{{ $form->id }}-alert {
        position: fixed;
        top: 20px;
        right: 20px;
        max-width: 320px;
        padding: 15px 20px;
        background-color: #f0fdf4;
        color: #166534;
        border-left: 5px solid #22c55e;
        border-radius: 6px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        font-family: system-ui, sans-serif;
        font-size: 15px;
        line-height: 1.4;
        z-index: 9999;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        animation: fadeSlideIn 0.4s ease-out;
    }

    /* Error Variant */
    .form-{{ $form->id }}-alert.error {
        background-color: #fef2f2;
        color: #991b1b;
        border-left-color: #ef4444;
    }

    /* Alert Message Text */
    .form-{{ $form->id }}-alert-message {
        flex: 1;
    }

    /* Close Button */
    .form-{{ $form->id }}-alert-close {
        background: none;
        border: none;
        font-size: 18px;
        font-weight: bold;
        line-height: 1;
        cursor: pointer;
        padding: 0;
        color: inherit;
        opacity: 0.7;
        transition: opacity 0.2s ease;
    }

    .form-{{ $form->id }}-alert-close:hover {
        opacity: 1;
    }

    /* Entry animation */
    @keyframes fadeSlideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
{{-- Inline CAPTCHA scripts (no @push used) --}}
@if ($hasRecaptcha)
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif

@if ($hasHcaptcha)
    <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
@endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form Submission
            const form = document.querySelector('.og-builder-form[data-form="{{ $form->id }}"]');

            if (!form) return;

            const submitBtn = form.querySelector(`.og-builder-form-{{ $form->id }}-btn`);

            form.addEventListener('submit', function(event) {
                event.preventDefault();

                // Clear previous error messages
                form.querySelectorAll('.error').forEach(errorSpan => errorSpan.remove());
                const invalidInputs = form.querySelectorAll('.is-invalid');
                invalidInputs.forEach(el => el.classList.remove('is-invalid'));


                // Disable submit button and show loader
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('loading');
                }

                const formData = new FormData(form);

                fetch(form.action, {
                        method: form.method,
                        body: formData
                    })
                    .then(function(response) {
                        if (response.ok) {
                            form.reset();
                            response.json().then(function(data) {

                                showFormAlert(data.message || 'Form submitted successfully.',
                                    false);

                                if (document.querySelector('.h-captcha') && window.hcaptcha) {
                                    hcaptcha.reset();
                                }

                                if (document.querySelector('.g-recaptcha') && window
                                    .grecaptcha) {
                                    grecaptcha.reset();
                                }

                                if (data.redirectUrl) {
                                    setTimeout(() => window.location.href = data.redirectUrl,
                                        6000);
                                }
                            });
                        } else {
                            response.json().then(function(data) {
                                if (data.errors) {
                                    Object.keys(data.errors).forEach(function(key) {
                                        const errorSpan = document.createElement(
                                            'span');
                                        errorSpan.className = 'error';
                                        errorSpan.textContent = data.errors[key][0];
                                        const inputElement = form.querySelector(
                                            `[name="${key}"], [name="${key}[]"]`);
                                        if (inputElement) {
                                            inputElement.classList.add('is-invalid');
                                            const formGroup = inputElement.closest(
                                                '.form-group');
                                            if (formGroup && !formGroup.querySelector(
                                                    '.error')) {
                                                formGroup.appendChild(errorSpan);
                                            }
                                        }
                                    });
                                    const firstError = form.querySelector('.is-invalid');
                                    if (firstError) {
                                        firstError.scrollIntoView({
                                            behavior: 'smooth',
                                            block: 'center'
                                        });
                                        firstError.focus();
                                    }

                                } else {
                                    showFormAlert(data.message ||
                                        'An unexpected error occurred.', true);
                                }
                            });
                        }
                    })
                    .catch(function() {
                        showFormAlert(data.message || 'An unexpected error occurred.', true);
                        if (document.querySelector('.h-captcha') && window.hcaptcha) {
                            hcaptcha.reset();
                        }
                        if (document.querySelector('.g-recaptcha') && window.grecaptcha) {
                            grecaptcha.reset();
                        }

                    })
                    .finally(function() {
                        // Re-enable button and hide loader
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('loading');
                        }
                        if (document.querySelector('.h-captcha') && window.hcaptcha) {
                            console.log('hCaptcha reset');
                            hcaptcha.reset();
                        }
                        if (document.querySelector('.g-recaptcha') && window.grecaptcha) {
                            console.log('reCaptcha reset');
                            grecaptcha.reset();
                        }

                    });
            });

            function showFormAlert(message, isError = false) {
                const alertBox = document.getElementById('form-{{ $form->id }}-alert');
                alertBox.classList.toggle('error', isError);
                alertBox.querySelector('.form-{{ $form->id }}-alert-message').innerHTML = message;
                alertBox.style.display = 'block';
                setTimeout(() => {
                    alertBox.style.display = 'none';
                }, 5000);
            }
        });
    </script>
@else
    <p>Form not found.</p>
@endif
