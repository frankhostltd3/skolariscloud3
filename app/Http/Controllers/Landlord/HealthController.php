<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Health\Jobs\RunHealthChecksJob;

class HealthController extends Controller
{
    public function refresh(Request $request): JsonResponse
    {
        RunHealthChecksJob::dispatchSync();

        return response()->json([
            'status' => 'ok',
            'message' => __('Health checks refreshed successfully.'),
        ]);
    }
}
