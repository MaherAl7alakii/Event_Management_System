<?php

namespace App\Http\Requests\Gallery;

use Illuminate\Foundation\Http\FormRequest;

class StoreGalleryRequest extends FormRequest
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
                'required',
                'in:image,video',
            ],

            'file' => [
                'required',
                'file',
                'max:51200', //50MB
            ],

        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            if (!$this->hasFile('file')) {
                return;
            }

            $extension = strtolower(
                $this->file('file')->getClientOriginalExtension()
            );

            if (
                $this->type == 'image' &&
                !in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])
            ) {
                $validator->errors()->add(
                    'file',
                    'The uploaded file must be an image.'
                );
            }

            if (
                $this->type == 'video' &&
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