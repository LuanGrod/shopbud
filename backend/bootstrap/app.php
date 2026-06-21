<?php

use App\Http\Middleware\ForceJsonResponse;
use App\Support\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            ForceJsonResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $exception): bool {
            return $request->is('api/*') || $request->expectsJson();
        });

        $exceptions->render(function (ValidationException $exception, Request $request) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            return ApiResponse::error('Os dados enviados são inválidos.', $exception->status, [
                'errors' => $exception->errors(),
            ]);
        });

        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            return ApiResponse::error('Autenticação necessária.', 401);
        });

        $exceptions->render(function (AuthorizationException $exception, Request $request) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            return ApiResponse::error('Você não tem permissão para acessar este recurso.', 403);
        });

        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            return ApiResponse::error('Recurso não encontrado.', 404, headers: $exception->getHeaders());
        });

        $exceptions->render(function (MethodNotAllowedHttpException $exception, Request $request) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            return ApiResponse::error('Método HTTP não permitido para este recurso.', 405, headers: $exception->getHeaders());
        });

        $exceptions->render(function (HttpExceptionInterface $exception, Request $request) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            $statusCode = $exception->getStatusCode();
            $message = match ($statusCode) {
                401 => 'Autenticação necessária.',
                403 => 'Você não tem permissão para acessar este recurso.',
                404 => 'Recurso não encontrado.',
                405 => 'Método HTTP não permitido para este recurso.',
                409 => 'Conflito ao processar a requisição.',
                410 => 'Recurso indisponível.',
                419 => 'Sessão expirada. Faça login novamente.',
                429 => 'Muitas tentativas. Aguarde um pouco antes de tentar novamente.',
                default => 'Não foi possível processar a requisição.',
            };
            $extra = [];

            if ($statusCode === 429 && isset($exception->getHeaders()['Retry-After'])) {
                $extra['retry_after_seconds'] = (int) $exception->getHeaders()['Retry-After'];
            }

            return ApiResponse::error($message, $statusCode, $extra, $exception->getHeaders());
        });
    })->create();
