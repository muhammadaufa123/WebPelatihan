<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubscribeTransactionRequest;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SubscribeTransaction;
use Illuminate\Support\Facades\DB;

class FrontController extends Controller
{
    /**
     * Halaman depan menampilkan semua course
     */
    public function index(Request $request)
    {
        $query = Course::with(['category', 'trainer', 'trainees'])->orderByDesc('id');

        if ($request->filled('course_type')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('course_type', $request->course_type);
            });
        }

        if ($request->filled('level')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('level', $request->level);
            });
        }

        $courses = $query->get();
        $categories = Category::all();

        return view('front.index', compact('courses', 'categories'));
    }

    /**
     * Detail course
     */
    public function details(Course $course)
    {
        return view('front.details', compact('course'));
    }

    public function category(Request $request, Category $category)
    {
        $query = $category->courses();

        if ($request->filled('course_type')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('course_type', $request->course_type);
            });
        }

        if ($request->filled('level')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('level', $request->level);
            });
        }

        $courses = $query->get();

        $otherCategories = Category::where('id', '!=', $category->id)->get();

        return view('front.category', compact('courses', 'category', 'otherCategories'));
    }

    /**
     * Halaman pembelajaran untuk course tertentu
     */
    public function learning(Course $course, $courseVideoId)
    {
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

        // Ambil video dari relasi
        $video = $course->course_videos->firstWhere('id', $courseVideoId);

        // Jika video tidak ditemukan, lempar 404
        if (!$video) {
            abort(404, 'Video tidak ditemukan.');
        }

        // Tambahkan course ke relasi user jika belum ada (untuk pelacakan/history)
        $user->courses()->syncWithoutDetaching($course->id);

        return view('front.learning', compact('course', 'video'));
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
