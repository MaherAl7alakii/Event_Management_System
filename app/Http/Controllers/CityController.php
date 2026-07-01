<?php

namespace App\Http\Controllers;

use App\Http\Resources\CityResource;
use App\Models\City;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CityController extends Controller
{
    use ResponseTrait;
    public function index()
    {
        $cities = City::with('governorate')->get();

        return $this->apiResponse(
            CityResource::collection($cities),
            'Cities fetched successfully',
            Response::HTTP_OK
        );
    }
}
