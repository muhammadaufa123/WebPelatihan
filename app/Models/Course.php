<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Certificate;
use App\Models\CourseModule;

class Course extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'slug',
        'about',
        'path_trailer',
        'thumbnail',
        'price',
        'trainer',
        'category_id',
        'trainer_id',
        'course_mode_id',
        'course_level_id'
    ];

    public function category(){
        return $this->belongsTo(Category::class);

    }
    public function trainer(){
            return $this->belongsTo(Trainer::class);
    }

    public function mode()
    {
        return $this->belongsTo(CourseMode::class, 'course_mode_id');
    }

    public function level()
    {
        return $this->belongsTo(CourseLevel::class, 'course_level_id');
    }

    public function course_videos(){
        return $this->hasMany(CourseVideo::class);
    }

    public function modules()
    {
        return $this->hasMany(CourseModule::class)->orderBy('order');
    }

    public function course_keypoints(){
        return $this->hasMany(CourseKeypoint::class);
    }

    public function modules()
    {
        return $this->hasMany(CourseModule::class);
    }

    // App\Models\Course.php

public function trainees()
{
    return $this->belongsToMany(User::class, 'course_trainees', 'course_id', 'user_id');
}

public function subscribeTransactions()
{
    return $this->hasMany(SubscribeTransaction::class);
}

public function finalQuizzes()
{
    return $this->hasMany(FinalQuiz::class);
}

    public function finalQuiz()
    {
        return $this->hasOne(FinalQuiz::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }


}
