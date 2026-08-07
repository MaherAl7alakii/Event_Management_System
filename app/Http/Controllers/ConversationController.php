<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConversationRequest;
use App\Http\Resources\ConversationResource;
use App\Models\Conversation;
use App\Services\ConversationService;
use App\Traits\ResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class ConversationController extends Controller
{
    use ResponseTrait;
    use AuthorizesRequests;

    protected ConversationService $conversationService;

    protected string $resourceName = 'messages.resources.conversation';

    public function __construct(ConversationService $conversationService)
    {
        $this->conversationService = $conversationService;
    }


    public function index()
    {
        $conversations = $this->conversationService->getAllConversations(
            auth()->user()
        );

        return $this->apiResponse(
            $conversations->isEmpty() ? null : ConversationResource::collection($conversations),
            $conversations->isEmpty()
                ? __('messages.empty', ['resource' => __('messages.resources.conversations')])
                : __('messages.fetched_success', ['resource' => __('messages.resources.conversations')]),
            Response::HTTP_OK
        );
    }


    public function findOrCreate(ConversationRequest $request)
    {

        $conversation = $this->conversationService->findOrCreateConversation(
            auth()->user(),
            $request->input('user_id')
        );
        return $this->apiResponse(
            new ConversationResource($conversation),
            __('messages.created_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_OK
        );
    }


    public function show(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $conversationData = $this->conversationService->getConversationById($conversation);

        return $this->apiResponse(
            new ConversationResource($conversationData),
            __('messages.fetched_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_OK
        );
    }
}
