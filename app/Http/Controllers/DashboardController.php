<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseTrainee;
use App\Models\SubscribeTransaction;
use App\Models\Trainer;
use App\Models\User; // Pastikan User model di-import jika belum
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $title = 'Dashboard';
        $roles = '';
        $assignedKelas = []; // Penting: Inisialisasi sebagai array kosong
        $trainees = 0;
        $trainers = 0;
        $courses = 0;

        if ($user->roles_id == 1) { // Admin
            $roles = 'Admin';
            $trainees = CourseTrainee::distinct('user_id')->count('user_id');
            $trainers = Trainer::count();
            $courses = Course::count();
        } elseif ($user->roles_id == 2) { // Trainer (Pengajar)
            $roles = 'Trainer';
            $trainerInstance = Trainer::where('user_id', $user->id)->first();
            $trainerCourses = collect();

            if ($trainerInstance) {
                // Ambil kursus yang dimiliki oleh trainer ini
                $trainerCourses = Course::where('trainer_id', $trainerInstance->id)->get();
                if ($trainerCourses->isNotEmpty()) {
                    foreach ($trainerCourses as $course) {
                        $assignedKelas[] = [
                            'mapel' => $course,
                            'kelas' => [$course]
                        ];
                    }
                } // else: $assignedKelas akan tetap kosong jika trainer tidak punya kursus

                $courseIdsForTrainer = $trainerCourses->pluck('id');
                $trainees = CourseTrainee::whereIn('course_id', $courseIdsForTrainer)
                                    ->distinct('user_id')
                                    ->count('user_id');
            } else {
                $trainees = 0;
            }
            $trainers = Trainer::count();
            $courses = $trainerCourses->count();

        } elseif ($user->roles_id == 3 || $user->hasRole('trainee')) { // Trainee (Siswa)
            $roles = 'Trainee';
            // Pastikan relasi 'courses' ada di model User dan berfungsi
            $traineeCourses = $user->courses()->get();

            if ($traineeCourses->isNotEmpty()) {
                foreach ($traineeCourses as $course) {
                    $assignedKelas[] = [
                        'mapel' => $course,
                        'kelas' => [$course]
                    ];
                }
            } // else: $assignedKelas akan tetap kosong jika trainee tidak terdaftar di kursus manapun

            $trainees = CourseTrainee::distinct('user_id')->count('user_id');
            $trainers = Trainer::count();
            $courses = $traineeCourses->count();
        } elseif ($user->hasRole('talent_admin')) { // Talent Admin
            return redirect()->route('talent_admin.dashboard');
        } elseif ($user->hasRole('recruiter')) { // Recruiter
            return redirect()->route('recruiter.dashboard');
        } elseif ($user->hasRole('talent')) { // Talent (only if no trainee role)
            return redirect()->route('talent.dashboard');
        }

        $categories = Category::count();
        $transactions = SubscribeTransaction::count();

        // Rename variables to match dashboard.blade.php expectations
        $students = $trainees;
        $teachers = $trainers;

        $view = $user->roles_id == 1 ? 'adashboard' : 'dashboard';

        return view($view, compact('title', 'roles', 'assignedKelas', 'categories', 'courses', 'transactions', 'students', 'teachers'));
    }
}
