<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseMaterialRequest;
use App\Models\CourseModule;
use App\Models\CourseMaterial;
use Illuminate\Support\Facades\DB;

class ModuleMaterialController extends Controller
{
    public function store(StoreCourseMaterialRequest $request, CourseModule $courseModule)
    {
        DB::transaction(function () use ($request, $courseModule) {
            $validated = $request->validated();
            $path = $request->file('file')->store('materials', 'public');
            $validated['file_path'] = $path;
            $validated['file_type'] = $request->file('file')->getClientOriginalExtension();
            $validated['course_module_id'] = $courseModule->id;
            CourseMaterial::create($validated);
        });
        return back()->with('success', 'Material uploaded successfully.');
    }
}
