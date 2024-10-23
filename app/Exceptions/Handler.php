<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
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
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request   $request
     * @param Throwable $e
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($request->expectsJson()) {
            $status = match (true) {
                $e instanceof ValidationException => 422,
                $e instanceof NotFoundHttpException => 404,
                $e instanceof MethodNotAllowedHttpException => 405,
                $e instanceof AuthenticationException => 401,
                $e instanceof UnauthorizedException => 403,
                default => $this->isHttpException($e) ? $e->getStatusCode() : 500,
            };

            $response = match (true) {
                $e instanceof ValidationException => [
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ],
                $e instanceof NotFoundHttpException => [
                    'message' => 'Resource not found.',
                ],
                $e instanceof MethodNotAllowedHttpException => [
                    'message' => 'Method not allowed.',
                ],
                $e instanceof AuthenticationException => [
                    'message' => 'Unauthenticated.',
                ],
                $e instanceof UnauthorizedException => [
                    'message' => 'UnauthorizedException.',
                ],
                default => [
                    'message' => $e->getMessage(),
                ],
            };

            return response()->json($response, $status);
        }

        return parent::render($request, $e);
    }

}
