<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class BookingModificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_time' => ['sometimes', 'date_format:H:i'],
            'booking_date'   => ['sometimes', 'date_format:Y-m-d'],
            'duration'   => ['sometimes', 'nullable', 'integer', 'min:30'],
            'quantity'   => ['sometimes', 'nullable', 'integer', 'min:1'],
            'customer_notes' =>['sometimes', 'nullable','string']
        ];
    }
}
