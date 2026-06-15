<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RefreshRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
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

   
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $this->authService->register($data);

        return $this->apiResponse(
            [
                'user' => $user['user'],
                'token' => $user['token'],
            ],
            'Registration completed successfully. OTP sent for verification.',
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

    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        $email = $request->validated('email');
        $this->authService->sendOtp($email);

        return $this->apiResponse(
            null,
            'OTP sent successfully.',
            Response::HTTP_OK
        );
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->authService->verifyOtp($data['email'], $data['otp']);

        return $this->apiResponse(
            null,
            'Email verified successfully.',
            Response::HTTP_OK
        );
    }
    public function forgotPassword(SendOtpRequest $request)
{
    $this->authService->forgotPassword($request->email);

   return $this->apiResponse(
    null,
    'OTP sent successfully.',
    Response::HTTP_OK
);
}

public function resetPassword(ResetPasswordRequest $request)
{
    $this->authService->resetPassword(
        $request->email,
        $request->otp,
        $request->password
    );

   return $this->apiResponse(
    null,
    'Password reset successfully.',
    Response::HTTP_OK
);
}
public function sendForgotPasswordOtp(SendOtpRequest $request): JsonResponse
{
    $data = $request->validated();

    $this->authService->sendForgotPasswordOtp($data['email']);

    return $this->apiResponse(
        null,
        'OTP sent successfully.',
        Response::HTTP_OK
    );
}
}