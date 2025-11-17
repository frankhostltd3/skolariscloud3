<?php

namespace App\Http\Controllers\Tenant\Modules;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class TimetableController extends Controller
{
    public function index(): View
    {
        $entries = [
            ['time' => '08:00', 'subject' => 'Mathematics', 'room' => 'A1'],
            ['time' => '09:00', 'subject' => 'English', 'room' => 'B3'],
            ['time' => '11:00', 'subject' => 'Physics', 'room' => 'Lab 2'],
        ];
        return view('tenant.modules.timetable.index', compact('entries'));
    }
}
