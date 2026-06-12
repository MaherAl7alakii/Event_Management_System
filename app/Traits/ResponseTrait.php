<?php

namespace App\Traits;

trait ResponseTrait
{
    public function apiResponse($data = null, $message = null, $status = null)
    {
        $array = [
            'message' => $message,
            'status' => $status,
            'data' => $data,

        ];

        return response()->json($array, $status);
    }


}
