<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
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
        RateLimiter::for("global", function (Request $request) {
            return Limit::perMinute(60)
                ->by($request->user()?->id ?: $request->ip())
                ->response(
                    function (Request $request, array $headers) {
                        $retryAfter = $headers["Retry-After"];
                        return response()
                            ->json([
                                "success" => false,
                                "msg" => "Você excedeu o limite de requisições, tente novamente mais tarde.",
                                "retry_after_seconds" => $retryAfter
                            ], 429, $headers);
                    }
                );
        });
        RateLimiter::for("login", function (Request $request) {
            return [
                Limit::perMinute(10),
                Limit::perMinute(3)->by($request->input("email"))
            ];
        });
    }
}
