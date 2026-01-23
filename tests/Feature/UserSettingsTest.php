<?php

use App\Enums\TeamRole;
use App\Enums\WeightUnit;
use App\Filament\App\Pages\UserSettings;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->team = Team::factory()->create();

    $this->user = User::factory()->create([
        'settings' => null,
    ]);
    $this->user->teams()->attach($this->team, ['role' => TeamRole::Coach->value]);

    Filament::setCurrentPanel(Filament::getPanel('app'));
});

describe('UserSettings Page Access', function () {
    it('allows authenticated user to access user settings page', function () {
        actingAs($this->user)
            ->get(UserSettings::getUrl(tenant: $this->team))
            ->assertSuccessful();
    });

    it('redirects unauthenticated user', function () {
        $this->get(UserSettings::getUrl(tenant: $this->team))
            ->assertRedirect();
    });
});

describe('UserSettings Form', function () {
    it('loads current user settings into the form', function () {
        $this->user->update([
            'settings' => [
                'preferred_language' => 'sl',
                'weight_unit' => WeightUnit::Lb->value,
            ],
        ]);

        $this->actingAs($this->user);
        Filament::setTenant($this->team);

        Livewire::test(UserSettings::class)
            ->assertFormSet([
                'preferred_language' => 'sl',
                'weight_unit' => WeightUnit::Lb,
            ]);
    });

    it('loads default values when user has no settings', function () {
        $this->actingAs($this->user);
        Filament::setTenant($this->team);

        Livewire::test(UserSettings::class)
            ->assertFormSet([
                'preferred_language' => null,
                'weight_unit' => WeightUnit::Kg,
            ]);
    });

    it('allows user to update preferred language', function () {
        $this->actingAs($this->user);
        Filament::setTenant($this->team);

        Livewire::test(UserSettings::class)
            ->fillForm([
                'preferred_language' => 'sl',
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $this->user->refresh();
        expect($this->user->getPreferredLanguage())->toBe('sl');
    });

    it('allows user to update weight unit', function () {
        $this->actingAs($this->user);
        Filament::setTenant($this->team);

        Livewire::test(UserSettings::class)
            ->fillForm([
                'weight_unit' => WeightUnit::Lb->value,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->user->refresh();
        expect($this->user->getWeightUnit())->toBe(WeightUnit::Lb);
    });

    it('stores null for system default language', function () {
        $this->user->update([
            'settings' => ['preferred_language' => 'sl'],
        ]);

        $this->actingAs($this->user);
        Filament::setTenant($this->team);

        Livewire::test(UserSettings::class)
            ->fillForm([
                'preferred_language' => '',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->user->refresh();
        expect($this->user->getPreferredLanguage())->toBeNull();
    });
});

describe('User Settings Helper Methods', function () {
    it('returns default weight unit when no setting exists', function () {
        expect($this->user->getWeightUnit())->toBe(WeightUnit::Kg);
    });

    it('returns correct weight unit from settings', function () {
        $this->user->update([
            'settings' => ['weight_unit' => WeightUnit::Lb->value],
        ]);

        expect($this->user->getWeightUnit())->toBe(WeightUnit::Lb);
    });

    it('returns null for preferred language when not set', function () {
        expect($this->user->getPreferredLanguage())->toBeNull();
    });

    it('returns correct preferred language from settings', function () {
        $this->user->update([
            'settings' => ['preferred_language' => 'sl'],
        ]);

        expect($this->user->getPreferredLanguage())->toBe('sl');
    });

    it('returns null for avatar when not set', function () {
        expect($this->user->getAvatar())->toBeNull();
    });

    it('returns correct avatar path from settings', function () {
        $this->user->update([
            'settings' => ['avatar' => 'user-avatars/test.jpg'],
        ]);

        expect($this->user->getAvatar())->toBe('user-avatars/test.jpg');
    });

    it('merges new settings with existing settings', function () {
        $this->user->update([
            'settings' => ['preferred_language' => 'en'],
        ]);

        $this->user->updateSettings(['weight_unit' => WeightUnit::Lb->value]);

        $this->user->refresh();
        expect($this->user->settings)->toBe([
            'preferred_language' => 'en',
            'weight_unit' => WeightUnit::Lb->value,
        ]);
    });
});

describe('WeightUnit Enum', function () {
    it('has correct values', function () {
        expect(WeightUnit::Kg->value)->toBe('kg');
        expect(WeightUnit::Lb->value)->toBe('lb');
    });

    it('has labels via HasLabel interface', function () {
        expect(WeightUnit::Kg->getLabel())->not->toBeNull();
        expect(WeightUnit::Lb->getLabel())->not->toBeNull();
    });
});
