<?php

namespace App\Filament\App\Pages;

use App\Filament\App\Widgets\CalendarWidget;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Calendar extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.app.pages.calendar';

    public static function getNavigationLabel(): string
    {
        return __('trainings.calendar.navigation_label');
    }

    public function getTitle(): string
    {
        return __('trainings.calendar.title');
    }

    protected function getFooterWidgets(): array
    {
        return [
            CalendarWidget::class,
        ];
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return 1;
    }
}
