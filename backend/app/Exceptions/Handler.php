<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        // Handle API requests
        if ($request->is('api/*') || $request->expectsJson()) {
            return $this->handleApiException($request, $exception);
        }

        return parent::render($request, $exception);
    }

    protected function handleApiException($request, Throwable $exception)
    {
        $statusCode = 500;
        $message = 'Internal server error';
        $errors = null;

        if ($exception instanceof AuthenticationException) {
            $statusCode = 401;
            $message = 'Unauthenticated.';
        } elseif ($exception instanceof ModelNotFoundException) {
            $statusCode = 404;
            $message = 'Resource not found';
        } elseif ($exception instanceof NotFoundHttpException) {
            $statusCode = 404;
            $message = 'Endpoint not found';
        } elseif ($exception instanceof UnauthorizedHttpException) {
            $statusCode = 401;
            $message = 'Unauthorized';
        } elseif ($exception instanceof ValidationException) {
            $statusCode = 422;
            $message = 'Validation failed';
            $errors = $exception->errors();
        } elseif (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();
            $message = $exception->getMessage();
        } elseif ($exception instanceof \Exception) {
            $message = $exception->getMessage();
        }

        // Don't expose internal errors in production
        if (!config('app.debug') && $statusCode === 500) {
            $message = 'An unexpected error occurred';
        }

        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        // Include trace in development
        if (config('app.debug')) {
            $response['trace'] = $exception->getTraceAsString();
        }

        return response()->json($response, $statusCode);
    }
}
