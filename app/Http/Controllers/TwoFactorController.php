<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Fortify\RecoveryCode;

class TwoFactorController extends Controller
{
    /**
     * Show the two-factor authentication management page.
     */
    public function show(Request $request)
    {
        $user = $request->user();

        return view('security.two-factor', [
            'user' => $user,
            'confirmsTwoFactorAuthentication' => true,
        ]);
    }

    /**
     * Enable two-factor authentication for the user.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->two_factor_secret) {
            return back()->with('error', 'Two-factor authentication is already enabled.');
        }

        $user->forceFill([
            'two_factor_secret' => encrypt(app(\Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider::class)->generateSecretKey()),
            'two_factor_recovery_codes' => encrypt(json_encode(Collection::times(8, function () {
                return RecoveryCode::generate();
            })->all())),
        ])->save();

        return back()->with('status', 'Two-factor authentication has been enabled. Please scan the QR code and confirm with a code.');
    }

    /**
     * Confirm two-factor authentication with a valid code.
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = $request->user();

        if (!$user->two_factor_secret) {
            return back()->with('error', 'Two-factor authentication is not enabled.');
        }

        if ($user->two_factor_confirmed_at) {
            return back()->with('error', 'Two-factor authentication is already confirmed.');
        }

        $twoFactorProvider = app(\Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider::class);
        $secret = decrypt($user->two_factor_secret);

        if (!$twoFactorProvider->verify($secret, $request->code)) {
            return back()->with('error', 'The provided code was invalid.');
        }

        $user->forceFill([
            'two_factor_confirmed_at' => now(),
        ])->save();

        return back()->with('status', 'Two-factor authentication has been confirmed and is now active!');
    }

    /**
     * Get the QR code SVG for the user.
     */
    public function qrCode(Request $request)
    {
        $user = $request->user();

        if (!$user->two_factor_secret) {
            return response()->json(['error' => 'Two-factor authentication is not enabled.'], 400);
        }

        $twoFactorProvider = app(\Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider::class);

        $qrCodeUrl = $twoFactorProvider->qrCodeUrl(
            config('app.name'),
            $user->email,
            decrypt($user->two_factor_secret)
        );

        return response()->json([
            'svg' => (new \BaconQrCode\Renderer\ImageRenderer(
                new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200),
                new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
            ))->render((new \BaconQrCode\Writer())->writeString($qrCodeUrl))
        ]);
    }

    /**
     * Get the recovery codes for the user.
     */
    public function recoveryCodes(Request $request)
    {
        $user = $request->user();

        if (!$user->two_factor_secret) {
            return response()->json(['error' => 'Two-factor authentication is not enabled.'], 400);
        }

        return response()->json([
            'codes' => json_decode(decrypt($user->two_factor_recovery_codes), true),
        ]);
    }

    /**
     * Regenerate recovery codes.
     */
    public function regenerateRecoveryCodes(Request $request)
    {
        $user = $request->user();

        if (!$user->two_factor_confirmed_at) {
            return back()->with('error', 'Two-factor authentication must be confirmed first.');
        }

        $user->forceFill([
            'two_factor_recovery_codes' => encrypt(json_encode(Collection::times(8, function () {
                return RecoveryCode::generate();
            })->all())),
        ])->save();

        return back()->with('status', 'Recovery codes have been regenerated.');
    }

    /**
     * Disable two-factor authentication for the user.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = $request->user();

        if (!password_verify($request->password, $user->password)) {
            return back()->withErrors(['password' => 'The provided password is incorrect.']);
        }

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        return back()->with('status', 'Two-factor authentication has been disabled.');
    }
}
