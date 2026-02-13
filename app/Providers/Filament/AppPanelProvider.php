<?php

namespace App\Providers\Filament;

use App\Filament\App\Pages\Tenancy\RegisterTeam;
use App\Filament\App\Pages\UserSettings;
use App\Helpers\SettingsHelper;
use App\Models\Team;
use Filament\Actions\Action;
use Filament\Enums\ThemeMode;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
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
            ->passwordReset()
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
            ->font('Space Grotesk')
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
            ->userMenuItems([
                Action::make('user_settings')
                    ->label(__('settings.user.navigation_label'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->hidden(fn (): bool => Filament::getTenant() === null) // hide if no tenant is active
                    ->url(fn (): string => Filament::getTenant() ? UserSettings::getUrl(): '#'),
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
