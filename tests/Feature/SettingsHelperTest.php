<?php

use App\Helpers\SettingsHelper;
use App\Settings\ApplicationSettings;

describe('SettingsHelper', function () {
    it('returns application name from settings', function () {
        $settings = app(ApplicationSettings::class);
        $settings->application_name = 'Test Application';
        $settings->save();

        SettingsHelper::$cachedSettings = null;

        expect(SettingsHelper::getApplicationName())->toBe('Test Application');
    });

    it('returns default language from settings', function () {
        $settings = app(ApplicationSettings::class);
        $settings->default_language = 'sl';
        $settings->save();

        SettingsHelper::$cachedSettings = null;

        expect(SettingsHelper::getDefaultLanguage())->toBe('sl');
    });

    it('returns timezone from settings', function () {
        $settings = app(ApplicationSettings::class);
        $settings->timezone = 'Europe/Ljubljana';
        $settings->save();

        SettingsHelper::$cachedSettings = null;

        expect(SettingsHelper::getTimezone())->toBe('Europe/Ljubljana');
    });

    it('returns application logo when set', function () {
        $settings = app(ApplicationSettings::class);
        $settings->application_logo = 'logos/test-logo.png';
        $settings->save();

        SettingsHelper::$cachedSettings = null;

        expect(SettingsHelper::getApplicationLogo())->toBe('logos/test-logo.png');
    });

    it('returns null for logo when not set', function () {
        $settings = app(ApplicationSettings::class);
        $settings->application_logo = null;
        $settings->save();

        SettingsHelper::$cachedSettings = null;

        expect(SettingsHelper::getApplicationLogo())->toBeNull();
    });

    it('caches settings after first access', function () {
        SettingsHelper::$cachedSettings = null;

        SettingsHelper::getApplicationName();

        expect(SettingsHelper::$cachedSettings)->not->toBeNull();
    });
});
