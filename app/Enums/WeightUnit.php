<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum WeightUnit: string implements HasLabel
{
    case Kg = 'kg';
    case Lb = 'lb';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Kg => __('enums.weight_unit.kg'),
            self::Lb => __('enums.weight_unit.lb'),
        };
    }
}
