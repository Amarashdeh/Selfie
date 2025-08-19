<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Http\Controllers\Controller;

class EmailVerificationPromptController extends Controller
{
    /**
     * Show the email verification prompt or redirect if already verified.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('user.login')->with('error', 'Please log in first.');
        }

        return $user->hasVerifiedEmail()
            ? redirect()->intended(route('dashboard'))
            : view('auth.verify-email');
    }

}
