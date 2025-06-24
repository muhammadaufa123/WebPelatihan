<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Course;
use App\Models\Category;
use App\Models\CourseLevel;
use App\Models\CourseTrainee;
use App\Models\CourseProgress;
use App\Models\Certificate;
use App\Models\Trainer;
use App\Models\FinalQuiz;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TraineeSeeder extends Seeder
{
    /**
     * Seed a trainee user with completed LMS courses for testing trainee-to-talent conversion.
     * This creates a realistic learning history that will be used to generate talent skills.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $this->command->info('🎓 Creating trainee with LMS completion data...');

        // ===============================================
        // CREATE SAMPLE COURSES IF NEEDED
        // ===============================================
        $this->createSampleCourses();

        // ===============================================
        // CREATE TRAINEE USER
        // ===============================================

        $traineeUser = User::firstOrCreate([
            'email' => 'trainee@test.com'
        ], [
            'name' => 'Lisa Trainee',
            'pekerjaan' => 'Junior Developer',
            'avatar' => null,
            'password' => bcrypt('password123'),
            'available_for_scouting' => false, // Will be enabled when they opt-in
            'talent_skills' => null, // Will be auto-generated from courses
            'hourly_rate' => null,
            'talent_bio' => null,
            'portfolio_url' => null,
            'location' => 'Jakarta, Indonesia',
            'experience_level' => 'beginner',
            'is_active_talent' => false, // Will be enabled after conversion
        ]);        // Assign trainee role if not already assigned
        if (!$traineeUser->hasRole('trainee')) {
            $traineeUser->assignRole('trainee');
        }
        $this->command->info("   ✓ Trainee user created: {$traineeUser->email}");

        // ===============================================
        // ENROLL IN COURSES & COMPLETE THEM
        // ===============================================
          $courses = Course::whereIn('name', [
            'Complete Web Development Bootcamp',
            'Advanced JavaScript Programming',
            'Laravel Framework Mastery',
            'React.js Frontend Development'
        ])->get();

        $completedSkills = [];

        foreach ($courses as $course) {
            // Create course enrollment (trainee relationship)
            $enrollment = CourseTrainee::firstOrCreate([
                'user_id' => $traineeUser->id,
                'course_id' => $course->id,
            ]);

            // Create course progress (100% completed)
            CourseProgress::firstOrCreate([
                'user_id' => $traineeUser->id,
                'course_id' => $course->id,
            ], [
                'completed_videos' => [],
                'completed_materials' => [],
                'completed_tasks' => [],
                'quiz_passed' => true,
                'progress' => 100.0,
            ]);            // Generate certificate
            $path = 'certificates/' . $traineeUser->id . '_' . $course->id . '.pdf';
            Storage::disk('public')->put($path, '');

            Certificate::firstOrCreate([
                'user_id' => $traineeUser->id,
                'course_id' => $course->id,
            ], [
                'path' => $path,
                'generated_at' => $faker->dateTimeBetween('-3 months', '-1 month'),
            ]);

            // Create final quiz for the course if it doesn't exist
            $finalQuiz = FinalQuiz::firstOrCreate([
                'course_id' => $course->id,
            ], [
                'title' => $course->name . ' - Final Assessment',
                'passing_score' => 70,
                'is_hidden_from_trainee' => false,
            ]);

            // Create quiz attempt with a realistic score (70-95%)
            $quizScore = $faker->numberBetween(75, 95);
            QuizAttempt::firstOrCreate([
                'final_quiz_id' => $finalQuiz->id,
                'user_id' => $traineeUser->id,
            ], [
                'score' => $quizScore,
                'is_passed' => $quizScore >= $finalQuiz->passing_score,
            ]);

            // Extract skills from course content for talent profile
            $courseSkills = $this->extractSkillsFromCourse($course);
            $completedSkills = array_merge($completedSkills, $courseSkills);

            $this->command->info("   ✓ Completed course: {$course->name} (Quiz: {$quizScore}%)");
        }

        // ===============================================
        // UPDATE USER WITH LEARNING-BASED PROFILE DATA
        // ===============================================

        $traineeUser->update([
            'talent_skills' => json_encode(array_unique($completedSkills)),
            'talent_bio' => $this->generateLearningBasedBio($traineeUser->name, count($courses)),
            'experience_level' => 'intermediate', // Promoted after completing courses
        ]);

        $this->command->info("   ✓ Generated skills from courses: " . implode(', ', array_slice($completedSkills, 0, 5)) . '...');
        $this->command->info('✅ Trainee with LMS completion data created successfully!');
        $this->command->info('');
        $this->command->info('🧪 TEST SCENARIO:');
        $this->command->info('   1. Login as: trainee@test.com / password123');
        $this->command->info('   2. View completed courses and certificates');
        $this->command->info('   3. Navigate to profile → opt-in as talent');
        $this->command->info('   4. Login again with same credentials');
        $this->command->info('   5. Verify talent role and auto-generated skills');
    }    /**
     * Create sample courses for testing if they don't exist
     */
    private function createSampleCourses(): void
    {
        $faker = Faker::create();        // Ensure we have a default trainer
        $defaultTrainerUser = User::firstOrCreate([
            'email' => 'trainer@demo.test'
        ], [
            'name' => 'Demo Trainer',
            'pekerjaan' => 'Senior Instructor',
            'avatar' => null,
            'password' => bcrypt('password123'),
        ]);        // Assign trainer role if not already assigned
        if (!$defaultTrainerUser->hasRole('trainer')) {
            $defaultTrainerUser->assignRole('trainer');
        }

        $defaultTrainer = Trainer::firstOrCreate([
            'user_id' => $defaultTrainerUser->id
        ], [
            'is_active' => true
        ]);// Ensure we have course levels
        $beginnerLevel = CourseLevel::firstOrCreate(['name' => 'Beginner']);
        $intermediateLevel = CourseLevel::firstOrCreate(['name' => 'Intermediate']);
        $advancedLevel = CourseLevel::firstOrCreate(['name' => 'Advanced']);

        // Ensure we have a default course mode (check if CourseMode exists)
        $onlineMode = null;
        if (class_exists('\App\Models\CourseMode')) {
            $onlineMode = \App\Models\CourseMode::firstOrCreate(['name' => 'Online']);
        }// Ensure we have categories
        $webDevCategory = Category::firstOrCreate([
            'name' => 'Web Development'
        ], [
            'slug' => 'web-development',
            'icon' => 'fas fa-code'
        ]);

        $programmingCategory = Category::firstOrCreate([
            'name' => 'Programming'
        ], [
            'slug' => 'programming',
            'icon' => 'fas fa-laptop-code'
        ]);        // Create sample courses
        $courses = [
            [
                'name' => 'Complete Web Development Bootcamp',
                'slug' => 'complete-web-development-bootcamp',
                'about' => 'Master full-stack web development from HTML/CSS to advanced frameworks',
                'category_id' => $webDevCategory->id,
                'course_level_id' => $intermediateLevel->id,
                'price' => 299.99,
                'skills' => ['HTML', 'CSS', 'JavaScript', 'Bootstrap', 'Git', 'Web Design']
            ],
            [
                'name' => 'Advanced JavaScript Programming',
                'slug' => 'advanced-javascript-programming',
                'about' => 'Deep dive into modern JavaScript ES6+, async programming, and design patterns',
                'category_id' => $programmingCategory->id,
                'course_level_id' => $advancedLevel->id,
                'price' => 199.99,
                'skills' => ['JavaScript', 'ES6+', 'Async Programming', 'Design Patterns', 'DOM Manipulation']
            ],
            [
                'name' => 'Laravel Framework Mastery',
                'slug' => 'laravel-framework-mastery',
                'about' => 'Build modern web applications with Laravel PHP framework',
                'category_id' => $webDevCategory->id,
                'course_level_id' => $advancedLevel->id,
                'price' => 249.99,
                'skills' => ['PHP', 'Laravel', 'MVC Architecture', 'Database Design', 'API Development']
            ],
            [
                'name' => 'React.js Frontend Development',
                'slug' => 'react-js-frontend-development',
                'about' => 'Create interactive user interfaces with React.js and modern tooling',
                'category_id' => $webDevCategory->id,
                'course_level_id' => $intermediateLevel->id,
                'price' => 179.99,
                'skills' => ['React.js', 'JSX', 'Component Architecture', 'State Management', 'Frontend Development']
            ]
        ];

        foreach ($courses as $courseData) {
            $createData = [
                'slug' => $courseData['slug'],
                'about' => $courseData['about'],
                'category_id' => $courseData['category_id'],
                'course_level_id' => $courseData['course_level_id'],
                'price' => $courseData['price'],
                'path_trailer' => '/videos/trailers/' . $courseData['slug'] . '.mp4',
                'thumbnail' => '/images/courses/' . $courseData['slug'] . '.jpg',
                'trainer_id' => $defaultTrainer->id,
                'created_at' => $faker->dateTimeBetween('-1 year', '-6 months'),
                'enrollment_start' => now()->subWeeks(1),
                'enrollment_end' => now()->addWeeks(3),
            ];

            // Add course_mode_id if it exists
            if ($onlineMode) {
                $createData['course_mode_id'] = $onlineMode->id;
            }

            Course::firstOrCreate([
                'name' => $courseData['name']
            ], $createData);
        }

        $this->command->info('   ✓ Sample courses ensured');
    }

    /**
     * Extract relevant skills from course content
     */
    private function extractSkillsFromCourse(Course $course): array
    {
        $skillsMap = [
            'Complete Web Development Bootcamp' => ['HTML', 'CSS', 'JavaScript', 'Bootstrap', 'Git', 'Web Design'],
            'Advanced JavaScript Programming' => ['JavaScript', 'ES6+', 'Async Programming', 'Design Patterns', 'DOM Manipulation'],
            'Laravel Framework Mastery' => ['PHP', 'Laravel', 'MVC Architecture', 'Database Design', 'API Development'],
            'React.js Frontend Development' => ['React.js', 'JSX', 'Component Architecture', 'State Management', 'Frontend Development']
        ];

        return $skillsMap[$course->name] ?? ['Programming', 'Web Development'];
    }

    /**
     * Generate a bio based on learning achievements
     */
    private function generateLearningBasedBio(string $name, int $courseCount): string
    {
        return "Junior developer passionate about continuous learning. Recently completed {$courseCount} comprehensive courses in web development and programming. Looking to apply newly acquired skills in real-world projects and grow as a professional developer. Eager to contribute to innovative teams and tackle challenging technical problems.";
    }
}
