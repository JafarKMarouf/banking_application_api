<?php

namespace App\Exceptions;

use App\Http\Response\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
     * @param mixed $request
     * @param \Throwable $e
     * @return \Illuminate\Http\JsonResponse|\App\Http\Response\Response
     */
    public function render($request, Throwable $e): JsonResponse|Response
    {
        if ($request->expectsJson()) {
            Log::error($e);
            if ($e instanceof ValidationException) {
                $status_code =
                    HttpFoundationResponse::HTTP_UNPROCESSABLE_ENTITY;
                return Response::validation(
                    $e->errors(),
                    $status_code
                );
            }

            if ($e instanceof ModelNotFoundException) {
                $status_code =
                    HttpFoundationResponse::HTTP_NOT_FOUND;
                return Response::error(
                    'Recource could not be found',
                    $status_code,
                );
            }

            if ($e instanceof UniqueConstraintViolationException) {
                $status_code =
                    HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR;
                return Response::error(
                    'Duplicate entry found',
                    $status_code
                );
            }
            if ($e instanceof QueryException) {
                $status_code =
                    HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR;
                return Response::error(
                    'Could not execute query',
                    $status_code
                );
            }
            if ($e instanceof AuthenticationException) {
                $status_code =  HttpFoundationResponse::HTTP_UNAUTHORIZED;
                return Response::error(
                    $e->getMessage(),
                    $status_code
                );
            }

            if ($e instanceof \Exception) {
                $status_code =
                    HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR;

                return Response::error(
                    'We could not handle your request, please try again later',
                    $status_code,
                );
            }
            if ($e instanceof \Error) {
                $status_code =
                    HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR;

                return Response::error(
                    'We could not handle your request, please try again later',
                    $status_code,
                );
            }
        }
        return parent::render($request, $e);
    }
}
