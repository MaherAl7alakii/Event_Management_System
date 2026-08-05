<?php

namespace App\Http\Requests\Offer;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [

            'discount' => ['required','integer','min:1','max:99'],

            'start_date' => ['required','date'],

            'end_date' => ['required','date','after_or_equal:start_date']

        ];
    }
}