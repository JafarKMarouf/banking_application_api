<?php

namespace App\Exceptions;

use App\Http\Response\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        Log::error($e);

        if ($e instanceof MethodNotAllowedHttpException) {
            $status_code = HttpFoundationResponse::HTTP_METHOD_NOT_ALLOWED;
            return Response::error($e->getMessage(), $status_code);
        }


        if ($e instanceof NotFoundHttpException) {
            $status_code = HttpFoundationResponse::HTTP_NOT_FOUND;
            return Response::error($e->getMessage(), $status_code);
        }

        if ($request->expectsJson()) {

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
                    'Unauthenticated or Token Expired, please try to login again',
                    $status_code
                );
            }
            if ($e instanceof PinHasAlreadyBeenSet) {
                $status_code = HttpFoundationResponse::HTTP_BAD_REQUEST;
                return Response::error('Pin has already been set', $status_code);
            }
            if ($e instanceof NotSetupPin) {
                $status_code = HttpFoundationResponse::HTTP_UNAUTHORIZED;
                return Response::error(
                    'You have not set PIN yet!, Please setup your PIN',
                    $status_code
                );
            }
            if ($e instanceof AccountNumberExistsException) {
                $status_code = HttpFoundationResponse::HTTP_BAD_REQUEST;
                return Response::error(
                    'Account number has already been generated!',
                    $status_code
                );
            }
            if ($e instanceof InvaildAccountNumberException) {
                $status_code = HttpFoundationResponse::HTTP_NOT_FOUND;
                return Response::error(
                    'Invaild your account number',
                    $status_code
                );
            }
            if ($e instanceof InvaildPinException) {
                $status_code = HttpFoundationResponse::HTTP_BAD_REQUEST;
                return Response::error(
                    'invaild pin!',
                    $status_code
                );
            }


            // if ($e instanceof AmountToLowException) {
            //     $status_code = HttpFoundationResponse::HTTP_BAD_REQUEST;
            //     return Response::error(
            //         "amount must be equal or greater than 500",
            //         $status_code
            //     );
            // }
            if ($e instanceof NotEnoughBalanceException) {
                $status_code = HttpFoundationResponse::HTTP_FORBIDDEN;
                return Response::error(
                    "your balance don't enough!",
                    $status_code
                );
            }

            if ($e instanceof AuthorizationException) {
                $status_code = HttpFoundationResponse::HTTP_UNAUTHORIZED;
                return Response::error($e->getMessage(), $status_code);
            }
            if ($e instanceof \Exception) {
                $status_code =
                    HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR;

                return Response::error(
                    $e->getMessage(),
                    $status_code,
                );
            }
            if ($e instanceof \Error) {
                $status_code =
                    HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR;

                return Response::error(
                    $e->getMessage(),
                    $status_code,
                );
            }
        }

        return parent::render($request, $e);
    }
}
