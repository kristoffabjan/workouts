<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ApplicationSettings extends Settings
{
    public string $application_name;

    public ?string $application_logo;

    public string $default_language;

    public string $timezone;

    public static function group(): string
    {
        return 'application';
    }
}
