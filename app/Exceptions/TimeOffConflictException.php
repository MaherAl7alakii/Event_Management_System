<?php

namespace App\Exceptions;


use App\Models\TimeOff;
use Exception;
use \App\Traits\ResponseTrait;


class TimeOffConflictException extends Exception
{
    use ResponseTrait;
    public function __construct(
        public readonly TimeOff $conflictingTimeOff,
    ) {
        parent::__construct(
            "This time range conflicts with an existing time off (#{$conflictingTimeOff->id})."
        );
    }

    public function render($request)
    {
        return $this->apiResponse(
            null,
            $this->getMessage(),
            409
        );
    }
}
