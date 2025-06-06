<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Concerns\HasUniqueStringIds;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'avatar',
        'pekerjaan',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function courses(){
        return $this->belongsToMany(Course::class, 'course_trainees');
    }

    public function subscribe_transaction(){
        return $this->hasMany(SubscribeTransaction::class);
    }

    public function hasActiveSubscription(?Course $course = null)
    {
        $query = SubscribeTransaction::where('user_id', $this->id)
            ->where('is_paid', true);

        if ($course) {
            $query->where('course_id', $course->id); // hanya boleh akses kelas yang dibayarkan
        }

        return $query->exists();
    }

    public function trainer()
    {
        return $this->hasOne(Trainer::class);
    }

    // New talent scouting relationships
    public function talentAdmin()
    {
        return $this->hasOne(TalentAdmin::class);
    }

    public function talent()
    {
        return $this->hasOne(Talent::class);
    }

    public function recruiter()
    {
        return $this->hasOne(Recruiter::class);
    }

}
