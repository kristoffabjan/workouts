<?php

namespace App\Filament\Admin\Pages;

use App\Settings\ApplicationSettings;
use BackedEnum;
use DateTimeZone;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageApplicationSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string $settings = ApplicationSettings::class;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 100;

    public static function getNavigationLabel(): string
    {
        return __('settings.application.navigation_label');
    }

    public function getTitle(): string
    {
        return __('settings.application.title');
    }

    public function getSubheading(): ?string
    {
        return __('settings.application.subheading');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('application_name')
                    ->label(__('settings.application.fields.application_name'))
                    ->required()
                    ->maxLength(255),

                FileUpload::make('application_logo')
                    ->label(__('settings.application.fields.application_logo'))
                    ->image()
                    ->directory('logos')
                    ->visibility('public')
                    ->automaticallyResizeImagesMode('cover')
                    ->imageAspectRatio('16:9')
                    ->automaticallyCropImagesToAspectRatio()
                    ->automaticallyResizeImagesToWidth(400)
                    ->automaticallyResizeImagesToHeight(100),

                Select::make('default_language')
                    ->label(__('settings.application.fields.default_language'))
                    ->options([
                        'en' => 'English',
                        'sl' => 'Slovenščina',
                    ])
                    ->required(),

                Select::make('timezone')
                    ->label(__('settings.application.fields.timezone'))
                    ->options(
                        collect(DateTimeZone::listIdentifiers())
                            ->mapWithKeys(fn (string $tz) => [$tz => $tz])
                            ->all()
                    )
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->is_admin;
    }
}
