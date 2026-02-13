<?php

namespace App\Filament\App\Pages;

use App\Enums\WeightUnit;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.app.pages.user-settings';

    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('settings.user.navigation_label');
    }

    public function getTitle(): string
    {
        return __('settings.user.title');
    }

    public function mount(): void
    {
        $user = Auth::user();
        $settings = $user->settings ?? [];
        $avatar = $settings['avatar'] ?? null;

        $this->form->fill([
            'avatar' => $avatar ? [$avatar] : [],
            'preferred_language' => $settings['preferred_language'] ?? null,
            'weight_unit' => $settings['weight_unit'] ?? WeightUnit::Kg->value,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('settings.user.title'))
                    ->description(__('settings.user.subheading'))
                    ->schema([
                        FileUpload::make('avatar')
                            ->label(__('settings.user.fields.avatar'))
                            ->image()
                            ->avatar()
                            ->disk('public')
                            ->directory('user-avatars')
                            ->visibility('public')
                            ->automaticallyResizeImagesMode('cover')
                            ->imageAspectRatio('1:1')
                            ->automaticallyCropImagesToAspectRatio()
                            ->automaticallyResizeImagesToWidth(200)
                            ->automaticallyResizeImagesToHeight(200),

                        Select::make('preferred_language')
                            ->label(__('settings.user.fields.preferred_language'))
                            ->options([
                                '' => __('settings.user.language_options.system'),
                                'en' => __('settings.user.language_options.en'),
                                'sl' => __('settings.user.language_options.sl'),
                            ])
                            ->placeholder(__('settings.user.language_options.system')),

                        Select::make('weight_unit')
                            ->label(__('settings.user.fields.weight_unit'))
                            ->options(WeightUnit::class)
                            ->default(WeightUnit::Kg),
                    ])
                    ->columns(1),

                Section::make(__('settings.user.security_section'))
                    ->description(__('settings.user.security_description'))
                    ->schema([
                        TextInput::make('current_password')
                            ->label(__('settings.user.fields.current_password'))
                            ->password()
                            ->revealable()
                            ->autocomplete('current-password')
                            ->requiredWith('password')
                            ->currentPassword(),

                        TextInput::make('password')
                            ->label(__('settings.user.fields.new_password'))
                            ->password()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->confirmed()
                            ->rules([Password::default()]),

                        TextInput::make('password_confirmation')
                            ->label(__('settings.user.fields.confirm_password'))
                            ->password()
                            ->revealable()
                            ->autocomplete('new-password'),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        try {
            $data = $this->getSchema('form')->getState();
            /* @var \App\Models\User $user */
            $user = Auth::user();

            $avatarValue = is_array($data['avatar']) ? ($data['avatar'][0] ?? null) : $data['avatar'];

            $user->updateSettings([
                'avatar' => $avatarValue,
                'preferred_language' => $data['preferred_language'] ?: null,
                'weight_unit' => $data['weight_unit'],
            ]);

            $passwordChanged = false;
            if (! empty($data['password'])) {
                $user->update([
                    'password' => Hash::make($data['password']),
                ]);
                $passwordChanged = true;

                $this->form->fill([
                    'avatar' => $avatarValue ? [$avatarValue] : [],
                    'preferred_language' => $data['preferred_language'] ?: null,
                    'weight_unit' => $data['weight_unit'],
                    'current_password' => null,
                    'password' => null,
                    'password_confirmation' => null,
                ]);
            }

            Notification::make()
                ->success()
                ->title($passwordChanged ? __('settings.user.messages.password_changed') : __('settings.user.messages.saved'))
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('settings.user.messages.save_failed'))
                ->body(__('settings.user.messages.save_failed_body'))
                ->send();

            throw $e;
        }
    }
}
