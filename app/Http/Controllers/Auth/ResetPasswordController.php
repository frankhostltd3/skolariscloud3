<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{
    public function create(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
            'school' => $request->attributes->get('currentSchool'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var School|null $school */
        $school = $request->attributes->get('currentSchool');

        if (! $school) {
            throw ValidationException::withMessages([
                'email' => 'Reset your password from your school\'s workspace address.',
            ]);
        }

        $minLength = (int) setting('password_min_length', 8);

        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:' . $minLength, 'confirmed'],
        ]);

        $user = User::query()
            ->where('email', $validated['email'])
            ->where('school_id', $school->id)
            ->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'We could not find an account with that email for this school.',
            ]);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) use ($request): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));

                Auth::login($user);
                $request->session()->regenerate();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('dashboard')->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'email' => __($status),
        ]);
    }
}
