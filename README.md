# Laravel Drag & Drop Contact Form Builder

A simple and customizable Drag & Drop Form Builder package for Laravel, developed by [OG Web Solutions](https://www.ogwebsolutions.com/). Build and manage forms with advanced settings, email placeholders, CAPTCHA support (reCAPTCHA & hCaptcha), paginated searchable submissions, CSV export, and fully extendable architecture with flexible layout support.

---

## 📦 Installation

1. **Install via Composer**

```bash
composer require ogwebsolutions/laravel-form-builder

2. Publish Config

php artisan vendor:publish --tag="ogformbuilder-config"

This will publish:

Configuration files (ogformbuilder.php, recaptcha.php, hcaptcha.php)

3. Run Migrations

php artisan migrate

🧩 Features

Drag & Drop UI for form creation
Supports multiple field types (text, select, checkbox, file, etc.)
CAPTCHA support: hCaptcha & reCAPTCHA
Submissions dashboard
Custom Blade layout support
Live form rendering
Field-level validation
Form submission tracking


🛠 Configuration

**Custom Layout Customization**

To override the default layout, update the config file:

// config/ogformbuilder.php

return [
    'layout' => 'layouts.app', // Your custom layout
];

You can also customize the url, route name and middleware in the config file.

Ensure your layout includes:

@yield('content')
@stack('scripts')

**CAPTCHA Setup**

Configure keys in .env:

RECAPTCHA_SITE_KEY=your-site-key
RECAPTCHA_SECRET_KEY=your-secret-key

HCAPTCHA_SITE_KEY=your-hcaptcha-site-key
HCAPTCHA_SECRET_KEY=your-hcaptcha-secret-key

You can switch between CAPTCHA types in form settings.

🚀 Usage

**Form Creation**
**Create a Form**
Go to /formbuilder
Click “Create New Form”
Drag fields, configure settings, save
**Manage Forms**
View all forms at /formbuilder
Edit or delete forms
**Form Settings**
Customize form fields, validation, CAPTCHA

Embed a Form

To render the form on your frontend: @ogRenderForm($formId)
Example: Use @ogRenderForm(1) to render form with ID 1.

🧱 Extending the Package

**Override Views**
To override views:
php artisan vendor:publish --tag=ogformbuilder-views
You can now customize views under:
resources/views/vendor/ogformbuilder/

**Listen to Events (Coming Soon)**
Events like FormSubmitted, FormSaved, etc. will be available to hook into.

🤝 Contributing

Pull requests are welcome. For major changes, please open an issue first.

🧾 License

This package is open-source software licensed under the MIT license.

📬 Support

For support, please email support@ogwebsolutions.com


