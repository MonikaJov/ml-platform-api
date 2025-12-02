<?php

use App\Exceptions\NotFound;
use App\Http\Middleware\EnsureApiTokenIsValidMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        //        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'token' => EnsureApiTokenIsValidMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        if (! app()->environment(['staging', 'local'])) {
            //            $exceptions->dontReport(TypeError::class);
            //            $exceptions->render(function (TypeError $e) {
            //                throw new TypeErrorException();
            //            });
            $exceptions->dontReport(NotFoundHttpException::class);
            $exceptions->render(function (NotFoundHttpException $e) {
                throw new NotFound;
            });
        }
    })->create();
