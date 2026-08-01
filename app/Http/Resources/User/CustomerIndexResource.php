<?php

namespace app\http\resources\user;

use Carbon\Carbon;
use illuminate\http\request;
use Illuminate\Http\Resources\Json\JsonResource;

class customerindexresource extends jsonresource
{
    /**
     * transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toarray(request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->getrolenames()->first(),

            'phone'         => $this->profile?->phone,
            'avatar'        => $this->profile?->avatar,
            'address'       => $this->profile?->address,
            'gender'        => $this->profile?->gender,

            'age'           => $this->profile?->birth_of_date
                ? Carbon::parse($this->profile->birth_of_date)->age
                : null,
            'governorate' =>[
                'id' =>$this->profile->city->governorate->id,
                'name' =>$this->profile->city->governorate->name,
            ],
            'city' =>[
                'id' =>$this->profile->city->id,
                'name' =>$this->profile->city->name,
            ],
            'is_banned' => $this->is_banned,
            'banned_at' => $this->banned_at?->format('d-m-Y H:i'),
            'created_at' => $this->created_at->format('d-m-Y H:i'),
        ];
    }
}
