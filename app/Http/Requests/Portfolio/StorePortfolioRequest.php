<?php

namespace App\Http\Requests\Portfolio;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class StorePortfolioRequest extends FormRequest
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
        return [
            'title' => ['required', 'string', 'max:255'],

            'type' => [
                'required',
                Rule::in(['image', 'video']),
            ],

           'url' => [
                   'required',
                   'url',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The title field is required.',

            'type.required' => 'The type field is required.',
            'type.in' => 'The type must be image or video.',

           'url.required' => 'The url field is required.',
           'url.url' => 'The url must be a valid URL.',
            'url.mimes' => 'Only jpg, jpeg, png, mp4, mov and avi files are allowed.',
            'url.max' => 'The file size must not exceed 20MB.',
        ];
    }
}