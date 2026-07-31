<?php

namespace App\Http\Requests\Gallery;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGalleryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'category_id' => [
                'nullable',
                'exists:categories,id',
            ],

            'title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'type' => [
                'sometimes',
                'in:image,video',
            ],

            'file' => [
                'nullable',
                'file',
                'max:51200',
            ],

        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            if (!$this->hasFile('file')) {
                return;
            }

            $type = $this->type;

            if (!$type) {
                return;
            }

            $extension = strtolower(
                $this->file('file')->getClientOriginalExtension()
            );

            if (
                $type == 'image' &&
                !in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])
            ) {
                $validator->errors()->add(
                    'file',
                    'The uploaded file must be an image.'
                );
            }

            if (
                $type == 'video' &&
                !in_array($extension, ['mp4', 'mov', 'avi', 'mkv'])
            ) {
                $validator->errors()->add(
                    'file',
                    'The uploaded file must be a video.'
                );
            }

        });
    }
}