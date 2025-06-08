<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseModuleController extends Controller
{
    public function create(Course $course)
    {
        return view('admin.course_modules.create', compact('course'));
    }

    public function edit(CourseModule $courseModule)
    {
        return view('admin.course_modules.edit', compact('courseModule'));
    }
    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'order' => 'integer',
        ]);

        $validated['course_id'] = $course->id;

        CourseModule::create($validated);

        return redirect()->route('admin.courses.edit', $course)->with('success', 'Module created successfully');
    }

    public function update(Request $request, CourseModule $courseModule)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'order' => 'integer',
        ]);

        $courseModule->update($validated);

        return redirect()->route('admin.courses.edit', $courseModule->course_id)->with('success', 'Module updated successfully');
    }

    public function destroy(CourseModule $courseModule)
    {
        $courseId = $courseModule->course_id;
        $courseModule->delete();

        return redirect()->route('admin.courses.edit', $courseId)->with('success', 'Module deleted successfully');
    }
}
