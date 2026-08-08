<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Services\NotificationService;
use App\Traits\ResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{
    use ResponseTrait;
    use AuthorizesRequests;

    protected string $resourceName  = 'messages.resources.notification';
    protected string $resourcesName = 'messages.resources.notifications';

    public function __construct(
        private readonly NotificationService $notifications,
    ) {
    }


    public function index()
    {
        $notificationsList = $this->notifications->getNotifications(auth()->user());

        return $this->apiResponse(
            !$notificationsList->isEmpty() ? NotificationResource::collection($notificationsList) : null,
            $notificationsList->isEmpty()
                ? __('messages.empty', ['resource' => __($this->resourcesName)])
                : __('messages.fetched_success', ['resource' => __($this->resourcesName)]),
            Response::HTTP_OK
        );
    }


    public function show(Notification $notification)
    {
//        $notification = Notification::query()->where('id' , 16)->first();
        $this->authorize('view', $notification);

        $notificationData = $this->notifications->readWithSubject($notification);

        return $this->apiResponse(
            $notificationData,
            __('messages.fetched_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_OK
        );
    }


    public function markAllRead()
    {
        $this->notifications->markAllAsRead(auth()->user());

        return $this->apiResponse(
            null,
            __('messages.marked_all_read_success', ['resource' => __($this->resourcesName)]),
            Response::HTTP_OK
        );
    }


    public function destroy(Notification $notification)
    {
        $this->authorize('delete', $notification);

        $this->notifications->deleteNotification($notification);

        return $this->apiResponse(
            null,
            __('messages.deleted_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_OK
        );
    }


    public function destroyAll()
    {
        $this->notifications->deleteAllNotifications(auth()->user());

        return $this->apiResponse(
            null,
            __('messages.deleted_all_success', ['resource' => __($this->resourcesName)]),
            Response::HTTP_OK
        );
    }
}
