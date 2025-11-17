<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IpBlock;
use Illuminate\Http\Request;

class IpBlockController extends Controller
{
    /**
     * Display IP blocks
     */
    public function index(Request $request)
    {
        $query = IpBlock::query();

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where(function ($q) {
                    $q->where('is_permanent', true)
                        ->orWhere('expires_at', '>', now())
                        ->orWhereNull('expires_at');
                });
            } elseif ($request->status === 'expired') {
                $query->where('is_permanent', false)
                    ->whereNotNull('expires_at')
                    ->where('expires_at', '<=', now());
            }
        }

        // Search by IP
        if ($request->filled('ip')) {
            $query->where('ip_address', 'like', '%' . $request->ip . '%');
        }

        $blocks = $query->orderBy('blocked_at', 'desc')->paginate(50)->withQueryString();

        return view('admin.security.ip-blocks', compact('blocks'));
    }

    /**
     * Store a new IP block
     */
    public function store(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer|min:1',
            'is_permanent' => 'boolean',
        ]);

        $durationMinutes = $request->boolean('is_permanent') ? null : $request->input('duration', 1440);

        IpBlock::blockIp(
            $request->input('ip_address'),
            $request->input('reason'),
            $durationMinutes,
            $request->boolean('is_permanent'),
            auth()->id() ?? 'admin',
            tenant('id')
        );

        return redirect()->back()->with('success', 'IP address blocked successfully.');
    }

    /**
     * Remove an IP block
     */
    public function destroy($id)
    {
        $block = IpBlock::findOrFail($id);
        $ipAddress = $block->ip_address;
        
        $block->delete();

        return redirect()->back()->with('success', "IP address {$ipAddress} unblocked successfully.");
    }

    /**
     * Cleanup expired blocks
     */
    public function cleanup()
    {
        $deleted = IpBlock::cleanupExpired();

        return redirect()->back()->with('success', "Cleaned up {$deleted} expired IP blocks.");
    }
}
