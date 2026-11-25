<?php

namespace Xenio\LivewireForms;

use Xenio\LivewireForms\Console\MakeFormCommand;
use Xenio\LivewireForms\Http\Controllers\SearchOptionsController;
use Xenio\LivewireForms\Http\Controllers\ValidateStepController;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class LivewireFormsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/livewire-forms.php', 'livewire-forms');
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'forms');

        if (class_exists(Blade::class) && method_exists(Blade::class, 'anonymousComponentPath')) {
            Blade::anonymousComponentPath(__DIR__.'/../resources/views', 'forms');
        }

        Route::middleware(config('livewire-forms.search.middleware', ['web', 'throttle:60,1']))
            ->post(config('livewire-forms.search.uri', '_livewire-forms/search'), SearchOptionsController::class)
            ->name('livewire-forms.search');

        Route::middleware(config('livewire-forms.wizard.middleware', ['web', 'throttle:60,1']))
            ->post(config('livewire-forms.wizard.uri', '_livewire-forms/validate-step'), ValidateStepController::class)
            ->name('livewire-forms.validate-step');

        if ($this->app->runningInConsole()) {
            $this->commands([MakeFormCommand::class]);

            $this->publishes([
                __DIR__ . '/../config/livewire-forms.php' => config_path('livewire-forms.php'),
            ], 'livewire-forms-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/forms'),
            ], 'forms-views');
        }
    }
}
