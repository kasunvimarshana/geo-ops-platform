<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        // List of exception types that should not be reported
    ];

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function render($request, Exception $exception)
    {
        if ($request->expectsJson()) {
            return $this->handleJsonException($exception);
        }

        return parent::render($request, $exception);
    }

    protected function handleJsonException(Exception $exception): JsonResponse
    {
        $statusCode = $this->getStatusCode($exception);
        $response = [
            'error' => [
                'message' => $exception->getMessage(),
                'code' => $statusCode,
            ],
        ];

        return new JsonResponse($response, $statusCode);
    }

    protected function getStatusCode(Exception $exception): int
    {
        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return Response::HTTP_NOT_FOUND;
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }
}