<?php

namespace App\Http\Requests\Service;

use App\Enums\PricingType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class ServiceRequest extends FormRequest
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
//        $hasArabic  = !empty(array_filter($this->input('ar', [])));
//        $hasEnglish = !empty(array_filter($this->input('en', [])));
//        $hasBothLanguages = $hasArabic && $hasEnglish;

        $hasMainArabic  = !empty(array_filter($this->input('ar', [])));
        $hasMainEnglish = !empty(array_filter($this->input('en', [])));

        $features = collect($this->input('features', []));

        $hasFeatureArabic = $features->pluck('ar')->filter(function ($item) {
            return !empty(array_filter((array) $item));
        })->isNotEmpty();

        $hasFeatureEnglish = $features->pluck('en')->filter(function ($item) {
            return !empty(array_filter((array) $item));
        })->isNotEmpty();

        $hasArabic  = $hasMainArabic || $hasFeatureArabic;
        $hasEnglish = $hasMainEnglish || $hasFeatureEnglish;

        $hasBothLanguages = $hasArabic && $hasEnglish;

        return [

            'category_id' => ['required', 'exists:categories,id',],
            'city_id' => ['required', 'exists:cities,id',],
            'pricing_type' => ['required',  new Enum(PricingType::class),],
            'base_price' => ['required', 'numeric', 'min:0',],
            'min_hours' => ['nullable', 'integer', 'min:1',],
            'max_hours' => ['nullable', 'integer', 'gte:min_hours',],
            'max_guests' => ['nullable', 'integer', 'min:1',],
            'is_active' => ['nullable', 'boolean',],
            'linked_service_ids'   => ['nullable', 'array'],
            'linked_service_ids.*' => ['integer', 'exists:services,id'],
            'images'            => ['nullable', 'array', 'max:10'],
            'images.*.id'       => ['nullable', 'integer', 'exists:service_images,id'],
            'images.*.url'      => ['required', 'url'],
            'images.*.is_primary' => ['nullable', 'boolean'],
            'ar.title' => [$hasArabic ? 'required' : 'nullable', 'string', 'max:150',],
            'ar.description' => [$hasArabic ? 'required' : 'nullable', 'string',],
            'ar.address'     => [
                'nullable',
                'string',
                Rule::when($hasBothLanguages, ['required_with:en.address'])
            ],
            'en.title' => [$hasEnglish ? 'required' : 'nullable', 'string', 'max:150',],
            'en.description' => [$hasEnglish ? 'required' : 'nullable', 'string',],
            'en.address'     => [
                'nullable',
                'string',
                Rule::when($hasBothLanguages, ['required_with:ar.address'])
            ],

            'features' => ['nullable', 'array', 'max:20'],
            'features.*.ar.value' => [$hasBothLanguages ? 'required' : 'nullable', 'string', 'max:255'],
            'features.*.en.value' => [$hasBothLanguages ? 'required' : 'nullable', 'string', 'max:255'],

        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $hasArabic  = !empty(array_filter($this->input('ar', [])));
            $hasEnglish = !empty(array_filter($this->input('en', [])));

            if (!$hasArabic && !$hasEnglish) {

                $validator->errors()->add(
                    'languages',
                    __('messages.validation.at_least_one')
                );

            }

        });
    }


    public function messages(): array
    {
        return [
            'features.*.ar.value.required' => __('messages.validation.features.ar_required'),
            'features.*.en.value.required' => __('messages.validation.features.en_required'),
        ];
    }
}
