<?php

use App\Http\Middleware\AdminAuth;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin.auth' => AdminAuth::class,
            'permission' => PermissionMiddleware::class,
            'Debugbar' => Debugbar::class,
        ]);
        $middleware->api([
            \Illuminate\Http\Middleware\HandleCors::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // $exceptions->render(function (Exception $exception, Request $request) {
        //     if ($exception instanceof NotFoundHttpException || $exception instanceof ErrorException) {
        //         if (Str::startsWith($request->path(), app('backend.prefix'))) {
        //             return response()->view('admin::errors.404', [], Response::HTTP_NOT_FOUND);
        //         } else {
        //             return response()->view('errors.404', [], Response::HTTP_NOT_FOUND);
        //         }
        //     }

        //     // Handle 500 error
        //     if ($exception instanceof HttpException && $exception->getStatusCode() === 500) {
        //         if (Str::startsWith($request->path(), app('backend.prefix'))) {
        //             return response()->view('admin::errors.500', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        //         } else {
        //             return response()->view('errors.500', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        //         }
        //     }

        //     // Handle MethodNotAllowedHttpException
        //     if ($exception instanceof MethodNotAllowedHttpException) {
        //         if (Str::startsWith($request->path(), app('backend.prefix'))) {
        //             return response()->view('admin::errors.404', [], Response::HTTP_METHOD_NOT_ALLOWED);
        //         } else {
        //             return response()->view('errors.404', [], Response::HTTP_METHOD_NOT_ALLOWED);
        //         }
        //     }

        //     // Handle ModelNotFoundException or BadMethodCallException
        //     if ($exception instanceof ModelNotFoundException || $exception instanceof BadMethodCallException) {
        //         if (Str::startsWith($request->path(), app('backend.prefix'))) {
        //             return response()->view('admin::errors.404', [], Response::HTTP_NOT_FOUND);
        //         } else {
        //             return response()->view('errors.404', [], Response::HTTP_NOT_FOUND);
        //         }
        //     }

        //     // Handle Symfony HttpException for status codes 419 and 403
        //     if ($exception instanceof HttpException) {
        //         $statusCode = $exception->getStatusCode();
        //         if ($statusCode === 419 || $statusCode === 403) {
        //             if (Str::startsWith($request->path(), app('backend.prefix'))) {
        //                 return response()->view('admin::errors.404', [], Response::HTTP_NOT_FOUND);
        //             } else {
        //                 return response()->view('errors.404', [], Response::HTTP_NOT_FOUND);
        //             }
        //         }
        //     }

        //     // Handle maintenance mode (503 Service Unavailable)
        //     if (app()->isDownForMaintenance()) {
        //         if (Str::startsWith($request->path(), app('backend.prefix'))) {
        //             return response()->view('admin::errors.503', [], Response::HTTP_SERVICE_UNAVAILABLE);
        //         } else {
        //             return response()->view('errors.503', [], Response::HTTP_SERVICE_UNAVAILABLE);
        //         }
        //     }
        // });
    })->create();
