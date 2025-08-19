
    @if($field['subtype'] === 'recaptcha')
        <div class="form-group captcha-wrap captcha-wrap-recaptcha captcha-wrap-{{ $form['id'] }}">
            <div class="g-recaptcha" data-sitekey="{{ config('recaptcha.sitekey') }}"></div>
        </div>
    @elseif($field['subtype'] === 'hcaptcha')
        <div class="form-group captcha-wrap captcha-wrap-hcaptcha captcha-wrap-{{ $form['id'] }}">
            <div class="h-captcha" data-sitekey="{{ config('hcaptcha.sitekey') }}" data-theme="dark"></div>
        </div>
    @endif

