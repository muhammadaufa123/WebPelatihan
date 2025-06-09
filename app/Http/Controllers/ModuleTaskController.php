<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreModuleTaskRequest;
use App\Models\CourseModule;
use App\Models\ModuleTask;
use Illuminate\Support\Facades\DB;

class ModuleTaskController extends Controller
{
    public function store(StoreModuleTaskRequest $request, CourseModule $courseModule)
    {
        DB::transaction(function () use ($request, $courseModule) {
            $data = $request->validated();
            $data['course_module_id'] = $courseModule->id;
            ModuleTask::create($data);
        });
        return back();
    }
}
