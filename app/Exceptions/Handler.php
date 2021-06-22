<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $throwable
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $throwable)
    {
        if ($request->wantsJson() && $throwable instanceof ValidationException) {
            $message = [
                'status' => 'error',
                'message' => $throwable->getMessage(),
                'errors' => $throwable->errors()
            ];
            if (config('app.debug')) {
                array_push($message, ['stacktrace' => $throwable->getTrace()]);
            }

            return response()->json($message, JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        return parent::render($request, $throwable);
    }

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
