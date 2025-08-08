<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = Auth::guard('admin')->user();

        if ($admin && ($admin->hasRole('Admin') || $admin->hasRole('SuperAdmin'))) {
            return $next($request);
        }

        abort(403, 'Unauthorized (Admin only)');
    }
}
