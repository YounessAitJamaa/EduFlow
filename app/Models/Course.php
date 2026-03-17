<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'title',
        'description',
        'price',
        'teacher_id',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function savdeByStudents()
    {
        return $this->hasMany(SavedCourse::class);
    }

    public function interests()
    {
        return $this->belongsToMany(Interest::class, 'course_interests', 'course_id', 'interest_id');
    }

}
