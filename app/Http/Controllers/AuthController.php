<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RefreshRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Services\AuthService;
use App\Traits\ResponseTrait;
use ErrorException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

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

    public function sendEmailVerificationOtp(): JsonResponse
    {
        $this->authService->sendOtp(auth()->user()->email, 'email_verify');

        return $this->apiResponse(null, 'OTP for email verification sent successfully.', Response::HTTP_OK);
    }

    public function verifyEmail(VerifyOtpRequest $request): JsonResponse
    {
        
        $email = $request->email ?? auth()->user()->email;

        $this->authService->verifyOtp(
            $email,
            'email_verify',
            $request->otp
        );

        return $this->apiResponse(
            null,
            'Email verified successfully.',
            Response::HTTP_OK
        );
    }

    public function sendForgotPasswordOtp(ForgotPasswordRequest $request): JsonResponse
    {
        $this->authService->sendOtp($request->email, 'password_reset');

       
        
        return $this->apiResponse(
            null,
            'OTP for password reset sent successfully.',
            Response::HTTP_OK
        );
    }

    public function verifyForgotPasswordOtp(VerifyOtpRequest $request): JsonResponse
    {
       
        $email = $request->email ?? (auth()->check() ? auth()->user()->email : null);

        if (!$email) {
            return $this->apiResponse(null, 'Email is required for password reset verification.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->authService->verifyOtp(
            $email,
            'password_reset',
            $request->otp
        );

        return $this->apiResponse(
            null,
            'OTP for password reset verified successfully. Proceed to reset password.',
            Response::HTTP_OK
        );
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        
        $email = $request->email ?? (auth()->check() ? auth()->user()->email : null);

        if (!$email) {
            return $this->apiResponse(null, 'Email is required for password reset.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->authService->resetPassword(
            $email,
            $request->password
        );

        return $this->apiResponse(
            null,
            'Password reset successfully.',
            Response::HTTP_OK
        );
    }

    public function resendOtp(SendOtpRequest $request): JsonResponse
    {
        $type = $request->input('type', 'email_verify');
        $this->authService->sendOtp($request->email, $type);

        return $this->apiResponse(null, 'OTP resent successfully.', Response::HTTP_OK);
    }
}