<?php

namespace App\Http\Requests\Booking;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CheckBookingAvailabilityRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $minDate = now()->addDays(4)->toDateString();
        return [
            'booking_date' => ['required', 'date_format:Y-m-d',"after_or_equal:{$minDate}"],
            'start_time'   => ['nullable', 'date_format:H:i', 'required_with:duration'],
            'duration'     => ['nullable', 'integer', 'min:1'],
        ];

    }

    public function messages(): array
    {
        return [
            'booking_date.after_or_equal' => __('messages.booking_must_be_4_days_ahead'),
        ];
    }
}
