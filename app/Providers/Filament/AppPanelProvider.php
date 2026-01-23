<?php

namespace App\Providers\Filament;

use App\Filament\App\Pages\Tenancy\RegisterTeam;
use App\Filament\App\Pages\UserSettings;
use App\Helpers\SettingsHelper;
use App\Models\Team;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Enums\ThemeMode;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('app')
            ->path('app')
            ->login()
            ->brandName(fn (): string => SettingsHelper::getApplicationName())
            ->brandLogo(fn (): ?string => ($logo = SettingsHelper::getApplicationLogo()) ? Storage::url($logo) : null)
            ->brandLogoHeight('2rem')
            ->colors([
                'primary' => Color::hex('#E5A823'),
                'danger' => Color::hex('#FA6868'),
                'warning' => Color::hex('#FAAC68'),
                'info' => Color::hex('#FACE68'),
                'success' => Color::hex('#5AB58A'),
            ])
            ->defaultThemeMode(ThemeMode::Dark)
            ->databaseNotifications()
            ->font("Space Grotesk")
            ->tenant(Team::class, slugAttribute: 'slug')
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\Filament\App\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\Filament\App\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\Filament\App\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->tenantRegistration(RegisterTeam::class)
            ->searchableTenantMenu()
            ->tenantMenuItems([
                'register' => Action::make('register_team')
                    ->label('Register new team')
                    ->icon('heroicon-o-plus-circle')
                    ->url(fn (): string => route('filament.app.tenant.registration')),
            ])
            ->userMenuItems([
                Action::make('user_settings')
                    ->label(__('settings.user.navigation_label'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url(fn (): string => UserSettings::getUrl()),
            ])
            ->viteTheme('resources/css/filament/app/theme.css')
            ->plugin(
                FilamentFullCalendarPlugin::make()
                    ->selectable(false)
                    ->editable(false)
                    ->timezone(SettingsHelper::getTimezone())
                    ->locale(SettingsHelper::getDefaultLanguage())
            );
    }
}
