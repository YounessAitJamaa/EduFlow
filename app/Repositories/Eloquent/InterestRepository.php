<?php

namespace App\Repositories\Eloquent;

use App\Models\Course;
use App\Models\User;
use App\Repositories\Interfaces\InterestRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class InterestRepository implements InterestRepositoryInterface
{
    public function syncStudentInterests(User $student, array $interestIds): void
    {
        $student->interests()->sync($interestIds);
    }

    public function getStudentInterests(User $student): Collection
    {
        return $student->interests()->get();
    }

    public function syncCourseInterests(Course $course, array $interestIds): void
    {
        $course->interests()->sync($interestIds);
    }

    public function getCourseInterests(Course $course): Collection
    {
        return $course->interests()->get();
    }

    public function getRecommendedCourses(array $interestIds): Collection
    {
        return Course::with(['teacher', 'interests'])
            ->whereHas('interests', function ($query) use ($interestIds) {
                $query->whereIn('interests.id', $interestIds);
            })
            ->get();
    }

    public function all(): Collection
    {
        return \App\Models\Interest::all();
    }
}
