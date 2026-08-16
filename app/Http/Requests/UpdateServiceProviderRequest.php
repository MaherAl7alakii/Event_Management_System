<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceProviderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'sometimes|required|string|max:100',
            'last_name' => 'sometimes|required|string|max:100',
            'city_id' => 'sometimes|nullable|exists:cities,id',
            'governorate_id' => 'sometimes|nullable|exists:governorates,id',
            'account_type' => 'sometimes|in:individual,professional',
            'business_name' => 'sometimes|nullable|string|max:255',
            'avatar' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|nullable|string',
            'years_of_experience' => 'sometimes|nullable|string',
            'description' => 'sometimes|nullable|string',
            'main_service_id' => 'sometimes|nullable|exists:categories,id',
            'categories' => 'sometimes|nullable|array',
            'categories.*' => 'exists:categories,id',
            'documents' => 'sometimes|nullable|array',
            'documents.*' => 'url',
            'portfolios' => 'sometimes|nullable|array',
            'portfolios.*.type' => 'required|in:image,video',
            'portfolios.*.title' => 'required|string|max:255',
            'portfolios.*.url' => 'required|url',
        ];
    }
}