<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration form.
     */
    public function create()
    {
        return view('auth.register'); // your registration view
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Create user
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 🔹 Assign Parent role to new user
        $user->assignRole('Parent');

        // Fire registered event (sends verification email)
        event(new Registered($user));

        // Automatically log in the user
        Auth::login($user);

        // Redirect to verification page until they verify email
        if (!$user->hasVerifiedEmail()) {
            die('yes');
            return redirect()->route('verification.notice');
        }
            die('no');

        return redirect()->route('dashboard');
    }
}
