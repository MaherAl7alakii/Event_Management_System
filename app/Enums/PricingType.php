<?php

namespace App\Enums;

enum PricingType: string
{
    case FIXED = 'fixed';

    case PER_HOUR = 'per_hour';

    case PER_PERSON = 'per_person';

    case PER_HOUR_PER_PERSON = 'per_hour_per_person';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }


    public function view(): string
    {
        return __("enums.pricing_types.{$this->value}");
    }


    public static function options(): array
    {
        return collect(self::cases())->map(function ($case) {
            return [
                'value' => $case->value,
                'view' => $case->view(),
            ];
        })->all();
    }
}
