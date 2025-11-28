<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Notifications\SendOtpNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class OtpVerificationController extends Controller
{
    public function show(Request $request)
    {
        if ($request->user()->approval_status === 'approved') {
            return redirect()->route('dashboard');
        }

        return view('auth.verify-otp');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();
        $otp = OtpCode::where('user_id', $user->id)
            ->where('code', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (! $otp) {
            throw ValidationException::withMessages([
                'otp' => __('The provided code is invalid or has expired.'),
            ]);
        }

        // Approve user
        $user->approval_status = 'approved';
        $user->save();

        // Delete used OTP
        $otp->delete();

        return redirect()->route('dashboard')->with('status', __('Account verified successfully!'));
    }

    public function resend(Request $request)
    {
        $user = $request->user();

        // Invalidate old codes
        OtpCode::where('user_id', $user->id)->delete();

        // Generate new code
        $code = (string) random_int(100000, 999999);
        
        OtpCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => now()->addMinutes(10),
        ]);

        $user->notify(new SendOtpNotification($code));

        return back()->with('status', __('A new verification code has been sent to your email.'));
    }
}
