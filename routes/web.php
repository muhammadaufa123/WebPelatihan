<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    FrontController,
    ProfileController,
    DashboardController,
    CategoryController,
    CourseController,
    CourseVideoController,
    SubscribeTransactionController,
    TrainerController,
    FinalQuizController,
    QuizAttemptController, // Pastikan ini sudah ada
    TalentAdminController,
    TalentController,
    RecruiterController,
    TalentDiscoveryController,
    CourseMaterialController,
};
// ====================
// FRONTEND ROUTES
// ====================
Route::get('/', [FrontController::class, 'index'])->name('front.index');
Route::get('/courses', [CourseController::class, 'explore'])->name('courses.index');
Route::middleware('auth')->get('/my-courses', [CourseController::class, 'myCourses'])->name('courses.my');
Route::get('/details/{course:slug}', [FrontController::class, 'details'])->name('front.details');
Route::get('/category/{category:slug}', [FrontController::class, 'category'])->name('front.category');

Route::middleware(['auth', 'role:trainee'])->group(function () {
    Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{course:slug}', [\App\Http\Controllers\CartController::class, 'store'])->name('cart.store');
    Route::delete('/cart/{cartItem}', [\App\Http\Controllers\CartController::class, 'destroy'])->name('cart.destroy');
});

Route::middleware(['auth', 'role:trainee'])->group(function () {
    Route::get('/checkout/{course:slug}', [FrontController::class, 'checkout'])->name('front.checkout');
    Route::post('/checkout/{course:slug}/store', [FrontController::class, 'checkout_store'])->name('front.checkout.store');

    // Route untuk menampilkan halaman kuis
    Route::get('/learning/course/{course}/quiz', [QuizAttemptController::class, 'show'])->name('front.quiz');

    // Route untuk submit kuis
    Route::post('/learning/quiz/{quiz}/submit', [QuizAttemptController::class, 'submit'])->name('learning.quiz.submit');
});

Route::get('/pricing/{course:slug}', [FrontController::class, 'pricing'])
    ->where('course', '^(?!checkout$)[a-z0-9\-]+$')
    ->name('front.pricing');

