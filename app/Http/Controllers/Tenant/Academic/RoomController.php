<?php

namespace App\Http\Controllers\Tenant\Academic;

use App\Http\Controllers\Controller;
use App\Models\Academic\Room;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $query = Room::where('school_id', $schoolId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $rooms = $query->orderBy('name')->paginate(perPage());

        // Get unique types for filter
        $types = Room::where('school_id', $schoolId)
            ->whereNotNull('type')
            ->distinct()
            ->pluck('type');

        return view('tenant.academics.rooms.index', compact('rooms', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tenant.academics.rooms.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('rooms')->where(function ($query) use ($schoolId) {
                    return $query->where('school_id', $schoolId);
                })
            ],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('rooms')->where(function ($query) use ($schoolId) {
                    return $query->where('school_id', $schoolId);
                })
            ],
            'capacity' => 'nullable|integer|min:1',
            'type' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $validated['school_id'] = $schoolId;
        $validated['is_active'] = $request->boolean('is_active', true);

        Room::create($validated);

        return redirect()->route('tenant.academics.rooms.index')
            ->with('success', 'Room created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        $this->authorizeRoom($room);
        return view('tenant.academics.rooms.show', compact('room'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room)
    {
        $this->authorizeRoom($room);
        return view('tenant.academics.rooms.edit', compact('room'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $room)
    {
        $this->authorizeRoom($room);
        $schoolId = auth()->user()->school_id;

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('rooms')->where(function ($query) use ($schoolId) {
                    return $query->where('school_id', $schoolId);
                })->ignore($room->id)
            ],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('rooms')->where(function ($query) use ($schoolId) {
                    return $query->where('school_id', $schoolId);
                })->ignore($room->id)
            ],
            'capacity' => 'nullable|integer|min:1',
            'type' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $room->update($validated);

        return redirect()->route('tenant.academics.rooms.index')
            ->with('success', 'Room updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        $this->authorizeRoom($room);

        // Check if room is used in timetable
        // Assuming TimetableEntry model has room_id
        if (\App\Models\Academic\TimetableEntry::where('room_id', $room->id)->exists()) {
            return back()->with('error', 'Cannot delete room because it is assigned to timetable entries.');
        }

        $room->delete();

        return redirect()->route('tenant.academics.rooms.index')
            ->with('success', 'Room deleted successfully.');
    }

    private function authorizeRoom(Room $room)
    {
        if ($room->school_id !== auth()->user()->school_id) {
            abort(403, 'Unauthorized action.');
        }
    }
}
