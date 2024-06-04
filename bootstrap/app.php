<?php

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //middleware Aliases

        $middleware->alias([
            'CheckRole' => CheckRole::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {

            if ($request->is('api/*')) 
            {
                $modelName = Str::afterLast($e->getPrevious()->getModel(), '\\');
                $id = implode(', ', $e->getPrevious()->getIds());

                return response()->streamJson([
                    'message' => "{$modelName} with id [{$id}] not found"
                ], 404);
            }
        });
        
    })->create();