// ====================
// AUTH ROUTES
// ====================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/talent', [ProfileController::class, 'updateTalent'])->name('profile.update-talent');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/learning/{course}/{courseVideoId}', [FrontController::class, 'learning'])
        ->name('front.learning')
        ->middleware('role:trainee|trainer|admin');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ====================
    // ADMIN ROUTES
    // ====================
    Route::prefix('admin')->name('admin.')->group(function () {

        // Admin Only
        Route::middleware('role:admin')->group(function () {
            Route::resource('categories', CategoryController::class);
            Route::resource('trainers', TrainerController::class);
            Route::resource('subscribe_transactions', SubscribeTransactionController::class);
        });

        // Admin + Trainer
        Route::middleware('role:admin|trainer')->group(function () {
            Route::resource('courses', CourseController::class);
            Route::resource('course_videos', CourseVideoController::class);

            // Course Materials
            Route::get('courses/materials', [CourseMaterialController::class, 'index'])->name('materials.index');
            Route::post('courses/materials', [CourseMaterialController::class, 'store'])->name('materials.store');

            Route::get('/add/video/{course:id}', [CourseVideoController::class, 'create'])->name('course.add_video');
            Route::post('/add/video/save/{course:id}', [CourseVideoController::class, 'store'])->name('course.add_video.save');

          // Final Quiz Management Routes
          Route::get('course-quiz', [FinalQuizController::class, 'index'])->name('course_quiz.index');
          Route::get('courses/{course}/quiz/create', [FinalQuizController::class, 'create'])->name('course_quiz.create');
          Route::post('courses/{course}/quiz', [FinalQuizController::class, 'store'])->name('course_quiz.store');
          Route::get('courses/{course}/quiz/edit', [FinalQuizController::class, 'edit'])->name('course_quiz.edit');
          Route::put('courses/{course}/quiz', [FinalQuizController::class, 'update'])->name('course_quiz.update');


        });
    });

    // ====================
    // LEARNING QUIZ (Trainee)
    // ====================
    // Route::middleware('role:trainee')->group(function () { // Grup ini sudah ada di atas, jadi route submit sudah tercakup

        // Contoh route untuk halaman kuis (jika ada yang generik, mungkin tidak diperlukan jika sudah ada yang spesifik)
        // Route::get('/quiz', function () {
        //     return view('front.quiz'); // Pastikan nama view benar
        // })->name('front.quiz');
        // Route::post('/learning/quiz/{quiz}/submit', [QuizAttemptController::class, 'submit'])->name('learning.quiz.submit'); // Sudah didefinisikan di atas
    // });

    // ====================
    // TALENT SCOUTING ROUTES
    // ====================

    // Talent Admin Routes
    Route::middleware('role:talent_admin')->group(function () {
        Route::get('talent-admin/dashboard', [TalentAdminController::class, 'dashboard'])->name('talent_admin.dashboard');
        Route::get('talent-admin/manage-talents', [TalentAdminController::class, 'manageTalents'])->name('talent_admin.manage_talents');
        Route::get('talent-admin/manage-recruiters', [TalentAdminController::class, 'manageRecruiters'])->name('talent_admin.manage_recruiters');
        Route::get('talent-admin/manage-requests', [TalentAdminController::class, 'manageRequests'])->name('talent_admin.manage_requests');
        Route::get('talent-admin/request/{talentRequest}', [TalentAdminController::class, 'showRequest'])->name('talent_admin.show_request');
        Route::patch('talent-admin/request/{talentRequest}/status', [TalentAdminController::class, 'updateRequestStatus'])->name('talent_admin.update_request_status');
        Route::patch('talent-admin/talent/{talent}/toggle-status', [TalentAdminController::class, 'toggleTalentStatus'])->name('talent_admin.toggle_talent_status');
        Route::patch('talent-admin/recruiter/{recruiter}/toggle-status', [TalentAdminController::class, 'toggleRecruiterStatus'])->name('talent_admin.toggle_recruiter_status');
    });

    // Talent Routes
    Route::middleware('role:talent')->group(function () {
        Route::get('talent/dashboard', [TalentController::class, 'dashboard'])->name('talent.dashboard');
    });

    // Recruiter Routes
    Route::middleware('role:recruiter')->group(function () {
        Route::get('recruiter/dashboard', [RecruiterController::class, 'dashboard'])->name('recruiter.dashboard');
        Route::post('recruiter/talent-request', [RecruiterController::class, 'submitTalentRequest'])->name('recruiter.submit_talent_request');
        Route::get('recruiter/my-requests', [RecruiterController::class, 'myRequests'])->name('recruiter.my_requests');

        // Talent Discovery Routes for Recruiters
        Route::prefix('recruiter/discovery')->name('recruiter.discovery.')->group(function () {
            Route::get('/', [TalentDiscoveryController::class, 'index'])->name('index');
            Route::post('/search', [TalentDiscoveryController::class, 'search'])->name('search');
            Route::post('/match', [TalentDiscoveryController::class, 'match'])->name('match');
            Route::get('/recommendations', [TalentDiscoveryController::class, 'recommendations'])->name('recommendations');
            Route::get('/talent/{talent}', [TalentDiscoveryController::class, 'show'])->name('show');
            Route::post('/advanced-search', [TalentDiscoveryController::class, 'advancedSearch'])->name('advanced');
            Route::get('/analytics', [TalentDiscoveryController::class, 'analytics'])->name('analytics');
        });
    });

    // Talent Admin Routes
    Route::middleware('role:talent_admin')->group(function () {
        // Talent Discovery Routes for Admins (full access)
        Route::prefix('admin/discovery')->name('admin.discovery.')->group(function () {
            Route::get('/', [TalentDiscoveryController::class, 'index'])->name('index');
            Route::post('/search', [TalentDiscoveryController::class, 'search'])->name('search');
            Route::post('/match', [TalentDiscoveryController::class, 'match'])->name('match');
            Route::get('/recommendations', [TalentDiscoveryController::class, 'recommendations'])->name('recommendations');
            Route::get('/talent/{talent}', [TalentDiscoveryController::class, 'show'])->name('show');
            Route::post('/advanced-search', [TalentDiscoveryController::class, 'advancedSearch'])->name('advanced');
            Route::get('/analytics', [TalentDiscoveryController::class, 'analytics'])->name('analytics');
        });
    });
});

require __DIR__.'/auth.php';
