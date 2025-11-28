<?php

namespace App\Http\Controllers\Tenant\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportCardsController extends Controller
{
    public function index()
    {
        return view('tenant.reports.report-cards.index');
    }
}
