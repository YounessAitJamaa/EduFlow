<?php

namespace App\Services;

use App\Models\Course;
use App\Models\SavedCourse;
use App\Repositories\Interfaces\SavedCourseRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class SavedCourseService
{
    protected $savedCourseRepository;

    public function __construct(SavedCourseRepositoryInterface $savedCourseRepository)
    {
        $this->savedCourseRepository = $savedCourseRepository;
    }

    public function getMySavedCourses(int $studentId): Collection
    {
        return $this->savedCourseRepository->getStudentSavedCourses($studentId);
    }

    public function saveCourse(Course $course, int $studentId): SavedCourse
    {
        $alreadySaved = $this->savedCourseRepository->getSavedCourse($studentId, $course->id);

        if ($alreadySaved) {
            throw new Exception("Course already saved", 409);
        }

        return $this->savedCourseRepository->saveCourse([
            'student_id' => $studentId,
            'course_id' => $course->id,
        ]);
    }

    public function removeSavedCourse(Course $course, int $studentId): bool
    {
        $savedCourse = $this->savedCourseRepository->getSavedCourse($studentId, $course->id);

        if (!$savedCourse) {
            throw new Exception("Saved Course not found", 404); // Using 404 instead of 409 for missing
        }

        return $this->savedCourseRepository->deleteSavedCourse($savedCourse);
    }
}
