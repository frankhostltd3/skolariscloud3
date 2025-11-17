<?php

namespace App\Http\Controllers\Tenant\Student\Forum;

use App\Http\Controllers\Controller;
use App\Models\ForumCategory;

class CategoriesController extends Controller
{
    public function index()
    {
        $categories = ForumCategory::query()->orderBy('name')->paginate(20);
        return view('tenant.student.forum.categories.index', compact('categories'));
    }
}