<?php

namespace App\Services;

use App\Models\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\Portfolio;
use App\Models\ServiceProviderCategory;
use App\Models\ServiceProviderDocument;
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

        $provider = ServiceProvider::updateOrCreate(
            ["user_id" => $userId],
            $data
        );

        if (isset($data["categories"])) {
            $provider->categories()->delete();
            foreach ($data["categories"] as $categoryId) {
                $provider->categories()->create(["category_id" => $categoryId]);
            }
        }

        if (isset($data["documents"])) {
            $provider->documents()->delete();
            foreach ($data["documents"] as $documentUrl) {
                $provider->documents()->create(["url" => $documentUrl]);
            }
        }

        if (isset($data["portfolios"])) {
            $provider->portfolios()->delete();
            foreach ($data["portfolios"] as $portfolioItem) {
                $provider->portfolios()->create($portfolioItem);
            }
        }

        return $provider;
    }

    public function getProviderByUserId(int $userId)
    {
        return ServiceProvider::with(['user', 'city.governorate', 'categories.category', 'documents', 'portfolios', 'mainService'])->where('user_id', $userId)->first();
    }
}