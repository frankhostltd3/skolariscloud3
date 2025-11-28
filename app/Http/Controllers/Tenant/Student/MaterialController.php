<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;
use App\Models\LearningMaterial;
use App\Models\MaterialAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    /**
     * Display all available learning materials
     */
    public function index(Request $request)
    {
        $student = Auth::user();
        
        // Get student's enrolled class IDs
        $classIds = $student->enrollments()
            ->where('status', 'active')
            ->pluck('class_id');

        $query = LearningMaterial::whereIn('class_id', $classIds)
            ->with(['class', 'subject', 'teacher']);

        // Filter by type
        if ($request->has('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }

        // Filter by class
        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by subject
        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $materials = $query->latest()->paginate(15);

        // Get filter options
        $classes = $student->enrollments()
            ->with('schoolClass')
            ->where('status', 'active')
            ->get()
            ->pluck('schoolClass');

        $subjects = \App\Models\Subject::whereIn('id', 
            LearningMaterial::whereIn('class_id', $classIds)
                ->distinct()
                ->pluck('subject_id')
        )->get();

        return view('tenant.student.classroom.materials.index', compact(
            'materials',
            'classes',
            'subjects'
        ));
    }

    /**
     * Show a specific learning material
     */
    public function show($id)
    {
        $student = Auth::user();
        
        // Get student's enrolled class IDs
        $classIds = $student->enrollments()
            ->where('status', 'active')
            ->pluck('class_id');

        $material = LearningMaterial::whereIn('class_id', $classIds)
            ->with(['class', 'subject', 'teacher'])
            ->findOrFail($id);

        // Record access
        $this->recordAccess($material, $student->id);

        // Increment views
        $material->incrementViews();

        return view('tenant.student.classroom.materials.show', compact('material'));
    }

    /**
     * Download a learning material file
     */
    public function download($id)
    {
        $student = Auth::user();
        
        // Get student's enrolled class IDs
        $classIds = $student->enrollments()
            ->where('status', 'active')
            ->pluck('class_id');

        $material = LearningMaterial::whereIn('class_id', $classIds)
            ->findOrFail($id);

        // Check if material is downloadable
        if (!$material->is_downloadable) {
            return back()->with('error', 'This material is not available for download.');
        }

        // Check if it's a file (not URL or YouTube)
        if (!in_array($material->type, ['document', 'video', 'image', 'audio'])) {
            return back()->with('error', 'This type of material cannot be downloaded.');
        }

        if (!$material->file_path || !Storage::disk($material->storage_disk)->exists($material->file_path)) {
            return back()->with('error', 'File not found.');
        }

        // Record access if not already recorded
        $this->recordAccess($material, $student->id);

        // Increment downloads
        $material->incrementDownloads();

        // Get file path and name
        $filePath = Storage::disk($material->storage_disk)->path($material->file_path);
        $fileName = $material->file_name ?? basename($material->file_path);

        return response()->download($filePath, $fileName);
    }

    /**
     * Record student access to material
     */
    protected function recordAccess(LearningMaterial $material, int $studentId)
    {
        // Check if access was already recorded today
        $alreadyAccessed = MaterialAccess::where('material_id', $material->id)
            ->where('student_id', $studentId)
            ->whereDate('accessed_at', today())
            ->exists();

        if (!$alreadyAccessed) {
            MaterialAccess::create([
                'material_id' => $material->id,
                'student_id' => $studentId,
                'accessed_at' => now(),
            ]);
        }
    }

    /**
     * Get materials by subject
     */
    public function bySubject($subjectId)
    {
        $student = Auth::user();
        
        $classIds = $student->enrollments()
            ->where('status', 'active')
            ->pluck('class_id');

        $materials = LearningMaterial::whereIn('class_id', $classIds)
            ->where('subject_id', $subjectId)
            ->with(['class', 'subject', 'teacher'])
            ->latest()
            ->paginate(15);

        $subject = \App\Models\Subject::findOrFail($subjectId);

        return view('tenant.student.classroom.materials.by-subject', compact('materials', 'subject'));
    }

    /**
     * Get recently accessed materials
     */
    public function recent()
    {
        $student = Auth::user();

        $recentAccesses = MaterialAccess::where('student_id', $student->id)
            ->with('material.class', 'material.subject', 'material.teacher')
            ->latest('accessed_at')
            ->take(20)
            ->get()
            ->pluck('material')
            ->unique('id');

        return view('tenant.student.classroom.materials.recent', compact('recentAccesses'));
    }
}
