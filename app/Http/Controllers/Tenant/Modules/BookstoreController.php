<?php

namespace App\Http\Controllers\Tenant\Modules;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class BookstoreController extends Controller
{
    public function index(): View
    {
        return view('tenant.modules.bookstore.index');
    }
}
