<?php

namespace App\Http\Controllers\Tenant\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\LearningMaterial;
use App\Models\SchoolClass;
use App\Models\Subject;

class LearningMaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teacher = Auth::user();
        $connection = $teacher?->getConnectionName() ?? config('database.default', 'tenant');

        if (! tenant_table_exists('learning_materials', $connection)) {
            $materials = new LengthAwarePaginator([], 0, 15, request()->integer('page', 1), [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);

            $stats = [
                'total' => 0,
                'documents' => 0,
                'videos' => 0,
                'total_views' => 0,
                'total_downloads' => 0,
            ];

            return view('tenant.teacher.classroom.materials.index', [
                'materials' => $materials,
                'stats' => $stats,
                'materialsAvailable' => false,
            ]);
        }

        $materials = LearningMaterial::with(['class', 'subject'])
            ->byTeacher($teacher->id)
            ->latest()
            ->paginate(15)
            ->appends(request()->query());

        $baseQuery = LearningMaterial::byTeacher($teacher->id);
        $hasViewsColumn = tenant_column_exists('learning_materials', 'views_count', $connection);
        $hasDownloadsColumn = tenant_column_exists('learning_materials', 'downloads_count', $connection);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'documents' => (clone $baseQuery)->byType('document')->count(),
            'videos' => (clone $baseQuery)->byType('video')->count(),
            'total_views' => $hasViewsColumn ? (clone $baseQuery)->sum('views_count') : 0,
            'total_downloads' => $hasDownloadsColumn ? (clone $baseQuery)->sum('downloads_count') : 0,
        ];

        return view('tenant.teacher.classroom.materials.index', [
            'materials' => $materials,
            'stats' => $stats,
            'materialsAvailable' => true,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $teacher = Auth::user();
        $connection = $teacher?->getConnectionName() ?? config('database.default', 'tenant');

        if (! tenant_table_exists('learning_materials', $connection)) {
            return redirect()
                ->route('tenant.teacher.classroom.materials.index')
                ->with('warning', 'Learning materials are not enabled for this school yet.');
        }

        $classes = tenant_table_exists('classes', $connection)
            ? SchoolClass::orderBy('name')->get()
            : collect();

        $subjects = tenant_table_exists('subjects', $connection)
            ? Subject::orderBy('name')->get()
            : collect();

        if ($classes->isEmpty() || $subjects->isEmpty()) {
            session()->flash('warning', 'Classes or subjects are missing. Uploads will be available once both are configured.');
        }

        return view('tenant.teacher.classroom.materials.create', compact('classes', 'subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, FileUploadService $fileService) {
        $teacher = Auth::user();
        $connection = $teacher?->getConnectionName() ?? config('database.default', 'tenant');

        if (! tenant_table_exists('learning_materials', $connection)) {
            return redirect()
                ->route('tenant.teacher.classroom.materials.index')
                ->with('error', 'Learning materials cannot be created because the required table is missing.');
        }

        if (! tenant_table_exists('classes', $connection) || ! tenant_table_exists('subjects', $connection)) {
            return redirect()
                ->route('tenant.teacher.classroom.materials.index')
                ->with('error', 'Learning materials cannot be created until classes and subjects are configured.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'type' => 'required|in:document,video,audio,image,link,youtube',
            'file' => 'nullable|file|max:51200', // 50MB
            'external_url' => 'nullable|url',
            'is_downloadable' => 'nullable|boolean',
        ]);

        $data = [
            'teacher_id' => $teacher->id,
            'class_id' => $validated['class_id'],
            'subject_id' => $validated['subject_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'is_downloadable' => $request->has('is_downloadable'),
        ];
    
    // Handle file upload
    if ($request->hasFile('file')) {
        $upload = $fileService->upload($request->file('file'), 'materials');
        $data['file_path'] = $upload['path'];
        $data['file_size'] = $upload['size'];
        $data['file_mime'] = $upload['mime_type'];
    }
    
    // Handle YouTube
    if ($validated['type'] === 'youtube' && $request->external_url) {
        $data['youtube_id'] = $fileService->extractYoutubeId($request->external_url);
        $data['external_url'] = $request->external_url;
    }
    
    // Handle external link
    if ($validated['type'] === 'link') {
        $data['external_url'] = $request->external_url;
    }
    
        $material = LearningMaterial::create($data);

        return redirect()
            ->route('tenant.teacher.classroom.materials.show', $material)
            ->with('success', 'Material uploaded successfully!');
}

 /**
     * Display the specified resource.
     */   
public function show(LearningMaterial $material) {
    $this->authorize('view', $material);
    
    $material->load(['class', 'subject', 'accesses.student']);
    
    return view('tenant.teacher.classroom.materials.show', compact('material'));
}


public function download(LearningMaterial $material) {
    $this->authorize('view', $material);
    
    if (!$material->file_path) {
        return back()->with('error', 'No file available for download.');
    }
    
    $material->incrementDownloads();
    
    return Storage::download($material->file_path, $material->title);
}

   


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LearningMaterial $material)
    {
        $this->authorize('update', $material);
        
    $classes = SchoolClass::orderBy('name')->get();
    $subjects = Subject::orderBy('name')->get();
        
        return view('tenant.teacher.classroom.materials.edit', compact('material', 'classes', 'subjects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LearningMaterial $material, FileUploadService $fileService)
    {
        $this->authorize('update', $material);
        $teacher = Auth::user();
        $connection = $teacher?->getConnectionName() ?? config('database.default', 'tenant');

        if (! tenant_table_exists('learning_materials', $connection)) {
            return redirect()
                ->route('tenant.teacher.classroom.materials.index')
                ->with('error', 'Learning materials cannot be updated because the required table is missing.');
        }

        if (! tenant_table_exists('classes', $connection) || ! tenant_table_exists('subjects', $connection)) {
            return redirect()
                ->route('tenant.teacher.classroom.materials.index')
                ->with('error', 'Learning materials cannot be updated until classes and subjects are configured.');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'type' => 'required|in:document,video,audio,image,link,youtube',
            'file' => 'nullable|file|max:51200', // 50MB
            'external_url' => 'nullable|url',
            'is_downloadable' => 'nullable|boolean',
        ]);
        
        $data = [
            'class_id' => $validated['class_id'],
            'subject_id' => $validated['subject_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'is_downloadable' => $request->has('is_downloadable'),
        ];
        
        // Handle new file upload (replacing old file)
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($material->file_path) {
                Storage::delete($material->file_path);
            }
            
            // Upload new file
            $upload = $fileService->upload($request->file('file'), 'materials');
            $data['file_path'] = $upload['path'];
            $data['file_size'] = $upload['size'];
            $data['file_mime'] = $upload['mime_type'];
        }
        
        // Handle YouTube URL update
        if ($validated['type'] === 'youtube' && $request->external_url) {
            $data['youtube_id'] = $fileService->extractYoutubeId($request->external_url);
            $data['external_url'] = $request->external_url;
        }
        
        // Handle external link update
        if ($validated['type'] === 'link') {
            $data['external_url'] = $request->external_url;
        }
        
        $material->update($data);
        
        return redirect()
            ->route('tenant.teacher.classroom.materials.show', $material)
            ->with('success', 'Material updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LearningMaterial $material) {
    $this->authorize('delete', $material);
    
    // Delete file from storage
    $material->deleteFile();
    
    $material->delete();
    
    return redirect()
        ->route('tenant.teacher.classroom.materials.index')
        ->with('success', 'Material deleted successfully!');
}
}
