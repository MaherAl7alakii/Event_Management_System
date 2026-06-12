<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RefreshRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use App\Traits\ResponseTrait;
use ErrorException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use ResponseTrait;

    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @throws ErrorException
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $this->authService->register($data);

        return $this->apiResponse(
            [
                'user' => $user['user'],
                'token' => $user['token'],
            ],
            'Registration completed successfully.',
            Response::HTTP_CREATED,
        );

    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->only('email', 'password');
        $token = $this->authService->login($data);

        return $this->apiResponse(
            $token,
            'Login successfully.',
            Response::HTTP_OK,
        );
    }

    public function refresh(RefreshRequest $request): JsonResponse
    {
        $refreshToken = $request->input('refresh_token');

        $token = $this->authService->refresh($refreshToken);

        return $this->apiResponse(
            $token,
            'Refresh token successfully.',
            Response::HTTP_OK,
        );
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return $this->apiResponse(
            null,
            'Logout successfully',
            Response::HTTP_OK
        );
    }

}
