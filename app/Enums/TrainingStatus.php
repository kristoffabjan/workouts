<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

enum TrainingStatus: string implements HasColor, HasIcon, HasLabel
{
    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case Completed = 'completed';
    case Skipped = 'skipped';

    public function getLabel(): ?string
    {
        return __('enums.training_status.'.$this->value);
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Draft => Color::Gray,
            self::Scheduled => Color::Blue,
            self::Completed => Color::Green,
            self::Skipped => Color::Orange,
        };
    }

    public function getIcon(): string|\BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::Draft => Heroicon::PencilSquare,
            self::Scheduled => Heroicon::Calendar,
            self::Completed => Heroicon::CheckCircle,
            self::Skipped => Heroicon::XCircle,
        };
    }
}
