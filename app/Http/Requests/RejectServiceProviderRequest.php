<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectServiceProviderRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

   
    public function rules(): array
    {
        return [
            'reason' => ['required','string','min:5']
        ];
    }
   
    public function messages(): array
    {
        return [
            'rejection_reason.required' => 'سبب الرفض مطلوب',
            'rejection_reason.string' => 'سبب الرفض يجب أن يكون نصاً',
            'rejection_reason.max' => 'سبب الرفض لا يجب أن يتجاوز 1000 حرف',
            'rejection_reason.min' => 'سبب الرفض يجب أن يكون على الأقل 10 أحرف',
        ];
    }
}
