<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingUpdateRequest extends FormRequest
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
        $booking = $this->route('booking');
        $pricingType = $booking?->service?->pricing_type?->value;


        return [
            'start_time'     => '|required|date_format:H:i',
            'duration'       => [
                Rule::requiredIf(in_array($pricingType, ['per_hour', 'per_hour_per_person'])),
                Rule::excludeIf(!in_array($pricingType, ['per_hour', 'per_hour_per_person'])),
                'integer',
                'min:30',
                function ($attribute, $value, $fail) {
                    if ($value % 30 !== 0) {
                        $fail(__('messages.validation.duration.invalid_intervals'));
                    }
                },
            ],
            'quantity' => [
                Rule::requiredIf(in_array($pricingType, ['per_person', 'per_hour_per_person'])),
                Rule::excludeIf(!in_array($pricingType, ['per_person', 'per_hour_per_person'])),

                'integer',
                'min:1',
            ],

            'customer_notes' => 'nullable|string|max:1000',
        ];
    }
}
