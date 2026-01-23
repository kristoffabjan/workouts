<?php

use App\Enums\TeamRole;
use App\Filament\App\Pages\TeamSettings;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->team = Team::factory()->create([
        'name' => 'Test Team',
        'settings' => [
            'logo' => null,
            'default_reminder_time' => '09:00',
        ],
    ]);

    $this->coach = User::factory()->create();
    $this->coach->teams()->attach($this->team, ['role' => TeamRole::Coach->value]);
    $this->team->update(['owner_id' => $this->coach->id]);

    $this->client = User::factory()->create();
    $this->client->teams()->attach($this->team, ['role' => TeamRole::Client->value]);

    Filament::setCurrentPanel(Filament::getPanel('app'));
});

describe('TeamSettings Page Access', function () {
    it('allows coaches to access the team settings page', function () {
        actingAs($this->coach)
            ->get(TeamSettings::getUrl(tenant: $this->team))
            ->assertSuccessful();
    });

    it('allows clients to access the team settings page', function () {
        actingAs($this->client)
            ->get(TeamSettings::getUrl(tenant: $this->team))
            ->assertSuccessful();
    });
});

describe('TeamSettings Form', function () {
    it('loads current team settings into the form', function () {
        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(TeamSettings::class)
            ->assertFormSet([
                'name' => $this->team->name,
                'default_reminder_time' => '09:00',
            ]);
    });

    it('allows owner to update team name', function () {
        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(TeamSettings::class)
            ->fillForm([
                'name' => 'Updated Team Name',
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $this->team->refresh();
        expect($this->team->name)->toBe('Updated Team Name');
        expect($this->team->slug)->toBe('updated-team-name');
    });

    it('allows owner to update default reminder time', function () {
        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(TeamSettings::class)
            ->fillForm([
                'default_reminder_time' => '08:30',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->team->refresh();
        expect($this->team->settings['default_reminder_time'])->toBe('08:30');
    });

    it('prevents client from saving team settings', function () {
        $this->actingAs($this->client);
        Filament::setTenant($this->team);

        Livewire::test(TeamSettings::class)
            ->call('save')
            ->assertNotified();

        $this->team->refresh();
        expect($this->team->name)->toBe('Test Team');
    });

    it('preserves existing settings when updating', function () {
        $this->team->update([
            'settings' => [
                'logo' => null,
                'default_reminder_time' => '10:00',
                'custom_setting' => 'value',
            ],
        ]);

        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(TeamSettings::class)
            ->fillForm([
                'name' => $this->team->name,
                'logo' => null,
                'default_reminder_time' => '11:00',
            ])
            ->call('save');

        $this->team->refresh();
        expect($this->team->settings['default_reminder_time'])->toBe('11:00');
        expect($this->team->settings['custom_setting'])->toBe('value');
    });
});

describe('TeamSettings Permissions', function () {
    it('allows coach (owner) to edit settings', function () {
        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        $component = Livewire::test(TeamSettings::class);

        expect($component->instance()->canEditSettings())->toBeTrue();
    });

    it('allows non-owner coach to edit settings', function () {
        $anotherCoach = User::factory()->create();
        $anotherCoach->teams()->attach($this->team, ['role' => TeamRole::Coach->value]);

        $this->actingAs($anotherCoach);
        Filament::setTenant($this->team);

        $component = Livewire::test(TeamSettings::class);

        expect($component->instance()->canEditSettings())->toBeTrue();
    });

    it('prevents client from editing settings', function () {
        $this->actingAs($this->client);
        Filament::setTenant($this->team);

        $component = Livewire::test(TeamSettings::class);

        expect($component->instance()->canEditSettings())->toBeFalse();
    });
});
