<?php

namespace Ogwebsolutions\FormBuilder;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Ogwebsolutions\FormBuilder\Models\OgForm;

class FormBuilderServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load package routes
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/views', 'formbuilder');

        // Publish main config
        $this->publishes([
            __DIR__ . '/config/ogformbuilder.php' => config_path('ogformbuilder.php'),
        ], 'formbuilder-config');

        // ✅ Publish CAPTCHA config files
        $this->publishes([
            __DIR__ . '/config/hcaptcha.php'   => config_path('hcaptcha.php'),
            __DIR__ . '/config/recaptcha.php'  => config_path('recaptcha.php'),
        ], 'formbuilder-captcha');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        // Publishes public assets
        $this->publishes([
            __DIR__ . '/public' => public_path('vendor/formbuilder'),
        ], 'public');

        // Blade directive
        Blade::directive('ogRenderForm', function ($id) {
            return "<?php echo view('formbuilder::form.render', ['form' => \\Ogwebsolutions\\FormBuilder\\Models\\OgForm::whereId($id)->where('status', 'active')->first()])->render(); ?>";
        });
    }


    public function register()
    {
        // Merge default config
        $this->mergeConfigFrom(__DIR__ . '/config/ogformbuilder.php', 'ogformbuilder');

        // Merge captcha configs
        $this->mergeConfigFrom(__DIR__ . '/config/hcaptcha.php', 'hcaptcha');
        $this->mergeConfigFrom(__DIR__ . '/config/recaptcha.php', 'recaptcha');
    }
}
