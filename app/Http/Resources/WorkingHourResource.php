<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkingHourResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'day_of_week' => $this->day_of_week,
            'start_time'  => $this->start_time ? Carbon::parse($this->start_time)->format('H:i') : null,
            'end_time'    => $this->end_time ? Carbon::parse($this->end_time)->format('H:i') : null,
            'is_active' => $this->is_active
        ];
    }
}
