<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (!Auth::guard($guard)->check()) {

                return match ($guard) {
                    'admin' => redirect()->route('admin.login'),
                    default => redirect()->route('user.login'),
                };
            }
            
        }

        return $next($request);
    }
}
