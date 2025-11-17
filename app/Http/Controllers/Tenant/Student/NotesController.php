<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LearningMaterial;
use App\Models\StudentNote;
use App\Models\Subject;
use App\Models\Student;

class NotesController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();

        if (!$student) {
            return view('tenant.student.notes.index', [
                'materials' => collect(),
                'personalNotes' => collect(),
                'subjects' => collect(),
                'student' => null,
                'statistics' => null,
            ]);
        }

        // Get learning materials for student's class
        $materialsQuery = LearningMaterial::query()
            ->with(['teacher', 'subject', 'class'])
            ->where('class_id', $student->class_id);

        // Get personal notes
        $notesQuery = StudentNote::query()
            ->with('subject')
            ->where('student_id', $student->id);

        // Apply filters
        $subjectId = $request->input('subject_id');
        $search = $request->input('search');
        $view = $request->input('view', 'materials'); // materials or personal

        if ($subjectId) {
            $materialsQuery->where('subject_id', $subjectId);
            $notesQuery->where('subject_id', $subjectId);
        }

        if ($search) {
            $materialsQuery->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
            $notesQuery->search($search);
        }

        if ($request->input('favorite')) {
            $notesQuery->where('is_favorite', true);
        }

        if ($request->input('type')) {
            $materialsQuery->where('type', $request->input('type'));
        }

        $materials = $materialsQuery->latest()->paginate(12, ['*'], 'materials_page')->withQueryString();
        $personalNotes = $notesQuery->latest()->paginate(12, ['*'], 'notes_page')->withQueryString();

        // Get subjects
        $subjects = Subject::whereHas('learningMaterials', function($q) use ($student) {
            $q->where('class_id', $student->class_id);
        })->orWhereHas('studentNotes', function($q) use ($student) {
            $q->where('student_id', $student->id);
        })->orderBy('name')->get(['id', 'name']);

        // Calculate statistics
        $statistics = [
            'total_materials' => LearningMaterial::where('class_id', $student->class_id)->count(),
            'total_personal_notes' => StudentNote::where('student_id', $student->id)->count(),
            'favorite_notes' => StudentNote::where('student_id', $student->id)->where('is_favorite', true)->count(),
            'documents' => LearningMaterial::where('class_id', $student->class_id)->where('type', 'document')->count(),
            'videos' => LearningMaterial::where('class_id', $student->class_id)->where('type', 'video')->count(),
            'total_words' => StudentNote::where('student_id', $student->id)->get()->sum('word_count'),
        ];

        return view('tenant.student.notes.index', compact(
            'materials', 
            'personalNotes', 
            'subjects', 
            'student', 
            'statistics',
            'subjectId',
            'search',
            'view'
        ));
    }

    public function show(LearningMaterial $note)
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();

        if (!$student) {
            abort(404, 'Student record not found');
        }

        // Verify student has access to this material (same class)
        if ($note->class_id != $student->class_id) {
            abort(403, 'You do not have access to this material');
        }

        // Record access and increment view count
        $note->incrementViews();
        $note->recordAccess($student->id, 'view');

        return view('tenant.student.notes.show', compact('note', 'student'));
    }

    public function download(LearningMaterial $note)
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();

        if (!$student) {
            abort(404, 'Student record not found');
        }

        // Verify access
        if ($note->class_id != $student->class_id) {
            abort(403, 'You do not have access to this material');
        }

        // Check if downloadable
        if (!$note->is_downloadable || !$note->file_path) {
            abort(403, 'This material is not available for download');
        }

        // Record download and increment counter
        $note->incrementDownloads();
        $note->recordAccess($student->id, 'download');

        return response()->download(storage_path('app/' . $note->file_path));
    }

    // Personal Notes CRUD
    public function createPersonalNote()
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();

        if (!$student) {
            return redirect()->route('tenant.student.notes.index')
                ->with('error', 'Student record not found');
        }

        $subjects = Subject::orderBy('name')->get(['id', 'name']);

        return view('tenant.student.notes.create', compact('student', 'subjects'));
    }

    public function storePersonalNote(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();

        if (!$student) {
            return redirect()->route('tenant.student.notes.index')
                ->with('error', 'Student record not found');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'subject_id' => 'nullable|exists:subjects,id',
            'tags' => 'nullable|string',
            'color' => 'nullable|string|max:20',
        ]);

        $note = new StudentNote($validated);
        $note->student_id = $student->id;
        
        if ($request->filled('tags')) {
            $note->tags = array_map('trim', explode(',', $request->tags));
        }

        $note->save();

        return redirect()->route('tenant.student.notes.index', ['view' => 'personal'])
            ->with('success', 'Note created successfully!');
    }

    public function editPersonalNote(StudentNote $personalNote)
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();

        if (!$student || $personalNote->student_id != $student->id) {
            abort(403, 'Unauthorized access');
        }

        $subjects = Subject::orderBy('name')->get(['id', 'name']);

        return view('tenant.student.notes.edit', compact('personalNote', 'student', 'subjects'));
    }

    public function updatePersonalNote(Request $request, StudentNote $personalNote)
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();

        if (!$student || $personalNote->student_id != $student->id) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'subject_id' => 'nullable|exists:subjects,id',
            'tags' => 'nullable|string',
            'color' => 'nullable|string|max:20',
        ]);

        $personalNote->fill($validated);
        
        if ($request->filled('tags')) {
            $personalNote->tags = array_map('trim', explode(',', $request->tags));
        }

        $personalNote->save();

        return redirect()->route('tenant.student.notes.index', ['view' => 'personal'])
            ->with('success', 'Note updated successfully!');
    }

    public function destroyPersonalNote(StudentNote $personalNote)
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();

        if (!$student || $personalNote->student_id != $student->id) {
            abort(403, 'Unauthorized access');
        }

        $personalNote->delete();

        return redirect()->route('tenant.student.notes.index', ['view' => 'personal'])
            ->with('success', 'Note deleted successfully!');
    }

    public function toggleFavorite(StudentNote $personalNote)
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();

        if (!$student || $personalNote->student_id != $student->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $personalNote->is_favorite = !$personalNote->is_favorite;
        $personalNote->save();

        return response()->json([
            'success' => true,
            'is_favorite' => $personalNote->is_favorite
        ]);
    }
}