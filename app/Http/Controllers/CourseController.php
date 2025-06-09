<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Category;
use App\Models\Course;
use App\Models\Trainer;
use App\Models\CourseMode;
use App\Models\CourseLevel;
use App\Models\SubscribeTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    // ✅ Untuk halaman frontend (landing page)
    public function frontIndex(Request $request)
    {
        $categories = Category::all();
        $modes = CourseMode::all();
        $levels = CourseLevel::all();

        $courses = Course::with(['category', 'trainer.user', 'trainees', 'mode', 'level'])
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->course_mode_id, fn($q) => $q->where('course_mode_id', $request->course_mode_id))
            ->when($request->course_level_id, fn($q) => $q->where('course_level_id', $request->course_level_id))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()
            ->get();

        if ($request->ajax()) {
            return view('partials.course-list', compact('courses'))->render();
        }

        return view('front.index', compact('courses', 'categories', 'modes', 'levels'));
    }

    /**
     * Page to explore all courses with filtering and search
     */
    public function explore(Request $request)
    {
        $categories = Category::all();
        $modes = CourseMode::all();
        $levels = CourseLevel::all();

        $courses = Course::with(['category', 'trainer.user', 'trainees', 'mode', 'level'])
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->course_mode_id, fn($q) => $q->where('course_mode_id', $request->course_mode_id))
            ->when($request->course_level_id, fn($q) => $q->where('course_level_id', $request->course_level_id))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()
            ->get();

        if ($request->ajax()) {
            return view('partials.course-list', compact('courses'))->render();
        }

        return view('front.explore', compact('courses', 'categories', 'modes', 'levels'));
    }

    /**
     * Display courses owned by authenticated user
     */
    public function myCourses()
    {
        $user = Auth::user();

        $courses = Course::whereHas('subscribeTransactions', function ($q) use ($user) {
            $q->where('user_id', $user->id)->where('is_paid', true);
        })->with(['category', 'trainer.user', 'mode', 'level'])->get();

        return view('front.my_courses', compact('courses'));
    }

    /**
     * Join a free course and add it to the trainee's course list
     */
    public function join(Course $course)
    {
        $user = Auth::user();

        if ($course->price > 0) {
            return redirect()->route('front.pricing', $course->slug);
        }

        $existing = SubscribeTransaction::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('is_paid', true)
            ->first();

        if (!$existing) {
            SubscribeTransaction::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'total_amount' => 0,
                'is_paid' => true,
                'proof' => 'free',
                'subscription_start_date' => now(),
            ]);
        }

        $user->courses()->syncWithoutDetaching($course->id);

        return redirect()->route('courses.my')->with('success', 'Course added to your list.');
    }

    // ✅ Untuk halaman admin (manage course)
    public function index()
    {
        $user = Auth::user();
        $query = Course::with(['category', 'trainer.user', 'trainees', 'course_videos', 'mode', 'level'])->orderByDesc('id');

        if ($user->hasRole('trainer')) {
            $query->whereHas('trainer', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        $courses = $query->paginate(10);

        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        $categories = Category::all();
        $modes = CourseMode::all();
        $levels = CourseLevel::all();

        return view('admin.courses.create', compact('categories', 'modes', 'levels'));
    }

    public function store(StoreCourseRequest $request)
    {
        $trainer = Trainer::where('user_id', Auth::id())->first();

        if (!$trainer) {
            return redirect()->route('admin.courses.index')
                ->withErrors(['trainer' => 'Unauthorized or invalid trainer']);
        }

        DB::transaction(function () use ($request, $trainer) {
            $validated = $request->validated();

            if ($request->hasFile('thumbnail')) {
                $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
            }

            $slug = Str::slug($validated['name']);
            $count = Course::where('slug', 'like', "{$slug}%")->count();
            $slug = $count ? "{$slug}-{$count}" : $slug;

            $validatedData = array_merge($validated, [
                'slug' => $slug,
                'trainer_id' => $trainer->id,
                'price' => $validated['price'] ?? 0,
            ]);

            $course = Course::create($validatedData);

            if (!empty($validated['course_keypoints'])) {
                foreach ($validated['course_keypoints'] as $keypointText) {
                    if (!empty($keypointText)) {
                        $course->course_keypoints()->create(['name' => $keypointText]);
                    }
                }
            }
        });

        return redirect()->route('admin.courses.index')->with('success', 'Course successfully created.');
    }

    public function show(Course $course)
{
    $course->load([
        'category',
        'trainer.user',
        'trainees',
        'course_videos',
        'course_keypoints',
        'modules',
        'modules.videos',
        'modules.materials',
        'modules.tasks',
    ]);

    return view('admin.courses.show', compact('course'));
}


    public function edit(Course $course)
    {
        $course->load(['category', 'trainer.user', 'trainees', 'course_videos', 'course_keypoints', 'modules']);

        $user = Auth::user();
        if ($user->hasRole('trainer') && $course->trainer->user_id !== $user->id) {
            abort(403);
        }

        $categories = Category::all();
        $modes = CourseMode::all();
        $levels = CourseLevel::all();

        return view('admin.courses.edit', compact('course', 'categories', 'modes', 'levels'));
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        DB::transaction(function () use ($request, $course) {
            $validated = $request->validated();

            if ($request->hasFile('thumbnail')) {
                $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
            }

            $slug = Str::slug($validated['name']);
            $count = Course::where('slug', 'like', "{$slug}%")->where('id', '!=', $course->id)->count();
            $slug = $count ? "{$slug}-{$count}" : $slug;

            $validatedData = array_merge($validated, ['slug' => $slug]);

            $course->update($validatedData);

            if (!empty($validated['course_keypoints'])) {
                $course->course_keypoints()->delete();
                foreach ($validated['course_keypoints'] as $keypointText) {
                    if (!empty($keypointText)) {
                        $course->course_keypoints()->create(['name' => $keypointText]);
                    }
                }
            }
        });

        return redirect()->route('admin.courses.show', $course);
    }

    public function destroy(Course $course)
    {
        $user = Auth::user();
        if ($user->hasRole('trainer') && $course->trainer->user_id !== $user->id) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            $course->delete();
            DB::commit();
            return redirect()->route('admin.courses.index')->with('success', 'Course deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.courses.index')->with('error', 'An error occurred while deleting the course.');
        }
    }
}
