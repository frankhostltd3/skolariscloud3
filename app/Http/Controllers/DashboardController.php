<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $userType = $user->user_type instanceof UserType ? $user->user_type : UserType::from($user->user_type);
        $view = $userType->viewPath();

        abort_unless(view()->exists($view), 404);

        return view($view, [
            'user' => $user,
            'title' => $userType->label() . ' Dashboard',
        ]);
    }
}
