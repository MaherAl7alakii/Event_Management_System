<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceProviderRequest extends FormRequest
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
            'city_id' => 'nullable|exists:cities,id',
//            'governorate_id'=>'required|exists:governorates,id',
//            'account_type' => 'required|in:individual,professional',
            'business_name' => 'required|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'years_of_experience' => 'nullable|string',
            'description' => 'nullable|string',
//            'main_service_id' => 'nullable|exists:categories,id',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'documents' => 'nullable|array',
            'documents.*' => 'url',
            'portfolios' => 'nullable|array',
            'portfolios.*.type' => 'required|in:image,video',
            'portfolios.*.title' => 'required|string|max:255',
            'portfolios.*.url' => 'required|url',
        ];
    }
}
