<?php

namespace App\Http\Controllers\Settings;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        abort_if(! $user, 403);
        abort_if(! $user->hasUserType(UserType::ADMIN), 403);

        $currentSchool = $request->attributes->get('currentSchool');

        return view('settings.index', [
            'isTenantContext' => (bool) $currentSchool,
        ]);
    }
}
