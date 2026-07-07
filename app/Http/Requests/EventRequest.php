<?php

namespace App\Http\Requests;

use App\Models\EventType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class EventRequest extends FormRequest
{

    protected ?bool $isOtherTypeSelected = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    protected function checkIfOtherType(): bool
    {
        if ($this->isOtherTypeSelected !== null) {
            return $this->isOtherTypeSelected;
        }

        $eventTypeId = $this->input('event_type_id');

        if (!$eventTypeId) {
            return $this->isOtherTypeSelected = false;
        }

        return $this->isOtherTypeSelected = DB::table('event_type_translations')
            ->where('event_type_id', $eventTypeId)
            ->where(function ($query) {
                $query->where('name', 'Other');
            })
            ->exists();
    }


    protected function prepareForValidation(): void
    {
        if (!$this->checkIfOtherType()) {
            $this->merge([
                'other_type' => null,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isStore = $this->isMethod('post');

        return [
            'event_type_id' => 'required|exists:event_types,id',

            'other_type'    => [
                $this->checkIfOtherType() ? 'required' : 'nullable',
                'string',
                'max:255'
            ],

            'city_id'       => 'required|exists:cities,id',
            'title'         => 'required|string|max:255',
            'cover_image'   => 'nullable|url|max:2048',

            'event_date'    => $isStore ? 'required|date|after_or_equal:today' : 'required|date',

            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'required|date_format:H:i|after:start_time',
            'guests_count'  => 'required|integer|min:1',
        ];
    }
}
