<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;

class EventsController extends Controller
{
    public function index()
    {
        return view('tenant.student.events.index');
    }
}