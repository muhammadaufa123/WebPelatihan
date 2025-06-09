<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseVideoRequest;
use App\Models\CourseModule;
use App\Models\CourseVideo;
use Illuminate\Support\Facades\DB;

class ModuleVideoController extends Controller
{
    public function store(StoreCourseVideoRequest $request, CourseModule $courseModule)
    {
        DB::transaction(function () use ($request, $courseModule) {
            $data = $request->validated();
            $data['course_id'] = $courseModule->course_id;
            $data['course_module_id'] = $courseModule->id;
            CourseVideo::create($data);
        });
        return back();
    }
}
