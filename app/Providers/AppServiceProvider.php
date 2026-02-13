<?php

namespace App\Providers;

use BezhanSalleh\LanguageSwitch\Enums\Placement;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use BezhanSalleh\PanelSwitch\PanelSwitch;
use Carbon\CarbonImmutable;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::anonymousComponentPath(resource_path('views/layouts'), 'layouts');

        $this->configureDefaults();
        $this->configureFilament();
        $this->configureLanguageSwitch();
        $this->configurePanelSwitch();
    }

    protected function configureLanguageSwitch(): void
    {
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['en', 'sl'])
                ->labels([
                    'en' => 'English',
                    'sl' => 'Slovenščina',
                ])
                ->circular()
                ->visible(insidePanels: true, outsidePanels: true)
                ->outsidePanelRoutes([
                    'filament.app.auth.login',
                    'filament.admin.auth.login',
                    'filament.app.auth.password-reset.request',
                    'filament.admin.auth.password-reset.request',
                ])
                ->outsidePanelPlacement(Placement::TopRight);
        });
    }

    protected function configurePanelSwitch(): void
    {
        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
            $panelSwitch
                ->labels([
                    'app' => 'App',
                    'admin' => 'Admin',
                ])
                ->modalHeading('Panels')
                ->renderHook('panels::global-search.after')
                ->simple();
        });
    }

    protected function configureFilament(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
            fn(): View => view('filament.auth.login-footer'),
        );
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(
            fn(): ?Password => app()->isProduction()
                ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
                : null
        );
    }
}
