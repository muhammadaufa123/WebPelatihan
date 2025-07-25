<?php

namespace Database\Factories;

use App\Models\Recruiter;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecruiterFactory extends Factory
{
    protected $model = Recruiter::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'company_name' => $this->faker->company(),
            'company_size' => $this->faker->randomElement(['1-10', '11-50', '51-200', '201-500', '500+']),
            'industry' => $this->faker->randomElement(['Technology', 'Finance', 'Healthcare', 'Education', 'Marketing']),
            'job_title' => $this->faker->jobTitle(),
            'company_website' => $this->faker->url(),
            'company_description' => $this->faker->paragraph(),
            'verification_status' => $this->faker->randomElement(['pending', 'verified', 'rejected']),
        ];
    }
}
