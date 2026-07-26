<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimeOffResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'type'                => $this->type,
            'start_date'          => $this->start_date->format('Y-m-d'),
            'end_date'            => $this->end_date->format('Y-m-d'),
            'start_time'          => $this->start_time ? Carbon::parse($this->start_time)->format('H:i') : null,
            'end_time'            => $this->end_time ? Carbon::parse($this->end_time)->format('H:i') : null,
            'reason' =>  $this->reason->value,
            'reason_view'  => $this->reason->view() ,
            'note'       => $this->note,
        ];
    }
}
