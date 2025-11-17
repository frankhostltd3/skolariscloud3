<?php

namespace App\Http\Controllers\Tenant\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClassStreamRequest;
use App\Http\Requests\UpdateClassStreamRequest;
use App\Models\Academic\ClassRoom;
use App\Models\Academic\ClassStream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassStreamController extends Controller
{
    /**
     * Display a listing of streams for a class.
     */
    public function index(ClassRoom $class)
    {
        $school = request()->attributes->get('currentSchool') ?? auth()->user()->school;

        // Ensure class belongs to current school
        if ($class->school_id !== $school->id) {
            abort(403, 'Unauthorized access to class.');
        }

        $streams = $class->streams()
            ->withCount('students')
            ->orderBy('name')
            ->paginate(15);

        return view('tenant.academics.streams.index', compact('class', 'streams'));
    }

    /**
     * Show the form for creating a new stream.
     */
    public function create(ClassRoom $class)
    {
        $school = request()->attributes->get('currentSchool') ?? auth()->user()->school;

        // Ensure class belongs to current school
        if ($class->school_id !== $school->id) {
            abort(403, 'Unauthorized access to class.');
        }

        return view('tenant.academics.streams.create', compact('class'));
    }

    /**
     * Store a newly created stream in storage.
     */
    public function store(StoreClassStreamRequest $request, ClassRoom $class)
    {
        $school = request()->attributes->get('currentSchool') ?? auth()->user()->school;

        // Ensure class belongs to current school
        if ($class->school_id !== $school->id) {
            abort(403, 'Unauthorized access to class.');
        }

        try {
            DB::beginTransaction();

            $stream = $class->streams()->create([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'capacity' => $request->capacity,
                'is_active' => $request->is_active ?? true,
            ]);

            DB::commit();

            return redirect()
                ->route('tenant.academics.streams.index', $class)
                ->with('success', __('Stream created successfully.'));

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', __('Failed to create stream. Please try again.'));
        }
    }

    /**
     * Display the specified stream.
     */
    public function show(ClassRoom $class, ClassStream $stream)
    {
        $school = request()->attributes->get('currentSchool') ?? auth()->user()->school;

        // Ensure class and stream belong to current school
        if ($class->school_id !== $school->id || $stream->class_id !== $class->id) {
            abort(403, 'Unauthorized access.');
        }

        $stream->load(['students', 'class']);

        return view('tenant.academics.streams.show', compact('class', 'stream'));
    }

    /**
     * Show the form for editing the specified stream.
     */
    public function edit(ClassRoom $class, ClassStream $stream)
    {
        $school = request()->attributes->get('currentSchool') ?? auth()->user()->school;

        // Ensure class and stream belong to current school
        if ($class->school_id !== $school->id || $stream->class_id !== $class->id) {
            abort(403, 'Unauthorized access.');
        }

        return view('tenant.academics.streams.edit', compact('class', 'stream'));
    }

    /**
     * Update the specified stream in storage.
     */
    public function update(UpdateClassStreamRequest $request, ClassRoom $class, ClassStream $stream)
    {
        $school = request()->attributes->get('currentSchool') ?? auth()->user()->school;

        // Ensure class and stream belong to current school
        if ($class->school_id !== $school->id || $stream->class_id !== $class->id) {
            abort(403, 'Unauthorized access.');
        }

        try {
            DB::beginTransaction();

            $stream->update([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'capacity' => $request->capacity,
                'is_active' => $request->is_active ?? $stream->is_active,
            ]);

            DB::commit();

            return redirect()
                ->route('tenant.academics.streams.index', $class)
                ->with('success', __('Stream updated successfully.'));

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', __('Failed to update stream. Please try again.'));
        }
    }

    /**
     * Remove the specified stream from storage.
     */
    public function destroy(ClassRoom $class, ClassStream $stream)
    {
        $school = request()->attributes->get('currentSchool') ?? auth()->user()->school;

        // Ensure class and stream belong to current school
        if ($class->school_id !== $school->id || $stream->class_id !== $class->id) {
            abort(403, 'Unauthorized access.');
        }

        // Check if stream has students
        if ($stream->students()->count() > 0) {
            return back()->with('error', __('Cannot delete stream with enrolled students. Please reassign students first.'));
        }

        try {
            $stream->delete();

            return redirect()
                ->route('tenant.academics.streams.index', $class)
                ->with('success', __('Stream deleted successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Failed to delete stream. Please try again.'));
        }
    }

    /**
     * Generate multiple streams with common naming patterns.
     */
    public function bulkCreate(Request $request, ClassRoom $class)
    {
        $school = request()->attributes->get('currentSchool') ?? auth()->user()->school;

        // Ensure class belongs to current school
        if ($class->school_id !== $school->id) {
            abort(403, 'Unauthorized access to class.');
        }

        $request->validate([
            'pattern' => 'required|in:alphabetic,numeric,cardinal,custom',
            'count' => 'required|integer|min:1|max:26',
            'prefix' => 'nullable|string|max:10',
            'suffix' => 'nullable|string|max:10',
            'custom_names' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1|max:500',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $created = 0;
            $names = [];

            // Generate stream names based on pattern
            switch ($request->pattern) {
                case 'alphabetic':
                    // A, B, C, D...
                    for ($i = 0; $i < $request->count; $i++) {
                        $names[] = chr(65 + $i); // 65 is ASCII for 'A'
                    }
                    break;

                case 'numeric':
                    // 1, 2, 3, 4...
                    for ($i = 1; $i <= $request->count; $i++) {
                        $names[] = (string)$i;
                    }
                    break;

                case 'cardinal':
                    // East, West, North, South
                    $cardinals = ['East', 'West', 'North', 'South', 'Northeast', 'Northwest', 'Southeast', 'Southwest'];
                    $names = array_slice($cardinals, 0, min($request->count, count($cardinals)));
                    break;

                case 'custom':
                    // Custom comma-separated names
                    if ($request->custom_names) {
                        $names = array_map('trim', explode(',', $request->custom_names));
                        $names = array_slice($names, 0, $request->count);
                    }
                    break;
            }

            // Create streams
            foreach ($names as $name) {
                $streamName = trim(($request->prefix ?? '') . ' ' . $name . ' ' . ($request->suffix ?? ''));

                // Check if stream already exists
                if (!$class->streams()->where('name', $streamName)->exists()) {
                    $class->streams()->create([
                        'name' => $streamName,
                        'code' => strtoupper(substr($class->code ?? '', 0, 3) . '-' . substr($name, 0, 2)),
                        'description' => $request->description,
                        'capacity' => $request->capacity,
                        'is_active' => true,
                    ]);
                    $created++;
                }
            }

            DB::commit();

            return redirect()
                ->route('tenant.academics.streams.index', $class)
                ->with('success', __(':count stream(s) created successfully.', ['count' => $created]));

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', __('Failed to create streams. Please try again.'));
        }
    }
}
