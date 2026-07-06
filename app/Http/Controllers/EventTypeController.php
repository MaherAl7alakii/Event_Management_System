<?php

namespace App\Http\Controllers;


use App\Http\Resources\EventTypeResource;
use App\Models\EventType;
use App\Traits\ResponseTrait;
use Symfony\Component\HttpFoundation\Response;

class EventTypeController extends Controller
{
    use ResponseTrait;
    public function index()
    {
        $eventTypes = EventType::All();

        return $this->apiResponse(
            EventTypeResource::collection($eventTypes),
            'Event Types fetched successfully',
            Response::HTTP_OK
        );

    }
}
