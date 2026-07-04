<?php

namespace App\Services;

use App\Models\Profile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileService
{
    public function updateOrCreateProfile(int $userId, array $data)
    {
        if (isset($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {
            $profile = Profile::where('user_id', $userId)->first();
            if ($profile && $profile->avatar) {
                Storage::disk('public')->delete($profile->avatar);
            }
           $user = auth()->user();

$extension = $data['avatar']->getClientOriginalExtension();


$fileName = Str::slug($user->name) . '.' . $extension;

$data['avatar'] = $data['avatar']->storeAs(
    'avatars/profiles',
    $fileName,
    'public'
);
        }

        return Profile::updateOrCreate(
            ['user_id' => $userId],
            $data
        );
    }

    public function getProfileByUserId(int $userId)
    {
        return Profile::with(['user', 'city.governorate'])->where('user_id', $userId)->first();
    }
}