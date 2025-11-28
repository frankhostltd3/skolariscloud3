<?php

namespace App\Http\Controllers\Tenant\Modules\HumanResource;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Http\Requests\DepartmentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DepartmentsController extends Controller
{
    public function index(): View
    {
        $departments = Department::all();
        return view('tenant.modules.human_resource.departments.index', compact('departments'));
    }

    public function create(): View
    {
        $this->authorize('create', Department::class);
        return view('tenant.modules.human_resource.departments.create');
    }

    public function store(DepartmentRequest $request): RedirectResponse
    {
        $this->authorize('create', Department::class);
        $department = Department::create($request->validated());
        return redirect()->route('tenant.modules.human-resource.departments.index')->with('success', 'Department created successfully.');
    }

    public function show(Department $department): View
    {
        $this->authorize('view', $department);
        return view('tenant.modules.human_resource.departments.show', compact('department'));
    }

    public function edit(Department $department): View
    {
        $this->authorize('update', $department);
        return view('tenant.modules.human_resource.departments.edit', compact('department'));
    }

    public function update(DepartmentRequest $request, Department $department): RedirectResponse
    {
        $this->authorize('update', $department);
        $department->update($request->validated());
        return redirect()->route('tenant.modules.human-resource.departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        $this->authorize('delete', $department);
        $department->delete();
        return redirect()->route('tenant.modules.human-resource.departments.index')->with('success', 'Department deleted successfully.');
    }
}
