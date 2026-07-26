<?php

namespace App\Enums;

enum TimeOffReason: string
{
    case PERSONAL = 'personal';
    case SICK_LEAVE = 'sick_leave';
    case VACATION = 'vacation';
    case MAINTENANCE = 'maintenance';
    case EMERGENCY = 'emergency';
    case TRAINING = 'training';
    case BREAK = 'break';
    case OTHER = 'other';


    public function view(): string
    {
        return __("enums.time_off_reasons.{$this->value}");
    }

    public static function options(): array
    {
        return collect(self::cases())->map(function ($case) {
            return [
                'value' => $case->value,
                'view'  => $case->view(),
            ];
        })->all();
    }

}
