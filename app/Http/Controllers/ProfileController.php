<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Services\ProfileService;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    use ResponseTrait;

    private ProfileService $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function show(): JsonResponse
    {
        $profile = $this->profileService->getProfileByUserId(auth()->id());

        if (!$profile) {
            return $this->apiResponse(
                null,
                'Profile not found.',
                Response::HTTP_OK
            );
        }

        return $this->apiResponse(
            new ProfileResource($profile),
            'Profile fetched successfully.',
            Response::HTTP_OK
        );
    }

    public function store(StoreProfileRequest $request): JsonResponse
    {
        $data = $request->validated();
        $profile = $this->profileService->updateOrCreateProfile(auth()->id(), $data);

        return $this->apiResponse(
            new ProfileResource($profile),
            'Profile created successfully.',
            Response::HTTP_CREATED
        );
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
 
  

        $data = $request->validated();
        $profile = $this->profileService->updateOrCreateProfile(auth()->id(), $data);

        return $this->apiResponse(
            new ProfileResource($profile),
            'Profile updated successfully.',
            Response::HTTP_OK
        );
    }
}