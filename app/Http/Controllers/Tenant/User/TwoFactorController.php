<?php

namespace App\Http\Controllers\Tenant\User;

use App\Http\Controllers\Controller;
use App\Models\SecurityAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TwoFactorController extends Controller
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->middleware('auth');
        $this->google2fa = app(Google2FA::class);
    }

    /**
     * Show the 2FA management page
     */
    public function show()
    {
        $user = auth()->user();
        
        return view('tenant.user.two-factor.show', [
            'user' => $user,
            'twoFactorEnabled' => $user->hasTwoFactorEnabled(),
        ]);
    }

    /**
     * Enable two-factor authentication
     */
    public function enable(Request $request)
    {
        $user = auth()->user();

        if ($user->hasTwoFactorEnabled()) {
            return back()->with('error', __('Two-factor authentication is already enabled.'));
        }

        // Generate secret key
        $secret = $this->google2fa->generateSecretKey(config('google2fa.otp_secret_length', 32));

        // Store encrypted secret temporarily in session
        session([
            'two_factor_secret' => encrypt($secret),
            'two_factor_setup_time' => now(),
        ]);

        // Generate QR code
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('google2fa.company', config('app.name')),
            $user->email,
            $secret
        );

        $qrCode = QrCode::size(config('google2fa.qr_code_size', 200))
            ->format('svg')
            ->generate($qrCodeUrl);

        return view('tenant.user.two-factor.enable', [
            'secret' => $secret,
            'qrCode' => $qrCode,
        ]);
    }

    /**
     * Confirm and activate two-factor authentication
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'code' => 'required|string|digits:6',
        ]);

        $user = auth()->user();

        // Get secret from session
        $secret = session('two_factor_secret');
        
        if (!$secret) {
            throw ValidationException::withMessages([
                'code' => [__('Two-factor authentication setup has expired. Please start again.')],
            ]);
        }

        $secret = decrypt($secret);

        // Verify the code
        $valid = $this->google2fa->verifyKey($secret, $request->code, config('google2fa.window', 4));

        if (!$valid) {
            throw ValidationException::withMessages([
                'code' => [__('The verification code is invalid.')],
            ]);
        }

        // Generate recovery codes
        $recoveryCodes = $this->generateRecoveryCodes();

        // Save to database
        $user->update([
            'two_factor_enabled' => true,
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
            'two_factor_confirmed_at' => now(),
        ]);

        // Clear session
        session()->forget(['two_factor_secret', 'two_factor_setup_time']);

        // Mark session as 2FA verified
        session(['two_factor_verified' => true]);

        // Log security event
        SecurityAuditLog::log(
            SecurityAuditLog::EVENT_TWO_FACTOR_ENABLED,
            'User enabled two-factor authentication',
            $user
        );

        return view('tenant.user.two-factor.recovery-codes', [
            'recoveryCodes' => $recoveryCodes,
        ])->with('success', __('Two-factor authentication has been enabled successfully.'));
    }

    /**
     * Disable two-factor authentication
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = auth()->user();

        // Verify password
        if (!password_verify($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => [__('The password is incorrect.')],
            ]);
        }

        // Disable 2FA
        $user->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);

        // Log security event
        SecurityAuditLog::log(
            SecurityAuditLog::EVENT_TWO_FACTOR_DISABLED,
            'User disabled two-factor authentication',
            $user
        );

        return redirect()->route('tenant.user.two-factor.show')
            ->with('success', __('Two-factor authentication has been disabled.'));
    }

    /**
     * Show recovery codes
     */
    public function showRecoveryCodes()
    {
        $user = auth()->user();

        if (!$user->hasTwoFactorEnabled()) {
            return redirect()->route('tenant.user.two-factor.show')
                ->with('error', __('Two-factor authentication is not enabled.'));
        }

        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

        return view('tenant.user.two-factor.recovery-codes', [
            'recoveryCodes' => $recoveryCodes,
            'showOnly' => true,
        ]);
    }

    /**
     * Regenerate recovery codes
     */
    public function regenerateRecoveryCodes(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = auth()->user();

        if (!$user->hasTwoFactorEnabled()) {
            return redirect()->route('tenant.user.two-factor.show')
                ->with('error', __('Two-factor authentication is not enabled.'));
        }

        // Verify password
        if (!password_verify($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => [__('The password is incorrect.')],
            ]);
        }

        // Generate new recovery codes
        $recoveryCodes = $this->generateRecoveryCodes();

        // Update database
        $user->update([
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
        ]);

        // Log security event
        SecurityAuditLog::log(
            'recovery_codes_regenerated',
            'User regenerated two-factor recovery codes',
            $user
        );

        return view('tenant.user.two-factor.recovery-codes', [
            'recoveryCodes' => $recoveryCodes,
        ])->with('success', __('Recovery codes have been regenerated.'));
    }

    /**
     * Generate recovery codes
     */
    protected function generateRecoveryCodes(): array
    {
        $codes = [];
        $count = config('google2fa.recovery_codes_count', 10);
        $length = config('google2fa.recovery_code_length', 10);

        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(Str::random($length / 2) . '-' . Str::random($length / 2));
        }

        return $codes;
    }
}
