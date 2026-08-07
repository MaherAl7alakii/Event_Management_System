<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['text', 'image', 'video','file'])],


            'body' => ['nullable', 'string', 'max:5000', Rule::requiredIf(fn () => $this->input('type') === 'text')],

            'media_url' => [
                Rule::requiredIf(fn () => in_array($this->input('type'), ['image', 'video'])),
                'nullable',
                'url',
                'max:2048',
            ],

        ];
    }

}
