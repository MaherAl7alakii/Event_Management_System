<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class BookingModificationResponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'approved' => ['required', 'boolean'],

            // ذات معنى فقط عندما يكون المستجيب هو المزود ووافق —
            // يُتجاهل تماماً عندما يكون المستجيب هو الزبون (راجع
            // BookingModificationService::respond). عدم إرسالها يعني
            // "لا سعر نهائي جديد — طبّق التعديل مباشرة".
            'new_price' => ['sometimes', 'nullable', 'numeric', 'min:0.01'],
        ];
    }
}
