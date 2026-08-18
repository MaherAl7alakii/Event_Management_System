<?php

namespace App\Services;

use App\Models\ServiceProvider;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\Portfolio;
use App\Models\ServiceProviderCategory;
use App\Models\ServiceProviderDocument;
use App\Models\Service;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ServiceProviderService
{

    public function __construct(
        private readonly WorkingHoursService $workingHoursService,
        private readonly StripeAccountService $stripeService,
    ) {
    }

    public function createProvider(int $userId, array $data): ServiceProvider
    {
        if (ServiceProvider::where('user_id', $userId)->exists()) {
            throw new InvalidArgumentException(__('messages.provider_already_exists'));
        }


        $user = User::findOrFail($userId);


        $stripeAccountId = $this->stripeService->createVerifiedAccount($user->name, $user->email);

        return DB::transaction(function () use ($userId, $data, $user, $stripeAccountId) {

            if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
                $extension = $data['avatar']->getClientOriginalExtension();
                $fileName = Str::slug($user->name) . '-' . time() . '.' . $extension;
                $data['avatar'] = $data['avatar']->storeAs('avatars/providers', $fileName, 'public');
            }

            $data['user_id'] = $userId;
            $data['stripe_account_id'] = $stripeAccountId;

            $provider = ServiceProvider::create($data);


            if (isset($data['categories'])) {
                $provider->categories()->sync($data['categories']);
            }

            if (isset($data['documents'])) {
                foreach ($data['documents'] as $documentUrl) {
                    $provider->documents()->create(['url' => $documentUrl]);
                }
            }

            if (isset($data['portfolios'])) {
                foreach ($data['portfolios'] as $portfolioItem) {
                    $provider->portfolios()->create($portfolioItem);
                }
            }

            $this->workingHoursService->createDefaultWorkingHours($provider);

            return $provider;
        });
    }


    public function updateProvider(ServiceProvider $provider, array $data): ServiceProvider
    {
        return DB::transaction(function () use ($provider, $data) {

            if (isset($data['first_name']) || isset($data['last_name'])) {
            $user = User::findOrFail($provider->user_id);

                $currentName = explode(' ', trim($user->name), 2);

                $firstName = $data['first_name']?? ($currentName[0] ?? '');

                $lastName = $data['last_name']?? ($currentName[1] ?? '');

                $user->update(['name' => trim($firstName . ' ' . $lastName),]);

                unset($data['first_name'],$data['last_name']);
            }

            if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
                if ($provider->avatar) {
                    Storage::disk('public')->delete($provider->avatar);
                }

                $user = auth()->user();
                $extension = $data['avatar']->getClientOriginalExtension();
                $fileName = Str::slug($user->name) . '-' . time() . '.' . $extension;
                $data['avatar'] = $data['avatar']->storeAs('avatars/providers', $fileName, 'public');
            }

            $provider->update($data);


            if (isset($data['categories'])) {
                $provider->categories()->sync($data['categories']);
            }

            if (isset($data['documents'])) {
                $provider->documents()->delete();
                foreach ($data['documents'] as $documentUrl) {
                    $provider->documents()->create(['url' => $documentUrl]);
                }
            }

            if (isset($data['portfolios'])) {
                $provider->portfolios()->delete();
                foreach ($data['portfolios'] as $portfolioItem) {
                    $provider->portfolios()->create($portfolioItem);
                }
            }

            return $provider;
        });
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
        $user = auth('api')->user();

        $isAdmin = $user && ($user->hasRole('admin') );

        return ServiceProvider::with([
            'city.governorate',
            'categories',
            'workingHours',
        ])
            ->when(
                $user !== null,
                function ($query) use ($user) {
                    $query->withExists([
                        'favorites as is_favorite' => function ($q) use ($user) {
                            $q->where('user_id', $user->id);
                        },
                    ]);
                }
            )
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('id', $providerId)
            ->when(!$isAdmin, function ($query) {
                $query->where('approval_status', 'approved');
            })
            ->first();
    }


    public function changePassword(int $userId, string $oldPassword, string $newPassword): void
    {
        $user = User::findOrFail($userId);

        if (!Hash::check($oldPassword, $user->password)) {
            throw new HttpException(
                422,
                'The old password is incorrect.'
            );
        }

        $user->update([
            'password' => Hash::make($newPassword),
        ]);
    }


}
