<?php

namespace App\Http\Controllers;

use App\Http\Resources\SearchHistoryResource;
use App\Models\SearchHistory;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SearchHistoryController extends Controller
{
    use ResponseTrait;
    protected string $resourceName = 'messages.resources.search_history';


    public function index()
    {
        $history = auth()->user()->searchHistory()
            ->orderBy('updated_at', 'desc')->get();

        return $this->apiResponse(
            !$history->isEmpty() ? SearchHistoryResource::collection($history) : null,
            $history->isEmpty()
                ? __('messages.empty', ['resource' => __($this->resourceName)])
                : __('messages.fetched_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_OK
        );
    }

    public function destroy($id)
    {


        $deleted = auth()->user()->searchHistory()
            ->where('id', $id)
            ->delete();

        if (!$deleted) {
            throw new NotFoundHttpException;
        }

        return $this->apiResponse(
            null,
            __('messages.deleted_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_OK
        );
    }

    public function clearAll()
    {
        auth()->user()->searchHistory()->delete();

        return $this->apiResponse(
            null,
            __('messages.deleted_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_OK
        );
    }
}
