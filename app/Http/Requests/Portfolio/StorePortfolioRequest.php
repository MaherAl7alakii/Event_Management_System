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
                'file',
                'mimes:jpg,jpeg,png,mp4,mov,avi',
                'max:20480', // 20MB
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The title field is required.',

            'type.required' => 'The type field is required.',
            'type.in' => 'The type must be image or video.',

            'url.required' => 'Please upload a file.',
            'url.file' => 'The uploaded file is invalid.',
            'url.mimes' => 'Only jpg, jpeg, png, mp4, mov and avi files are allowed.',
            'url.max' => 'The file size must not exceed 20MB.',
        ];
    }
}