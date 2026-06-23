<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\LoginWithGoogleRequest;
use App\Http\Requests\Auth\RefreshRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\VerifyResetOtpRequest;
use App\Http\Requests\Auth\ResendResetOtpRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Traits\ResponseTrait;
use ErrorException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

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
                'user' => new UserResource($user['user']),
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

    public function loginWithGoogle(LoginWithGoogleRequest $request)
    {

        $googleToken = $request->input('google_token');

        $user = $this->authService->loginWithGoogle($googleToken);

        return $this->apiResponse(
            [
                'user' => new UserResource($user['user']),
                'token' => $user['token'],
            ],
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

public function sendOtp(): JsonResponse
{
    $this->authService->sendOtp(auth()->user()->email);

    return $this->apiResponse(null,'OTP sent successfully.',Response::HTTP_OK);
}
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $email = auth('api')->check() ? auth('api')->user()->email : $request->email;

        if (!$email) {
            return $this->apiResponse(null, 'Email is required for verification.', Response::HTTP_BAD_REQUEST);
        }

        $result = $this->authService->verifyOtp(
            $email,
            $request->otp
        );

        return $this->apiResponse(
            $result,
            'Email verified .',
            Response::HTTP_OK
        );
    }

    public function forgotPassword(SendOtpRequest $request): JsonResponse
{
    $result = $this->authService->forgotPassword($request->email);

    return $this->apiResponse(
        $result,
        'OTP sent successfully to your email.',
        Response::HTTP_OK
    );
}

    public function resendOtp(SendOtpRequest $request): JsonResponse
    {
        $type = $request->input('type', 'email_verify'); // default to email_verify

        if ($type === 'password_reset') {
            $this->authService->forgotPassword($request->email);
        } else {
            $this->authService->sendOtp($request->email);
        }

        return $this->apiResponse(
            null,
            'OTP resent successfully.',
            Response::HTTP_OK
        );
    }

    public function verifyResetOtp(VerifyResetOtpRequest $request): JsonResponse
    {
       $result = $this->authService->verifyResetOtp(
    $request->reset_token,
    $request->otp
);

        return $this->apiResponse(
            $result,
            'OTP verified successfully. You can now reset your password.',
            Response::HTTP_OK
        );
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $this->authService->resetPassword(
            auth()->user(),
            $request->password
        );

        return $this->apiResponse(
            null,
            'Password reset successfully.',
            Response::HTTP_OK
        );
    }
public function resendResetOtp(ResendResetOtpRequest $request): JsonResponse
{
    $this->authService->resendResetOtp(
        $request->reset_token
    );

    return $this->apiResponse(
        null,
        'OTP resent successfully.',
        Response::HTTP_OK
    );
}
}
