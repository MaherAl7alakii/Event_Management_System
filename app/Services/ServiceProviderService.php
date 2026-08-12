<?php

namespace App\Services;

use App\Models\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\Portfolio;
use App\Models\ServiceProviderCategory;
use App\Models\ServiceProviderDocument;
use App\Models\Service;
use Illuminate\Support\Str;
class ServiceProviderService
{
    private WorkingHoursService $workingHoursService;

    public function __construct(WorkingHoursService $workingHoursService)
    {
        $this->workingHoursService = $workingHoursService;
    }
    public function updateOrCreateProvider(int $userId, array $data,bool $isCreate)
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
           $provider->categories()->sync($data["categories"]);
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

        // if($isCreate)
        //     $this->workingHoursService->createDefaultWorkingHours($provider);



        return $provider;
    }

    public function getProviderByUserId(int $userId)
    {
        return ServiceProvider::with(['user', 'city.governorate', 'categories', 'documents', 'portfolios','workingHours'])
->withAvg('reviews', 'rating')
->withCount('reviews')
->when(
    auth('api')->check(),
    function ($query) {
        $query->withExists([
            'favorites as is_favorite' => function ($q) {
                $q->where('user_id', auth('api')->id());
            },
        ]);
    }
)->where('user_id', $userId)->first();
    }
   
    public function getSetupProgress(): array
{
    $provider = $this->getProviderByUserId(auth()->id());

    if (!$provider) {
        return [
            'progress' => 0,
            'steps' => [],
        ];
    }

    $businessInformation =
        !empty($provider->business_name) &&
        !empty($provider->phone) &&
        !empty($provider->city_id) &&
        !empty($provider->address);

    $workingHours = $provider->workingHours()->exists();

    $firstService = Service::where(
        'provider_id',
        $provider->user_id
    )->exists();

    $portfolio = $provider->portfolios()->exists();

  $steps = [
    [
        'key' => 'business_information',
        'title' => 'Business Information',
        'completed' => $businessInformation,
    ],
    [
        'key' => 'working_hours',
        'title' => 'Working Hours',
        'completed' => $workingHours,
    ],
    [
        'key' => 'first_service',
        'title' => 'First Service',
        'completed' => $firstService,
    ],
    [
        'key' => 'portfolio',
        'title' => 'Portfolio',
        'completed' => $portfolio,
    ],
];

   $completed = collect($steps)
    ->where('completed', true)
    ->count();

$progress = (int) (($completed / count($steps)) * 100);

   return [
    'progress' => $progress,
    'completed_steps' => $completed,
    'total_steps' => count($steps),
    'steps' => $steps,
];
}
public function getProviderById(int $providerId)
{
    return ServiceProvider::with([
        'city.governorate',
        'categories',
        'workingHours',
    ])
        ->when(
            auth('api')->check(),
            function ($query) {
                $query->withExists([
                    'favorites as is_favorite' => function ($q) {
                        $q->where('user_id', auth('api')->id());
                    },
                ]);
            }
        )
        ->withAvg('reviews', 'rating')
        ->withCount('reviews')
        ->where('id', $providerId)
        ->where('approval_status', 'approved')
        ->first();
}
}
