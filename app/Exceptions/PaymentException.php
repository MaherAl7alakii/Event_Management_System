<?php

namespace App\Exceptions;

use App\Traits\ResponseTrait;
use Exception;
use Throwable;
use Symfony\Component\HttpFoundation\Response;

class PaymentException extends Exception
{
    use ResponseTrait;

    public function __construct(
        string $message = "",
        int $code = Response::HTTP_UNPROCESSABLE_ENTITY,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }


    public function render($request)
    {
        $statusCode = $this->code
            ? $this->code
            : Response::HTTP_UNPROCESSABLE_ENTITY;

        return $this->apiResponse(
            null,
            $this->getMessage(),
            $statusCode
        );
    }
}
