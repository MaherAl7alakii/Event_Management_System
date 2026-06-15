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
        $this->sendOtp($user->email, 'email_verify');
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

   
    public function sendOtp(string $email, string $type): void
{
    $user = User::where('email', $email)->first();

    if (!$user) {
        throw new \Exception('User not found.');
    }

    if ($type === 'email_verify' && $user->email_verified_at) {
        throw new \Exception('Email already verified.');
    }

    $otp = $this->otpService->generateOtp($email, $type);

    Mail::to($email)->send(new OtpMail($otp));
}
    
   public function verifyOtp(string $email, string $type, string $otp): bool
{
    $user = User::where('email', $email)->first();

    if (!$user) {
        throw new \Exception('User not found.');
    }

    if ($type === 'email_verify' && $user->email_verified_at) {
        throw new \Exception('Email already verified.');
    }

    if ($this->otpService->verifyOtp($email, $type, $otp)) {
        if ($type === 'email_verify') {
            $user->email_verified_at = now();
            $user->save();
        }

        if ($type === 'password_reset') {
            
           Cache::put("password_reset_verified:{$email}", true, now()->addMinutes(15));
        }
        return true;
    }

    throw new \Exception('Invalid OTP');
}

   public function resetPassword(string $email, string $newPassword): bool
{
    $user = User::where('email', $email)->first();

    if (!$user) {
        throw new \Exception('User not found.');
    }

   
    $isVerified = Cache::get("password_reset_verified:{$email}");

    if (!$isVerified) {
        throw new \Exception('Please verify your OTP first.');
    }

    $user->update([
        'password' => Hash::make($newPassword)
    ]);

  
    Cache::forget("password_reset_verified:{$email}");

    return true;
}



}