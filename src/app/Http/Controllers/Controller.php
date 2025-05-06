<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function apiResponse(mixed $payload, int $code = 200): JsonResponse
    {
        return response()->json(
            [
                'data' => $payload,
            ],
            $code
        );
    }

    protected function apiResponsePages(mixed $payload, ?int $recordsTotal = null, int $code = 200): JsonResponse
    {
        $result = ['data' => $payload];

        if (!empty($recordsTotal)) {
            $result['recordsTotal'] = $recordsTotal;
        }

        return response()->json(
            $result,
            $code
        );
    }

    protected function apiError($message, $code = 500): JsonResponse
    {
        return response()->json(
            [
                'message' => $message,
            ],
            $code
        );
    }
}
