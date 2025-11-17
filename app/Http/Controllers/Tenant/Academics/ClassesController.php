<?php

namespace App\Http\Controllers\Tenant\Academics;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Teacher;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassesController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $items = SchoolClass::query()
            ->when($q !== '', fn($query) => $query->where('name','like',"%$q%"))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();
        return view('tenant.academics.classes.index', compact('items','q'));
    }

    public function create(): View
    {
        $teachers = Teacher::orderBy('name')->pluck('name','id');
        $subjects = Subject::orderBy('name')->get();
        return view('tenant.academics.classes.create', compact('teachers','subjects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'max_capacity' => ['nullable','integer','min:1','max:200'],
            'class_teacher_id' => ['nullable','exists:teachers,id', Rule::unique('classes','class_teacher_id')],
            'subject_ids' => ['array'],
            'subject_ids.*' => ['exists:subjects,id'],
        ]);
        $subjectIds = $data['subject_ids'] ?? [];
        unset($data['subject_ids']);
        $class = SchoolClass::create($data);
        if (!empty($subjectIds)) {
            $class->subjects()->sync($subjectIds);
        }
        return redirect()->route('tenant.academics.classes.index')->with('success', __('Class created.'));
    }

    public function show(SchoolClass $class): View
    {
        return view('tenant.academics.classes.show', ['item' => $class]);
    }

    public function edit(SchoolClass $class): View
    {
        $teachers = Teacher::orderBy('name')->pluck('name','id');
        $subjects = Subject::orderBy('name')->get();
        $selectedSubjects = $class->subjects()->pluck('subjects.id')->toArray();
        return view('tenant.academics.classes.edit', ['item' => $class, 'teachers' => $teachers, 'subjects' => $subjects, 'selectedSubjects' => $selectedSubjects]);
    }

    public function update(Request $request, SchoolClass $class): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'max_capacity' => ['nullable','integer','min:1','max:200'],
            'class_teacher_id' => ['nullable','exists:teachers,id', Rule::unique('classes','class_teacher_id')->ignore($class->id)],
            'subject_ids' => ['array'],
            'subject_ids.*' => ['exists:subjects,id'],
        ]);
        $subjectIds = $data['subject_ids'] ?? [];
        unset($data['subject_ids']);
        $class->update($data);
        $class->subjects()->sync($subjectIds);
        return redirect()->route('tenant.academics.classes.show', $class)->with('success', __('Class updated.'));
    }

    public function destroy(SchoolClass $class): RedirectResponse
    {
        $class->delete();
        return redirect()->route('tenant.academics.classes.index')->with('success', __('Class deleted.'));
    }
}
