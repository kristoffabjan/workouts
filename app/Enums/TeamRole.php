<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

enum TeamRole: string implements HasColor, HasIcon, HasLabel
{
    case Coach = 'coach';
    case Client = 'client';

    public function getLabel(): ?string
    {
        return __('enums.team_role.'.$this->value);
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Coach => Color::Amber,
            self::Client => Color::Sky,
        };
    }

    public function getIcon(): string|\BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::Coach => Heroicon::AcademicCap,
            self::Client => Heroicon::User,
        };
    }
}
