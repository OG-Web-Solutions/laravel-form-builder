<?php

namespace Ogwebsolutions\FormBuilder;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class FormBuilderServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load package routes
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/views', 'ogformbuilder');

        // Publish config files
        $this->publishes([
            __DIR__ . '/config/ogformbuilder.php' => config_path('ogformbuilder.php'),
            __DIR__ . '/config/hcaptcha.php'   => config_path('hcaptcha.php'),
            __DIR__ . '/config/recaptcha.php'  => config_path('recaptcha.php'),
        ], 'ogformbuilder-config');

        // Publish views
        $this->publishes([
            __DIR__ . '/Views/form' => resource_path('views/vendor/ogformbuilder/form'),
        ], 'ogformbuilder-views');


        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        // Blade directive
        Blade::directive('ogRenderForm', function ($id) {
            return "<?php echo view('ogformbuilder::form.render', ['form' => \\Ogwebsolutions\\FormBuilder\\Models\\OgForm::whereId($id)->where('status', 'active')->first()])->render(); ?>";
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
