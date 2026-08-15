<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class AdminRefundRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_id' => ['required', 'integer', 'exists:payments,id'],
            'amount'     => ['required', 'numeric', 'min:0.01'],
            'reason'     => ['nullable', 'string', 'max:500'],
        ];
    }
}
