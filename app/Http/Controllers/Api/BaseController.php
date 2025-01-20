<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    /**
     * Send Json response
     *
     * @param array|string $data
     * @param string $message
     * @param string $field
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function sendResponse(array|string $data, string $message = "", int $statusCode = 200, string $field = "data"): JsonResponse
    {
        $response = [
            "message" => $message
        ];
        if (!empty($data)) {
            if (is_array($data)) {
                $response[$field] = $data;
            } else {
                $response[$field] = json_decode($data, true) ?? $data;
            }
        }
        return response()->json($response, boolval(preg_match('/^[1-5][0-9][0-9]$/', $statusCode)) ? $statusCode : 500);
    }
}
