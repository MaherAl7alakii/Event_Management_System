<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
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
        $category = $this->route('category');


        $categoryId = is_object($category) ? $category->id : $category;


        $enTranslationId = DB::table('category_translations')
            ->where('category_id', $categoryId)
            ->where('locale', 'en')
            ->value('id');

        $arTranslationId = DB::table('category_translations')
            ->where('category_id', $categoryId)
            ->where('locale', 'ar')
            ->value('id');

        return [
            'icon' => ['required', 'url'],
            'is_active' => ['required', 'boolean'],

            'en.name' => [
                'required',
                'string',
                Rule::unique('category_translations', 'name')->ignore($enTranslationId)
            ],
            'ar.name' => [
                'required',
                'string',
                Rule::unique('category_translations', 'name')->ignore($arTranslationId)
            ],
        ];
    }
}
