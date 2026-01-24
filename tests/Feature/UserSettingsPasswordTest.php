<?php

use App\Enums\TeamRole;
use App\Filament\App\Pages\UserSettings;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

beforeEach(function () {
    $this->team = Team::factory()->create();

    $this->user = User::factory()->create([
        'password' => Hash::make('current-password'),
        'settings' => null,
    ]);
    $this->user->teams()->attach($this->team, ['role' => TeamRole::Coach->value]);

    Filament::setCurrentPanel(Filament::getPanel('app'));
});

describe('Password Change in UserSettings', function () {
    it('shows password change form in user settings', function () {
        $this->actingAs($this->user);
        Filament::setTenant($this->team);

        Livewire::test(UserSettings::class)
            ->assertSee('Security')
            ->assertSee('Current Password')
            ->assertSee('New Password')
            ->assertSee('Confirm Password');
    });

    it('validates current password is required when new password provided', function () {
        $this->actingAs($this->user);
        Filament::setTenant($this->team);

        Livewire::test(UserSettings::class)
            ->fillForm([
                'current_password' => '',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])
            ->call('save')
            ->assertHasFormErrors(['current_password' => 'required_with']);
    });

    it('validates current password is correct', function () {
        $this->actingAs($this->user);
        Filament::setTenant($this->team);

        Livewire::test(UserSettings::class)
            ->fillForm([
                'current_password' => 'wrong-password',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])
            ->call('save')
            ->assertHasFormErrors(['current_password']);
    });

    it('validates new password confirmation matches', function () {
        $this->actingAs($this->user);
        Filament::setTenant($this->team);

        Livewire::test(UserSettings::class)
            ->fillForm([
                'current_password' => 'current-password',
                'password' => 'new-password-123',
                'password_confirmation' => 'different-password',
            ])
            ->call('save')
            ->assertHasFormErrors(['password']);
    });

    it('can change password with valid data', function () {
        $this->actingAs($this->user);
        Filament::setTenant($this->team);

        Livewire::test(UserSettings::class)
            ->fillForm([
                'current_password' => 'current-password',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $this->user->refresh();
        expect(Hash::check('new-password-123', $this->user->password))->toBeTrue();
    });

    it('clears password fields after successful change', function () {
        $this->actingAs($this->user);
        Filament::setTenant($this->team);

        Livewire::test(UserSettings::class)
            ->fillForm([
                'current_password' => 'current-password',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])
            ->call('save')
            ->assertFormSet([
                'current_password' => null,
                'password' => null,
                'password_confirmation' => null,
            ]);
    });

    it('can save settings without changing password', function () {
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
        expect(Hash::check('current-password', $this->user->password))->toBeTrue();
    });
});
