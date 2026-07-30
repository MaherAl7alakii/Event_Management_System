<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EstimatePriceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $service = $this->route('service');
        $pricingType = $service ? $service->pricing_type->value : null;

        return [
            'duration'       => [
                Rule::requiredIf(in_array($pricingType, ['per_hour', 'per_hour_per_person'])),
                Rule::excludeIf(!in_array($pricingType, ['per_hour', 'per_hour_per_person'])),
                'integer',
                'min:30',
                function ($attribute, $value, $fail) {
                    if ($value % 30 !== 0) {
                        $fail('The duration must be in 30-minute intervals (e.g., 30, 60, 90).');
                    }
                },
            ],
            'quantity' => [
                Rule::requiredIf(in_array($pricingType, ['per_person', 'per_hour_per_person'])),
                Rule::excludeIf(!in_array($pricingType, ['per_person', 'per_hour_per_person'])),
                'integer',
                'min:1',
            ],
        ];
    }
}
