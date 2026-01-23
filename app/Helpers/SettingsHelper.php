<?php

namespace App\Helpers;

use App\Settings\ApplicationSettings;
use Illuminate\Support\Facades\Schema;

class SettingsHelper
{
    public static ?ApplicationSettings $cachedSettings = null;

    public static function getApplicationSettings(): ?ApplicationSettings
    {
        if (static::$cachedSettings !== null) {
            return static::$cachedSettings;
        }

        try {
            if (Schema::hasTable('settings')) {
                static::$cachedSettings = app(ApplicationSettings::class);

                return static::$cachedSettings;
            }
        } catch (\Throwable) {
        }

        return null;
    }

    public static function getApplicationName(): string
    {
        $settings = static::getApplicationSettings();

        return $settings?->application_name ?? config('app.name', 'Workouts');
    }

    public static function getApplicationLogo(): ?string
    {
        $settings = static::getApplicationSettings();

        return $settings?->application_logo;
    }

    public static function getDefaultLanguage(): string
    {
        $settings = static::getApplicationSettings();

        return $settings?->default_language ?? config('app.locale', 'en');
    }

    public static function getTimezone(): string
    {
        $settings = static::getApplicationSettings();

        return $settings?->timezone ?? config('app.timezone', 'UTC');
    }
}
