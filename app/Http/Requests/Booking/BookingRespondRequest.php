<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingRespondRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        return [
            'buffer_after_minutes' => [
                 'nullable',
                'integer',
                'min:0',
                'max:1440',
            ],
        ];
    }
}
