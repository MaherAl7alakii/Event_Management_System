<?php

namespace App\Exceptions;

use Exception;

class NoDraftBookingsToSubmitException extends Exception
{

    public function render($request)
    {
        return response()->json([
            'message' => $this->getMessage(),
            'status' => '422',
            'data' => null
        ], 422);
    }
}
