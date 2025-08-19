<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Twilio\Rest\Client;

class ProfileController extends Controller
{
    public function myProfile()
    {
        $user = Auth::user();
        return view('admin.profile.my_profile', compact('user'));
    }

    public function editProfile()
    {
        $user = Auth::user();
        return view('admin.profile.edit_profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = Auth::user();
        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        return response()->json(['success' => true, 'message' => 'Profile updated successfully']);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Current password is incorrect'], 422);
        }

        $user->update(['password' => Hash::make($request->new_password)]);
        return response()->json(['success' => true, 'message' => 'Password updated successfully']);
    }
    
    public function changeEmail(Request $request)
    {
        $request->validate([
            'new_email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) {
                    if (Admin::where('email', $value)->exists() || User::where('email', $value)->exists()) {
                        $fail('The email is already taken by another account.');
                    }
                },
            ],
        ]);

        $user = auth('admin')->user();
        $user->pending_email = $request->new_email;
        $user->email_verification_token = Str::random(60);
        $user->save();

        // Send verification email
        Mail::send('emails.verify-new-email', ['user' => $user], function($m) use ($user) {
            $m->to($user->pending_email)->subject('Verify Your New Email');
        });

        return response()->json(['message' => 'Verification email sent to new email address.']);
    }


    public function verifyNewEmail($id, $token)
    {
        $user = Admin::where('id', $id)
            ->where('email_verification_token', $token)
            ->firstOrFail();

        $user->email = $user->pending_email;
        $user->pending_email = null;
        $user->email_verification_token = null;
        $user->save();

        return redirect()->route('admin.profile')->with('success', 'Email verified and updated successfully!');
    }

    /**
     * Request phone change: save pending phone, create code, send SMS via Twilio.
     */
    public function requestPhoneChange(Request $request)
    {
        $request->validate([
            'phone' => [
                'required',
                // custom rule: unique across admins and users
                function ($attribute, $value, $fail) {
                    if (Admin::where('phone', $value)->exists() || User::where('phone', $value)->exists()) {
                        $fail('The phone number is already in use.');
                    }
                },
            ],
        ]);

        $admin = Auth::guard('admin')->user();

        // optional: throttle - don't allow a new code if previous one is still valid
        if ($admin->phone_verification_expires_at && Carbon::now()->lt($admin->phone_verification_expires_at)) {
            $remaining = Carbon::now()->diffInSeconds($admin->phone_verification_expires_at);
            return response()->json(['message' => 'Please wait before requesting another code.'], 429);
        }

        // generate 6-digit code and hash it
        $code = random_int(100000, 999999);
        $hashed = Hash::make((string)$code);

        $admin->pending_phone = $request->phone;
        $admin->phone_verification_code = $hashed;
        $admin->phone_verification_expires_at = Carbon::now()->addMinutes(3);
        $admin->save();

        // Send SMS via Twilio
        try {
            $twilioSid = config('services.twilio.twilio_sid');
            $twilioToken = config('services.twilio.twilio_auth_token');
            $twilioFrom = config('services.twilio.twilio_from');

            $twilio = new Client($twilioSid, $twilioToken);
            $message = "Your verification code: {$code}. It expires in 3 minutes.";

            $twilio->messages->create($admin->pending_phone, [
                'from' => $twilioFrom,
                'body' => $message,
            ]);

        } catch (\Throwable $e) {
            // rollback code fields if SMS fails
            $admin->pending_phone = null;
            $admin->phone_verification_code = null;
            $admin->phone_verification_expires_at = null;
            $admin->save();

            // log error and inform user
            \Log::error('Twilio SMS error: '.$e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Verification code sent to the new phone number.']);
    }

    /**
     * Verify phone change code and finalize phone update.
     */
    public function verifyPhoneChange(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|digits:6',
        ]);

        $admin = Auth::guard('admin')->user();

        if (! $admin->phone_verification_code || ! $admin->pending_phone) {
            return response()->json(['message' => 'No phone change requested.'], 422);
        }

        // check expiry
        if (Carbon::now()->gt($admin->phone_verification_expires_at)) {
            // cleanup expired attempt
            $admin->pending_phone = null;
            $admin->phone_verification_code = null;
            $admin->phone_verification_expires_at = null;
            $admin->save();

            return response()->json(['message' => 'Verification code expired. Please request a new code.'], 422);
        }

        // verify code (we stored hashed)
        if (! Hash::check($request->verification_code, $admin->phone_verification_code)) {
            return response()->json(['message' => 'Invalid verification code.'], 422);
        }

        // Unique check again (race condition)
        if (Admin::where('phone', $admin->pending_phone)->where('id','!=',$admin->id)->exists()
            || User::where('phone', $admin->pending_phone)->exists()) {
            // cleanup
            $admin->pending_phone = null;
            $admin->phone_verification_code = null;
            $admin->phone_verification_expires_at = null;
            $admin->save();
            return response()->json(['message' => 'Phone number no longer available.'], 422);
        }

        // Apply new phone
        $admin->phone = $admin->pending_phone;
        $admin->pending_phone = null;
        $admin->phone_verification_code = null;
        $admin->phone_verification_expires_at = null;
        $admin->save();

        return response()->json(['message' => 'Phone number updated successfully.']);
    }

}
