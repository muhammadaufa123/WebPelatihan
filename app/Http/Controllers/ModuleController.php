<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Course;
use App\Models\Module;

class ModuleController extends Controller
{
    /**
     * Display a listing of the modules for a course.
     */
    public function index(Course $course)
    {
        $modules = $course->modules()->orderBy('order')->get();
        return view('admin.modules.index', compact('course', 'modules'));
    }

    /**
     * Show the form for creating a new module.
     */
    public function create(Course $course)
    {
        return view('admin.modules.create', compact('course'));
    }

    /**
     * Store a newly created module in storage.
     */
    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'order' => 'nullable|integer'
        ]);
        $validated['course_id'] = $course->id;
        Module::create($validated);
        return redirect()->route('admin.modules.index', $course);
    }

    /**
     * Show the form for editing the specified module.
     */
    public function edit(Course $course, Module $module)
    {
        return view('admin.modules.edit', compact('course', 'module'));
    }

    /**
     * Update the specified module in storage.
     */
    public function update(Request $request, Course $course, Module $module)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'order' => 'nullable|integer'
        ]);
        $module->update($validated);
        return redirect()->route('admin.modules.index', $course);
    }

    /**
     * Remove the specified module from storage.
     */
    public function destroy(Course $course, Module $module)
    {
        DB::transaction(function () use ($module) {
            $module->delete();
        });
        return redirect()->route('admin.modules.index', $course);
    }
}
