<?php

namespace App\Http\Controllers\Tenant\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
        $materials = LearningMaterial::with(['class', 'subject'])
        ->byTeacher(Auth::id())
        ->latest()
        ->paginate(15);
    
    $stats = [
        'total' => LearningMaterial::byTeacher(Auth::id())->count(),
        'documents' => LearningMaterial::byTeacher(Auth::id())->byType('document')->count(),
        'videos' => LearningMaterial::byTeacher(Auth::id())->byType('video')->count(),
        'total_views' => LearningMaterial::byTeacher(Auth::id())->sum('views_count'),
        'total_downloads' => LearningMaterial::byTeacher(Auth::id())->sum('downloads_count'),
    ];
    
    return view('tenant.teacher.classroom.materials.index', compact('materials', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    $classes = SchoolClass::orderBy('name')->get();
    $subjects = Subject::orderBy('name')->get();
    
        return view('tenant.teacher.classroom.materials.create', compact('classes', 'subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, FileUploadService $fileService) {
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'class_id' => 'required|exists:school_classes,id',
        'subject_id' => 'required|exists:subjects,id',
        'type' => 'required|in:document,video,audio,image,link,youtube',
        'file' => 'nullable|file|max:51200', // 50MB
        'external_url' => 'nullable|url',
        'is_downloadable' => 'nullable|boolean',
    ]);
    
    $data = [
        'teacher_id' => Auth::id(),
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
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_id' => 'required|exists:school_classes,id',
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
