<?php

namespace App\Http\Requests;

use App\Enums\TimeOffReason;
use App\Enums\TimeOffType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;


class TimeOffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isBlockTime = $this->isBlockTime();

        return [
            'type' => ['required', new Enum(TimeOffType::class)],

            'start_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'end_date' => [
                Rule::excludeIf($isBlockTime),
                Rule::requiredIf(!$isBlockTime),
                'date',
                'after_or_equal:start_date',
            ],

            'start_time' => [
                Rule::requiredIf($isBlockTime),
                Rule::excludeIf(!$isBlockTime),
                'date_format:H:i',
            ],

            'end_time' => [
                Rule::requiredIf($isBlockTime),
                Rule::excludeIf(!$isBlockTime),
                'date_format:H:i',
                'after:start_time',
            ],

            'reason' => ['required', Rule::enum(TimeOffReason::class)],
            'note'   => ['nullable', 'string', 'max:1000'],
        ];
    }



    public function validated($key = null, $default = null): array
    {
        $data = parent::validated($key, $default);

        if ($this->isBlockTime()) {
            $data['end_date'] = $data['start_date'];
        }

        $data['end_date'] ??= $data['start_date'];

        return $data;
    }

    private function isBlockTime(): bool
    {
        return $this->input('type') === TimeOffType::BLOCK_TIME->value;
    }

}
