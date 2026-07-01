<?php

namespace App\Http\Controllers;

use App\Http\Resources\CityResource;
use App\Http\Resources\GovernorateResource;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use App\Models\Governorate;
use Symfony\Component\HttpFoundation\Response;

class GovernorateController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        $governorates = Governorate::all();

        return $this->apiResponse(
            GovernorateResource::collection($governorates),
            'Governorates fetched successfully',
            Response::HTTP_OK
        );
    }

    public function getGovernorateCities(Governorate $governorate)
    {
        $cities = $governorate->cities;

        return $this->apiResponse(
            CityResource::collection($cities),
            empty($cities) ? 'No cities found' : 'Cities fetched successfully',
            Response::HTTP_OK
        );

    }
}
