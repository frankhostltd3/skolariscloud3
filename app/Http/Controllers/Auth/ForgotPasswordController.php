<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function create(Request $request): View
    {
        return view('auth.forgot-password', [
            'school' => $request->attributes->get('currentSchool'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var School|null $school */
        $school = $request->attributes->get('currentSchool');

        if (! $school) {
            throw ValidationException::withMessages([
                'email' => 'Request a password reset from your school\'s workspace address.',
            ]);
        }

        $validated = $request->validate([
            'email' => ['required', 'email'],
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

        $status = Password::sendResetLink(['email' => $user->email]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'email' => __($status),
        ]);
    }
}
