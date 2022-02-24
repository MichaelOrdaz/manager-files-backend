<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Spatie\Permission\Exceptions\UnauthorizedException;
use App\Exceptions\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\ModelNotFoundException;
use Illuminate\Validation\ValidationException;


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
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    // public function register()
    // {
    //     $this->reportable(function (Throwable $e) {
    //         //
    //     });
    // }


    public function register()
    {

        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'errors' => ['Registro no encontrado'],
                    'debug' => $e->getMessage(),
                    'success' => false
                ], 404);
            }
        });

        $this->renderable(function (AuthorizationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'errors' => ['No tienes autorización suficiente para este recurso'],
                    'success' => false
                ], 401);
            }
        });

        $this->renderable(function (ModelNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'errors' => ['El recurso no fue encontrado'],
                    'success' => false
                ], 403);
            }
        });

        $this->renderable(function (AccessDeniedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'errors' => $e->getMessage(),
                    'success' => false
                ], 403);
            }
        });

        $this->renderable(function (ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'errors' => $e->errors(),
                    'success' => false
                ], 422);
            }
        });

        $this->renderable(function (Throwable $e, $request) {
          if ($request->is('api/*')) {
            return response()->json([
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'errors' => $e->getMessage(),
                'class' => get_class($e),
                'success' => false
            ], 500);
          }
        });

      }

}
