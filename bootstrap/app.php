<?php

use App\Http\Middleware\SetLanguage;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
//use Illuminate\Validation\UnauthorizedException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'setLanguage' => SetLanguage::class,
            'verified.email' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $e, $request) {
            return response()->json([
                'message' => $e->errors(),
                'status' => 422,
                'data' => null
            ], 422);
        });

        $exceptions->render(function (NotFoundHttpException $e, $request) {
            return response()->json([
                'message' => __('messages.exceptions.not_found'),
                'status'  => Response::HTTP_NOT_FOUND,
                'data'    => null
            ], Response::HTTP_NOT_FOUND);
        });


        $exceptions->render(function (UnauthorizedException $e, $request) {
            return response()->json([
                'message' => __('messages.exceptions.unauthorized'),
                'status'  => Response::HTTP_FORBIDDEN,
                'data'    => null
            ], Response::HTTP_FORBIDDEN);
        });


        $exceptions->render(function (AuthenticationException $e, $request) {
            return response()->json([
                'message' => __('messages.exceptions.unauthenticated'),
                'status'  => Response::HTTP_UNAUTHORIZED,
                'data'    => null
            ], Response::HTTP_UNAUTHORIZED);
        });


        $exceptions->render(function (AccessDeniedHttpException $e, $request) {
            return response()->json([
                'message' => __('messages.exceptions.access_denied'),
                'status'  => Response::HTTP_FORBIDDEN,
                'data'    => null
            ], Response::HTTP_FORBIDDEN);
        });


    })->create();
