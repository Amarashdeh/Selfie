<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        // Find user by ID from the route if not authenticated
        $user = $request->user() ?? User::find($request->route('id'));

        if (!$user) {
            abort(404, 'User not found');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard')->with('verified', 1);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // Log the user in if they are not already authenticated
        if (!Auth::check()) {
            Auth::login($user);
        }

        return redirect()->route('dashboard')->with('verified', 1);
    }
}
