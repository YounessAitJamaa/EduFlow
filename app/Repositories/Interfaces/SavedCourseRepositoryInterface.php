<?php

namespace App\Repositories\Interfaces;

use App\Models\SavedCourse;
use Illuminate\Database\Eloquent\Collection;

interface SavedCourseRepositoryInterface
{
    public function getStudentSavedCourses(int $studentId): Collection;
    public function getSavedCourse(int $studentId, int $courseId): ?SavedCourse;
    public function saveCourse(array $data): SavedCourse;
    public function deleteSavedCourse(SavedCourse $savedCourse): bool;
}
