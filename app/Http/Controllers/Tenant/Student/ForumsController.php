<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;

class ForumsController extends Controller
{
    public function index()
    {
        return view('tenant.student.forums.index');
    }
}