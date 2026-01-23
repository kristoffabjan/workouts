<?php

namespace App\Filament\App\Pages;

use App\Enums\WeightUnit;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

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
        $user = auth()->user();
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
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->getSchema('form')->getState();
        $user = Auth::user();

        $avatarValue = is_array($data['avatar']) ? ($data['avatar'][0] ?? null) : $data['avatar'];

        $user->updateSettings([
            'avatar' => $avatarValue,
            'preferred_language' => $data['preferred_language'] ?: null,
            'weight_unit' => $data['weight_unit'],
        ]);

        Notification::make()
            ->success()
            ->title(__('settings.user.messages.saved'))
            ->send();
    }
}
