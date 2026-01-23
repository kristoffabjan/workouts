<?php

use App\Filament\Admin\Pages\ManageApplicationSettings;
use App\Models\User;
use App\Settings\ApplicationSettings;
use Filament\Facades\Filament;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->nonAdmin = User::factory()->create(['is_admin' => false]);

    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

describe('ApplicationSettings Page Access', function () {
    it('allows admin users to access the settings page', function () {
        actingAs($this->admin)
            ->get(ManageApplicationSettings::getUrl(panel: 'admin'))
            ->assertSuccessful();
    });

    it('denies non-admin users access to the settings page', function () {
        actingAs($this->nonAdmin)
            ->get(ManageApplicationSettings::getUrl(panel: 'admin'))
            ->assertForbidden();
    });

    it('denies unauthenticated users access to the settings page', function () {
        $this->get(ManageApplicationSettings::getUrl(panel: 'admin'))
            ->assertRedirect();
    });
});

describe('ApplicationSettings Form', function () {
    it('loads current settings into the form', function () {
        $settings = app(ApplicationSettings::class);

        Livewire::actingAs($this->admin)
            ->test(ManageApplicationSettings::class)
            ->assertFormSet([
                'application_name' => $settings->application_name,
                'default_language' => $settings->default_language,
                'timezone' => $settings->timezone,
            ]);
    });

    it('allows admin to update application name', function () {
        Livewire::actingAs($this->admin)
            ->test(ManageApplicationSettings::class)
            ->fillForm([
                'application_name' => 'New App Name',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $settings = app(ApplicationSettings::class);
        expect($settings->application_name)->toBe('New App Name');
    });

    it('allows admin to update default language', function () {
        Livewire::actingAs($this->admin)
            ->test(ManageApplicationSettings::class)
            ->fillForm([
                'default_language' => 'sl',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $settings = app(ApplicationSettings::class);
        expect($settings->default_language)->toBe('sl');
    });

    it('allows admin to update timezone', function () {
        Livewire::actingAs($this->admin)
            ->test(ManageApplicationSettings::class)
            ->fillForm([
                'timezone' => 'Europe/Ljubljana',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $settings = app(ApplicationSettings::class);
        expect($settings->timezone)->toBe('Europe/Ljubljana');
    });

    it('requires application name to be set', function () {
        Livewire::actingAs($this->admin)
            ->test(ManageApplicationSettings::class)
            ->fillForm([
                'application_name' => '',
            ])
            ->call('save')
            ->assertHasFormErrors(['application_name' => 'required']);
    });

    it('requires default language to be set', function () {
        Livewire::actingAs($this->admin)
            ->test(ManageApplicationSettings::class)
            ->fillForm([
                'default_language' => '',
            ])
            ->call('save')
            ->assertHasFormErrors(['default_language' => 'required']);
    });

    it('requires timezone to be set', function () {
        Livewire::actingAs($this->admin)
            ->test(ManageApplicationSettings::class)
            ->fillForm([
                'timezone' => '',
            ])
            ->call('save')
            ->assertHasFormErrors(['timezone' => 'required']);
    });
});
