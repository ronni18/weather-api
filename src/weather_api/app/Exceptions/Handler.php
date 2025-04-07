<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function render($request, Throwable $exception)
{
    // Si es una API y la excepción es por falta de autorización
    if ($exception instanceof AuthorizationException) {
        return response()->json([
            'message' => __('messages.unauthorized'),
            'status' => 403
        ], 403);
    }

    return parent::render($request, $exception);
}
}
