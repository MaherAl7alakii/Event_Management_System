<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\UnauthorizedException;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class AuthService
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function register(array $data)
    {
        DB::beginTransaction();
        try {
            $data['name'] = $data['first_name'] . ' ' . $data['last_name'];
            unset($data['first_name'], $data['last_name']);

            $user = User::create($data);

            $roleName = request()->is('api/customer/register') ? 'customer' : 'service_provider';

            $role = Role::query()->where('name', $roleName)->first();
            $user->assignRole($role);
            $permissions = $role->permissions->pluck('name')->toArray();
            $user->givePermissionTo($permissions);

            DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();
        throw new \Exception('Registration failed: ' . $e->getMessage());
    }
    try {
        $this->sendOtp($user->email);
    } catch (\Exception $e) {
    }
    $tokenRequest = Request::create('/oauth/token', 'POST', [
            'grant_type' => 'password',
            'client_id' => config('services.passport.client_id'),
            'client_secret' => config('services.passport.client_secret'),
            'username' => $data['email'],
            'password' => $data['password'],
            'scope' => '',
        ]);

        $response = app()->handle($tokenRequest);
        $token = json_decode($response->getContent(), true);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
    public function login(array $data)
    {

        if (!Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            throw new AuthenticationException('Email or Password is incorrect.');

        }

        $user = User::where('email', $data['email'])->first();
        if (!$user->email_verified_at) {
    throw new AuthenticationException(
        'Please verify your email first.'
    );
}

         if (!(
            (request()->is('*admin*') && $user->hasRole('admin')) ||
            (request()->is('*customer*') && $user->hasRole('customer')) ||
            (request()->is('*service_provider*') && $user->hasRole('service_provider'))
        )) {
           throw new UnauthorizedException();
        }



        $tokenRequest = Request::create('/oauth/token', 'POST', [
            'grant_type' => 'password',
            'client_id' => config('services.passport.client_id'),
            'client_secret' => config('services.passport.client_secret'),
            'username' => $data['email'],
            'password' => $data['password'],
            'scope' => '',
        ]);


        $response = app()->handle($tokenRequest);

        if (!$response->isSuccessful()) {
            throw new AuthenticationException('Client authentication failed');

        }

        $token = json_decode($response->getContent(), true);

        return $token;
    }



    public function loginWithGoogle(string $googleToken)
    {

        $client = new \Google_Client(['client_id' => config('services.google.client_id')]);
        $payload = $client->verifyIdToken($googleToken);

        if (!$payload) {
            throw new AuthenticationException('Invalid Google Token');
        }

        if (empty($payload['email'])) {
            throw new AuthenticationException('Google account must provide a valid email address.');
        }

        $email = $payload['email'];
        $name = $payload['name'] ?? explode('@', $email)[0];

        DB::beginTransaction();
        try {

            $user = User::where('email', $email)->first();

            if (!$user) {

                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make(Str::random(24)),
                ]);

                $user->email_verified_at = now();
                $user->save();


                $roleName = request()->is('*customer*') ? 'customer' : 'service_provider';

                $role = Role::query()->where('name', $roleName)->first();
                if ($role) {
                    $user->assignRole($role);
                    $permissions = $role->permissions->pluck('name')->toArray();
                    $user->givePermissionTo($permissions);
                }
            } else {

                if (!(
                    (request()->is('*admin*') && $user->hasRole('admin')) ||
                    (request()->is('*customer*') && $user->hasRole('customer')) ||
                    (request()->is('*service_provider*') && $user->hasRole('service_provider'))
                )) {
                    throw new UnauthorizedException();
                }


                if (!$user->email_verified_at) {
                    $user->email_verified_at = now();
                    $user->save();
                    $user->refresh();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }


        $tokenRequest = Request::create('/oauth/token', 'POST', [
            'grant_type' => 'password',
            'client_id' => config('services.passport.client_id'),
            'client_secret' => config('services.passport.client_secret'),
            'username' => $user->email,
            'password' => 'social_auth_bypass',
            'scope' => '',
            'is_social' => true,
        ]);

        $response = app()->handle($tokenRequest);

        if (!$response->isSuccessful()) {
            throw new AuthenticationException('Client authentication failed');
        }

        $token = json_decode($response->getContent(), true);


        return [
            'user' => $user,
            'token' => $token,
        ];
    }


    public function refresh($refreshToken)
    {
        $tokenRequest = Request::create('/oauth/token', 'POST', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => config('services.passport.client_id'),
            'client_secret' => config('services.passport.client_secret'),
            'scope' => '',
        ]);

        $response = app()->handle($tokenRequest);

        if (!$response->isSuccessful()) {
            throw new AuthenticationException('Invalid refresh token');
        }
        $token = json_decode($response->getContent(), true);

        return $token;
    }

    public function logout(): void
    {
        auth()->user()->token()->revoke();
    }

    public function sendOtp(string $email): void
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw new \Exception('User not found.');
        }


        $otp = $this->otpService->generateOtp($email, 'email_verify');

        Mail::to($email)->send(new OtpMail($otp));
    }


    public function verifyOtp(string $email, string $otp): array
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw new \Exception('User not found.');
        }

        if ($this->otpService->verifyOtp($email, 'email_verify', $otp)) {
            $user->email_verified_at = now();
            $user->save();


            return [
                'user' => $user,
            ];
        }

        throw new \Exception('Invalid OTP');
    }

   public function forgotPassword(string $email): array
{
    $user = User::where('email', $email)->first();

    if (!$user) {
        throw new \Exception('User not found.');
    }

    $otp = $this->otpService->generateOtp($email, 'password_reset');

    Mail::to($email)->send(new OtpMail($otp));

    $resetToken = Str::uuid()->toString();

    Cache::put(
        "reset_email:{$resetToken}",
        $email,
        now()->addMinutes(15)
    );

    return [
        'reset_token' => $resetToken
    ];
}

  public function verifyResetOtp(string $resetToken,string $otp): array
{
    $email = Cache::get("reset_email:{$resetToken}");

    if (!$email) {
        throw new \Exception('Invalid or expired reset token.');
    }

    $user = User::where('email', $email)->first();

    if (!$user) {
        throw new \Exception('User not found.');
    }

    if (!$this->otpService->verifyOtp(
        $email,
        'password_reset',
        $otp
    )) {
        throw new \Exception('Invalid OTP');
    }

    $this->otpService->setTemporaryFlag(
        $email,
        'password_reset_verified',
        15
    );

    Cache::forget("reset_email:{$resetToken}");

    $tokenResult = $this->generateUserToken($user);

    return [
        'token' => $tokenResult
];
}

   public function resetPassword(User $user,string $newPassword): bool
{
    if (
        !$this->otpService->hasTemporaryFlag(
            $user->email,
            'password_reset_verified'
        )
    ) {
        throw new \Exception(
            'Session expired or invalid.'
        );
    }

    $user->update([
        'password' => Hash::make($newPassword)
    ]);

    $user->tokens()->delete();

    $this->otpService->clearTemporaryFlag(
        $user->email,
        'password_reset_verified'
    );

    return true;
}

    protected function generateUserToken(User $user): array
    {

        $tokenResult = $user->createToken('Password Reset Token');

        return [
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => $tokenResult->token->expires_at,
        ];
    }


public function resendResetOtp(string $resetToken): void
{
    $email = Cache::get("reset_email:{$resetToken}");

    if (!$email) {
        throw new \Exception(
            'Reset session expired. Please start again.'
        );
    }

    $otp = $this->otpService->generateOtp(
        $email,
        'password_reset'
    );

    Mail::to($email)->send(
        new OtpMail($otp)
    );
}

}
