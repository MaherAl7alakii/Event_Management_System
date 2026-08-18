<?php

namespace App\Http\Requests;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EventWithBookingsRequest extends FormRequest
{
    protected ?bool $isOtherTypeSelected = null;

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

    public function rules(): array
    {
        $minDate = now()->addDays(4)->toDateString();


        $rules = [

            'event_type_id' => 'required|exists:event_types,id',
            'other_type'    => [$this->checkIfOtherType() ? 'required' : 'nullable', 'string', 'max:255'],
            'city_id'       => 'required|exists:cities,id',
            'title'         => 'required|string|max:255',
            'cover_image'   => 'nullable|url|max:2048',
            'event_date'    => ['required', 'date_format:Y-m-d', "after_or_equal:{$minDate}"],
            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'required|date_format:H:i|after:start_time',
            'guests_count'  => 'required|integer|min:1',


            'bookings'      => 'required|array|min:1',
        ];


        $bookings = $this->input('bookings', []);

        if (is_array($bookings)) {
            foreach ($bookings as $index => $booking) {
                $serviceId = $booking['service_id'] ?? null;
                $pricingType = null;


                if ($serviceId) {
                    $service = Service::find($serviceId);
                    $pricingType = $service?->pricing_type?->value;
                }

                $rules["bookings.{$index}.service_id"]     = 'required|exists:services,id';
                $rules["bookings.{$index}.booking_date"]   = ['required', 'date_format:Y-m-d', "after_or_equal:{$minDate}"];
                $rules["bookings.{$index}.start_time"]     = 'required|date_format:H:i';
                $rules["bookings.{$index}.customer_notes"] = 'nullable|string|max:1000';

                $rules["bookings.{$index}.duration"] = [
                    Rule::requiredIf(in_array($pricingType, ['per_hour', 'per_hour_per_person'])),
                    Rule::excludeIf(!in_array($pricingType, ['per_hour', 'per_hour_per_person'])),
                    'integer',
                    'min:30',
                    function ($attribute, $value, $fail) {
                        if ($value % 30 !== 0) {
                            $fail(__('messages.validation.duration.invalid_intervals'));
                        }
                    },
                ];

                $rules["bookings.{$index}.quantity"] = [
                    Rule::requiredIf(in_array($pricingType, ['per_person', 'per_hour_per_person'])),
                    Rule::excludeIf(!in_array($pricingType, ['per_person', 'per_hour_per_person'])),
                    'integer',
                    'min:1',
                ];
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'event_date.after_or_equal' => __('messages.booking_must_be_4_days_ahead'),
            'bookings.*.booking_date.after_or_equal' => __('messages.booking_must_be_4_days_ahead'),
        ];
    }
}
