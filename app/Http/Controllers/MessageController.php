<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use App\Http\Resources\MessageReadResource;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\MessageService;
use App\Traits\ResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends Controller
{
    use ResponseTrait;
    use AuthorizesRequests;

    protected MessageService $messageService;

    protected string $resourceName = 'messages.resources.message';

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }


    public function index(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $messages = $this->messageService->getMessages(
            $conversation,
        );

        return $this->apiResponse(
            $messages->isEmpty() ? null : MessageResource::collection($messages),
            $messages->isEmpty()
                ? __('messages.empty', ['resource' => __('messages.resources.messages')])
                : __('messages.fetched_success', ['resource' => __('messages.resources.messages')]),
            Response::HTTP_OK
        );
    }


    public function store(StoreMessageRequest $request, Conversation $conversation)
    {
        $this->authorize('interact', $conversation);

        $message = $this->messageService->sendMessage(
            $conversation,
            $request->user(),
            $request->validated()
        );

        return $this->apiResponse(
            new MessageResource($message),
            __('messages.created_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_CREATED
        );
    }


    public function update(UpdateMessageRequest $request, Conversation $conversation, Message $message)
    {
        $this->authorize('view', $conversation);
        $this->authorize('update', $message);
        abort_if($message->conversation_id !== $conversation->id, Response::HTTP_NOT_FOUND);

        $updatedMessage = $this->messageService->updateMessage(
            $message,
            $request->validated('body')
        );

        return $this->apiResponse(
            new MessageResource($updatedMessage),
            __('messages.updated_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_OK
        );
    }


    public function destroy(Conversation $conversation, Message $message)
    {
        $this->authorize('view', $conversation);
        $this->authorize('delete', $message);
        abort_if($message->conversation_id !== $conversation->id, Response::HTTP_NOT_FOUND);

        $this->messageService->deleteMessage($conversation, $message);

        return $this->apiResponse(
            null,
            __('messages.deleted_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_OK
        );
    }


    public function markAsRead(Conversation $conversation)
    {
        $this->authorize('interact', $conversation);

        $result = $this->messageService->markAsRead($conversation, auth()->user());

        return $this->apiResponse(
            new MessageReadResource($result),
            __('messages.marked_read_success'),
            Response::HTTP_OK
        );
    }
}
