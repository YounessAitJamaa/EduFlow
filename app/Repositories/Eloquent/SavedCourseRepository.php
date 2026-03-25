<?php

namespace App\Repositories\Eloquent;

use App\Models\SavedCourse;
use App\Repositories\Interfaces\SavedCourseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SavedCourseRepository implements SavedCourseRepositoryInterface
{
    public function getStudentSavedCourses(int $studentId): Collection
    {
        return SavedCourse::with(['course', 'student'])
            ->where('student_id', $studentId)
            ->get();
    }

    public function getSavedCourse(int $studentId, int $courseId): ?SavedCourse
    {
        return SavedCourse::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->first();
    }

    public function saveCourse(array $data): SavedCourse
    {
        return SavedCourse::create($data);
    }

    public function deleteSavedCourse(SavedCourse $savedCourse): bool
    {
        return $savedCourse->delete();
    }
}
