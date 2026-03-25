<?php

namespace App\Repositories\Interfaces;

use App\Models\Course;
use App\Models\Interest;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface InterestRepositoryInterface
{
    public function syncStudentInterests(User $student, array $interestIds): void;
    public function getStudentInterests(User $student): Collection;
    public function syncCourseInterests(Course $course, array $interestIds): void;
    public function getCourseInterests(Course $course): Collection;
    public function getRecommendedCourses(array $interestIds): Collection;
}
