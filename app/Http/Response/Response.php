<?php

namespace App\Http\Response;

use Illuminate\Http\JsonResponse;

class Response
{
    public static function success($data, $message, $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $code);
    }

    public static function error($message, $code = 500): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data' => [],
            'message' => $message,
        ], $code);
    }

    public static function validation($message, $code = 422): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data' => [],
            'message' => $message,
        ], $code);
    }
}
