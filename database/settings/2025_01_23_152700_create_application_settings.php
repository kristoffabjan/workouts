<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('application.application_name', config('app.name', 'Workouts'));
        $this->migrator->add('application.application_logo', null);
        $this->migrator->add('application.default_language', 'en');
        $this->migrator->add('application.timezone', config('app.timezone', 'UTC'));
    }

    public function down(): void
    {
        $this->migrator->delete('application.application_name');
        $this->migrator->delete('application.application_logo');
        $this->migrator->delete('application.default_language');
        $this->migrator->delete('application.timezone');
    }
};
