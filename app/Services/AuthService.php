<?php

namespace App\Services;

use App\Models\User;



use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\UnauthorizedException;
use Spatie\Permission\Models\Role;

class AuthService
{

    public function register(array $data)
    {

        db::beginTransaction();
        try {
            $data['name'] = $data['first_name'] . ' ' . $data['last_name'];
            unset($data['first_name'], $data['last_name']);

            $user = User::create($data);

            $roleName = request()->is('api/customer/register') ? 'customer' : 'service_provider';

            $role = Role::query()->where('name', $roleName)->first();
            $user->assignRole($role);
            $permissions = $role->permissions->pluck('name')->toArray();
            $user->givePermissionTo($permissions);


            db::commit();
        } catch (\Exception $e) {
            db::rollBack();
            throw new \Exception('Registration failed: ' . $e->getMessage());
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



    public  function logout(): void
    {
        auth()->user()->token()->revoke();
    }
}
