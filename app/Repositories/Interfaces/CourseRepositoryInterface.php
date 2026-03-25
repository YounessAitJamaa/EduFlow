<?php

namespace App\Repositories\Interfaces;

use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;

interface CourseRepositoryInterface
{
    public function getAllCourses(): Collection;
    public function getCourseById(int $id): ?Course;
    public function createCourse(array $data): Course;
    public function updateCourse(Course $course, array $data): bool;
    public function deleteCourse(Course $course): bool;
    public function getTeacherCoursesWithStats(int $teacherId): Collection;
}
