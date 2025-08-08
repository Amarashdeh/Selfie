<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Exceptions\Handler;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([

            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'teacher' => \App\Http\Middleware\TeacherMiddleware::class,
            'moderator' => \App\Http\Middleware\ModeratorMiddleware::class,
            'parent' => \App\Http\Middleware\ParentMiddleware::class,


            'prevent-back-history' => \App\Http\Middleware\PreventBackHistory::class,

            'auth' => \App\Http\Middleware\Authenticate::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            

        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
    })->create();
