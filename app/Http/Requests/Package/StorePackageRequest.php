<?php

namespace App\Http\Requests\Package;

use Illuminate\Foundation\Http\FormRequest;

class StorePackageRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }



    public function rules(): array
    {
        return [

            'image' => 'nullable|image|max:2048',

            'name' => 'required|string',

            'description' => 'nullable|string',

            'discount' => 'nullable|numeric|min:0|max:100',

            'status' => 'nullable|in:active,hidden',


            'service_ids' => 'required|array|min:1',

            'service_ids.*' => 'required|exists:services,id',

        ];
    }
}