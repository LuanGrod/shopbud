<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(mixed $data = null, ?string $message = null, int $status = 200, array $headers = []): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status, $headers);
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    public static function error(string $message, int $status, array $extra = [], array $headers = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            ...$extra,
        ], $status, $headers);
    }

    /**
     * @return array{success: bool, message: ?string}
     */
    public static function resourceMeta(?string $message = null): array
    {
        return [
            'success' => true,
            'message' => $message,
        ];
    }
}
