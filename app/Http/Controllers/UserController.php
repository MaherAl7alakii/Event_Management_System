<?php

namespace App\Http\Controllers;


use App\Http\Resources\User\CustomerIndexResource;
use App\Http\Resources\User\ProviderIndexResource;
use App\Models\User;
use App\Services\UserService;
use App\Traits\PaginationResponseTrait;
use App\Traits\ResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    use ResponseTrait;
    use PaginationResponseTrait;
    use AuthorizesRequests;
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    public function getCustomers(Request $request)
    {
        $customers = $this->userService->getCustomers($request->all());

        return $this->apiResponse(
            !$customers->isEmpty() ? [
                'pagination' => $this->formatPaginatedResponse($customers),
                'customers'  => CustomerIndexResource::collection($customers),
            ] : null,
            $customers->isEmpty()
                ? __('messages.empty', ['resource' => __('messages.resources.customers')])
                : __('messages.fetched_success', ['resource' => __('messages.resources.customers')]),
            Response::HTTP_OK
        );
    }


    public function getServiceProviders(Request $request)
    {
        $providers = $this->userService->getServiceProviders($request->all());

        return $this->apiResponse(
            !$providers->isEmpty() ? [
                'pagination'        => $this->formatPaginatedResponse($providers),
                'service_providers' => ProviderIndexResource::collection($providers),
            ] : null,
            $providers->isEmpty()
                ? __('messages.empty', ['resource' => __('messages.resources.service_providers')])
                : __('messages.fetched_success', ['resource' => __('messages.resources.service_providers')]),
            Response::HTTP_OK
        );
    }

    public function ban(User $user)
    {
        $this->authorize('ban', $user);
        $user->banned_at = $user->banned_at ==null ? now() : $user->banned_at ;
        $user->is_banned = true;
        $user->save();

        return $this->apiResponse(
            null,
            __('messages.user_banned_success'),
            Response::HTTP_OK
        );
    }


    public function unban(User $user)
    {
        $this->authorize('ban', $user);
        $user->banned_at = null;
        $user->is_banned = false;
        $user->save();

        return $this->apiResponse(
            null,
            __('messages.user_unbanned_success'),
            Response::HTTP_OK
        );
    }
}
