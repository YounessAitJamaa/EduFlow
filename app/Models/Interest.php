<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    public function students()
    {
        return $this->belongsToMany(User::class, 'student_interests', 'interest_id', 'student_id');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_interests', 'interest_id', 'course_id');
    }
}
