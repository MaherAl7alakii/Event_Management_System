<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class PackageBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
//            'event_id' => ['required', 'exists:events,id'],

            'services'                => ['required', 'array', 'min:1'],
            'services.*.service_id'   => ['required', 'integer', 'distinct'],
            'services.*.booking_date' => ['required', 'date'],
            'services.*.start_time'   => ['required', 'date_format:H:i'],
            'services.*.duration'     => ['nullable', 'integer', 'min:30'],
            'services.*.quantity'     => ['nullable', 'integer', 'min:1'],
            'services.*.customer_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
