<?php

namespace App\Providers;

use App\Support\ApiResponse;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $tooManyRequestsResponse = function (Request $request, array $headers): JsonResponse {
            return ApiResponse::error('Muitas tentativas. Aguarde um pouco antes de tentar novamente.', 429, [
                'retry_after_seconds' => (int) $headers['Retry-After'],
            ], $headers);
        };

        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(60)
                ->by($request->user()?->id ?: $request->ip())
                ->response(
                    function (Request $request, array $headers): JsonResponse {
                        return ApiResponse::error('Muitas requisições. Aguarde um pouco antes de tentar novamente.', 429, [
                            'retry_after_seconds' => (int) $headers['Retry-After'],
                        ], $headers);
                    },
                );
        });
        RateLimiter::for('login', function (Request $request) use ($tooManyRequestsResponse) {
            return [
                Limit::perMinute(10)->response($tooManyRequestsResponse),
                Limit::perMinute(3)->by($request->input('email'))->response($tooManyRequestsResponse),
            ];
        });
    }
}
