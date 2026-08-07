<?php

namespace App\Http\Controllers;

use App\Http\Requests\TypingRequest;
use App\Models\Conversation;
use App\Services\TypingService;
use App\Traits\ResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Symfony\Component\HttpFoundation\Response;

class TypingController extends Controller
{
    use ResponseTrait;
    use AuthorizesRequests;

    protected TypingService $typingService;

    public function __construct(TypingService $typingService)
    {
        $this->typingService = $typingService;
    }


    public function __invoke(TypingRequest $request, Conversation $conversation)
    {
        $this->authorize('interact', $conversation);

        $this->typingService->broadcastTyping(
            $conversation,
            $request->user(),
            $request->validated('is_typing')
        );

        return $this->apiResponse(
            null,
            __('messages.typing_broadcasted'),
            Response::HTTP_OK
        );
    }
}
