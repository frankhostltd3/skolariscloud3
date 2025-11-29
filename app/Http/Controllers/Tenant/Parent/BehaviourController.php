<?php

namespace App\Http\Controllers\Tenant\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BehaviourController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $parent = $user->parentProfile;
        
        $students = $parent ? $parent->students()
            ->with(['class', 'stream', 'behaviours'])
            ->get() : collect([]);

        return view('tenant.parent.behaviour.index', compact('students'));
    }
}
