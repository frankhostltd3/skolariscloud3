<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;

class DocumentsController extends Controller
{
    public function index()
    {
        return view('tenant.student.documents.index');
    }
}