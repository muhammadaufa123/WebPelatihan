<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubscribeTransactionRequest;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseMode;
use App\Models\CourseLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SubscribeTransaction;
use App\Models\CourseProgress;
use App\Models\Certificate;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class FrontController extends Controller
{
    /**
     * Halaman depan menampilkan semua course
     */
    public function index(Request $request)
    {
        $query = Course::with(['category', 'trainer', 'trainees', 'mode', 'level'])->orderByDesc('id');

        if ($request->filled('course_mode_id')) {
            $query->where('course_mode_id', $request->course_mode_id);
        }

        if ($request->filled('course_level_id')) {
            $query->where('course_level_id', $request->course_level_id);
        }

        $courses = $query->get();
        $categories = Category::all();
        $modes = CourseMode::all();
        $levels = CourseLevel::all();

        return view('front.index', compact('courses', 'categories', 'modes', 'levels'));
    }

    /**
     * Detail course
     */
    public function details(Course $course)
    {
        $course->load(['category', 'trainer.user', 'trainees', 'course_videos', 'course_keypoints', 'modules']);

        return view('front.details', compact('course'));
    }

    public function category(Request $request, Category $category)
    {
        $query = $category->courses()->with(['mode', 'level']);

        if ($request->filled('course_mode_id')) {
            $query->where('course_mode_id', $request->course_mode_id);
        }

        if ($request->filled('course_level_id')) {
            $query->where('course_level_id', $request->course_level_id);
        }

        $courses = $query->get();

        $otherCategories = Category::where('id', '!=', $category->id)->get();
        $modes = CourseMode::all();
        $levels = CourseLevel::all();

        return view('front.category', compact('courses', 'category', 'otherCategories', 'modes', 'levels'));
    }

    /**
     * Halaman pembelajaran untuk course tertentu
     */
    public function learning(Course $course, $courseVideoId)
    {
        $course->load(['category', 'trainer.user', 'trainees', 'course_videos', 'course_keypoints', 'modules']);

        $user = Auth::user();

        // Cek apakah user punya akses ke course ini
        $hasAccess = SubscribeTransaction::where('user_id', $user->id)
            ->where('is_paid', true)
            ->where(function ($query) use ($course) {
                $query->whereNull('course_id') // akses ke semua kelas
                      ->orWhere('course_id', $course->id); // atau hanya kelas ini
            })
            ->exists();

        // Jika tidak punya akses dan course berbayar, redirect
        if (!$hasAccess && $course->price > 0) {
            return redirect()->route('front.pricing', compact('course'))
                             ->with('error', 'Kamu belum membeli akses ke kelas ini.');
        }

        // Muat modul beserta video dan materi
        $course->load(['modules.videos', 'modules.materials']);

        // Ambil video dari relasi
        $video = $course->course_videos->firstWhere('id', $courseVideoId);

        // Jika video tidak ditemukan, lempar 404
        if (!$video) {
            abort(404, 'Video tidak ditemukan.');
        }

        // Tambahkan course ke relasi user jika belum ada (untuk pelacakan/history)
        $user->courses()->syncWithoutDetaching($course->id);

        // Update progress for this video
        $progress = CourseProgress::firstOrCreate([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ], [
            'completed_videos' => [],
            'progress' => 0,
        ]);

        $completed = $progress->completed_videos ?? [];
        if (!in_array($video->id, $completed)) {
            $completed[] = $video->id;
            $totalVideos = $course->course_videos->count();
            $percentage = $totalVideos > 0 ? floor(count($completed) / $totalVideos * 100) : 0;
            $progress->update([
                'completed_videos' => $completed,
                'progress' => $percentage,
            ]);
        }

        // Generate certificate if eligible
        if ($progress->progress == 100 && $progress->quiz_passed && !$progress->course->certificates()->where('user_id', $user->id)->exists()) {
            $this->generateCertificate($course, $user);
        }

        $certificate = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        return view('front.learning', compact('course', 'video', 'certificate', 'progress'));
    }

    /**
     * Halaman pricing sebelum checkout
     */
    public function pricing(Course $course)
    {
        return view('front.pricing', compact('course'));
    }

    /**
     * Halaman checkout
     */
    public function checkout(Course $course)
    {
        return view('front.checkout', compact('course'));
    }

    private function generateCertificate(Course $course, $user): void
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('certificates.certificate', [
            'course' => $course,
            'user' => $user,
            'date' => now()->toDateString(),
        ]);

        $path = 'certificates/' . $user->id . '_' . $course->id . '.pdf';
        \Illuminate\Support\Facades\Storage::disk('public')->put($path, $pdf->output());

        Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'path' => $path,
            'generated_at' => now(),
        ]);
    }

    /**
     * Proses penyimpanan data saat checkout
     */

      public function checkout_store(StoreSubscribeTransactionRequest $request, Course $course)
     {
         $user = Auth::user();
     
         // Check if the user is already actively subscribed to THIS specific course
         if ($user->hasActiveSubscription($course)) {
             return redirect()->route('front.details', $course->slug)->with('info', 'You are already subscribed to this course.');
         }

         // Check if the user already has a PENDING transaction for THIS specific course
         $existingPendingTransaction = SubscribeTransaction::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('is_paid', false)
            ->exists();

         if ($existingPendingTransaction) {
            return redirect()->route('dashboard')->with('info', 'You already have a pending transaction for this course. Please wait for admin approval.');
         }
     
         DB::transaction(function () use ($request, $user, $course) {
             $validated = $request->validated();
     
             if ($request->hasFile('proof')) {
                 $proofPath = $request->file('proof')->store('proofs', 'public');
                 $validated['proof'] = $proofPath;
             }
     
             $validated['user_id'] = $user->id;
             $validated['course_id'] = $course->id;
             $validated['total_amount'] = $course->price; // Ensures correct price for the specific course
             $validated['is_paid'] = false;
             // subscription_start_date will be null until admin approves
     
             SubscribeTransaction::create($validated);
         });
     
         return redirect()->route('dashboard')->with('success', 'Your transaction is being processed. Please wait for admin approval.');
     }
     
}
