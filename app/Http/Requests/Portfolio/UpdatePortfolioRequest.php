<?php


namespace App\Http\Requests\Portfolio;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePortfolioRequest extends FormRequest
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
            'title' => ['sometimes', 'string', 'max:255'],

            'type' => [
                'sometimes',
                Rule::in(['image', 'video']),
            ],

            'url' => [
                'sometimes',
                'url',
       ],
        ];
    }

    public function messages(): array
    {
        return [
            'type.in' => 'The type must be image or video.',
            'url.mimes' => 'Only jpg, jpeg, png, mp4, mov and avi files are allowed.',
            'url.max' => 'The file size must not exceed 20MB.',
        ];
    }
}