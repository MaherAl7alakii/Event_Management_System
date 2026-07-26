<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class WorkingHoursRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'days' => ['required', 'array', 'size:7'],


            'days.*.day_of_week' => ['required', 'integer', 'between:0,6', 'distinct'],
            'days.*.is_active'   => ['required', 'boolean'],


            'days.*.start_time'  => [
                'exclude_if:days.*.is_active,false',
                'required',
                'date_format:H:i',
            ],
            'days.*.end_time'    => [
                'exclude_if:days.*.is_active,false',
                'required',
                'date_format:H:i',
                'after:days.*.start_time',
            ],
        ];
    }
}
