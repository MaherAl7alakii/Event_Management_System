<?php

namespace App\Services;

use App\Models\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class ServiceProviderService
{
    public function updateOrCreateProvider(int $userId, array $data)
    {
        if (isset($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {
            $provider = ServiceProvider::where('user_id', $userId)->first();
            if ($provider && $provider->avatar) {
                Storage::disk('public')->delete($provider->avatar);
            }
                    $user = auth()->user();

$extension = $data['avatar']->getClientOriginalExtension();


$fileName = Str::slug($user->name) . '.' . $extension;

$data['avatar'] = $data['avatar']->storeAs(
    'avatars/providers',
    $fileName,
    'public'
);
     
        }

        return ServiceProvider::updateOrCreate(
            ['user_id' => $userId],
            $data
        );
    }

    public function getProviderByUserId(int $userId)
    {
        return ServiceProvider::with(['user', 'city.governorate'])->where('user_id', $userId)->first();
    }
}