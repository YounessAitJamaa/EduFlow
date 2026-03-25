<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use App\Repositories\Interfaces\InterestRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class InterestService
{
    protected $interestRepository;

    public function __construct(InterestRepositoryInterface $interestRepository)
    {
        $this->interestRepository = $interestRepository;
    }

    public function syncStudentInterests(User $student, array $interestIds): Collection
    {
        $this->interestRepository->syncStudentInterests($student, $interestIds);
        return $this->interestRepository->getStudentInterests($student);
    }

    public function getStudentInterests(User $student): Collection
    {
        return $this->interestRepository->getStudentInterests($student);
    }

    public function attachCourseInterests(Course $course, array $interestIds, int $teacherId): Collection
    {
        if ($course->teacher_id !== $teacherId) {
            throw new Exception("Access denied", 403);
        }

        $this->interestRepository->syncCourseInterests($course, $interestIds);
        return $this->interestRepository->getCourseInterests($course);
    }

    public function getCourseInterests(Course $course): Collection
    {
        return $this->interestRepository->getCourseInterests($course);
    }

    public function getRecommendedCourses(User $student): Collection
    {
        $interestIds = $this->interestRepository->getStudentInterests($student)->pluck('id')->toArray();

        if (empty($interestIds)) {
            return collect([]);
        }

        return $this->interestRepository->getRecommendedCourses($interestIds);
    }
}
