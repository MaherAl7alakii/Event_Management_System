<?php

namespace App\Http\Controllers;

use App\Http\Requests\TimeOffRequest;
use App\Http\Requests\WorkingHoursRequest;
use App\Http\Resources\CalendarDayViewResource;
use App\Http\Resources\TimeOffResource;
use App\Http\Resources\WorkingHourResource;
use App\Models\ServiceProvider;
use App\Models\TimeOff;
use App\Services\CalendarService;
use App\Services\WorkingHoursService;
use App\Traits\ResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CalendarController extends Controller
{
    use ResponseTrait;
    use AuthorizesRequests;

    protected string $workingHoursResourceName = 'messages.resources.working_hours';
    protected string $calendarResourceName = 'messages.resources.calendar';
    protected string $timeOffResourceName = 'messages.resources.time_off';

    public function __construct(
        private readonly WorkingHoursService $workingHoursService,
        private readonly CalendarService $calendarService,
    ) {
    }


    public function getWorkingHours(): JsonResponse
    {
        $provider = auth()->user()->serviceProvider;

        $workingHours = $provider->workingHours()->orderBy('day_of_week')->get();

        return $this->apiResponse(
            $workingHours->isNotEmpty() ? ['days' => WorkingHourResource::collection($workingHours)] : null,
            $workingHours->isEmpty()
                ? __('messages.empty', ['resource' => __($this->workingHoursResourceName)])
                : __('messages.fetched_success', ['resource' => __($this->workingHoursResourceName)]),
            Response::HTTP_OK
        );
    }


    public function getWorkingHoursByProvider(ServiceProvider $provider): JsonResponse
    {
        $workingHours = $provider->workingHours()->orderBy('day_of_week')->get();

        return $this->apiResponse(
            $workingHours->isNotEmpty() ? ['service_provider_id' => $provider->id,'days' => WorkingHourResource::collection($workingHours)] : null,
            $workingHours->isEmpty()
                ? __('messages.empty', ['resource' => __($this->workingHoursResourceName)])
                : __('messages.fetched_success', ['resource' => __($this->workingHoursResourceName)]),
            Response::HTTP_OK
        );
    }


    public function updateWorkingHours(WorkingHoursRequest $request): JsonResponse
    {
        $provider = auth()->user()->serviceProvider;

        $workingHours = $this->workingHoursService->updateWorkingHours($provider, $request->validated('days'));

        return $this->apiResponse(
            ['days' => WorkingHourResource::collection($workingHours)],
            __('messages.updated_success', ['resource' => __($this->workingHoursResourceName)]),
            Response::HTTP_OK
        );
    }


    public function getDayView(Request $request): JsonResponse
    {
        $provider = auth()->user()->serviceProvider;

        $validated = $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
        ]);

        $dayData = $this->calendarService->getDayView($provider, $validated['date']);

        return $this->apiResponse(
            new CalendarDayViewResource($dayData),
            __('messages.fetched_success', ['resource' => __($this->calendarResourceName)]),
            Response::HTTP_OK
        );
    }


    public function getMonthOverview(Request $request): JsonResponse
    {
        $provider = auth()->user()->serviceProvider;

        $validated = $request->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year'  => ['required', 'integer', 'digits:4'],
        ]);

        $overviewData = $this->calendarService->getMonthOverview(
            $provider,
            (int) $validated['month'],
            (int) $validated['year']
        );

        return $this->apiResponse(
            $overviewData ?: null,
            empty($overviewData)
                ? __('messages.empty', ['resource' => __($this->calendarResourceName)])
                : __('messages.fetched_success', ['resource' => __($this->calendarResourceName)]),
            Response::HTTP_OK
        );
    }



    public function showTimeOff(TimeOff $timeOff): JsonResponse
    {
        $this->authorize('view', $timeOff);

        return $this->apiResponse(
            new TimeOffResource($timeOff),
            __('messages.fetched_success', ['resource' => __($this->timeOffResourceName)]),
            Response::HTTP_OK
        );
    }


    public function storeTimeOff(TimeOffRequest $request): JsonResponse
    {
        $provider = auth()->user()->serviceProvider;

        $timeOff = $this->workingHoursService->createTimeOff($provider, $request->validated());

        return $this->apiResponse(
            new TimeOffResource($timeOff),
            __('messages.created_success', ['resource' => __($this->timeOffResourceName)]),
            Response::HTTP_CREATED
        );
    }


    public function updateTimeOff(TimeOffRequest $request, TimeOff $timeOff): JsonResponse
    {
        $this->authorize('update', $timeOff);

        $result = $this->workingHoursService->updateTimeOff($timeOff, $request->validated());

        return $this->apiResponse(
            new TimeOffResource($result),
            __('messages.updated_success', ['resource' => __($this->timeOffResourceName)]),
            Response::HTTP_OK
        );
    }


    public function destroyTimeOff(TimeOff $timeOff): JsonResponse
    {
        $this->authorize('delete', $timeOff);

        $this->workingHoursService->deleteTimeOff($timeOff);

        return $this->apiResponse(
            null,
            __('messages.deleted_success', ['resource' => __($this->timeOffResourceName)]),
            Response::HTTP_OK
        );
    }

}
