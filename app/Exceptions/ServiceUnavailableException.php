<?php

namespace App\Exceptions;
use App\Models\Service;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ServiceUnavailableException extends Exception
{
    use ResponseTrait;

    public function __construct(
        public readonly string $reason,
        public readonly int $serviceId,
    ) {
        parent::__construct($this->getTranslatedMessage($reason));
    }

    public static function forReason(Service $service, string $reason): self
    {
        return new self($reason, $service->id);
    }


    public function render(Request $request): JsonResponse
    {
        return $this->apiResponse(
            null,
             $this->getMessage(),
             409
        );
    }


    private function getTranslatedMessage(string $reason): string
    {
        $key = "messages.exceptions.service_availability.{$reason}";

        return trans()->has($key)
            ? __($key)
            : __('messages.exceptions.service_availability.default');
    }
}
