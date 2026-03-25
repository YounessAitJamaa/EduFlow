<?php

namespace App\Repositories\Eloquent;

use App\Models\Course;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CourseRepository implements CourseRepositoryInterface
{
    public function getAllCourses(): Collection
    {
        return Course::with('teacher')->get();
    }

    public function getCourseById(int $id): ?Course
    {
        return Course::with('teacher')->find($id);
    }

    public function createCourse(array $data): Course
    {
        return Course::create($data);
    }

    public function updateCourse(Course $course, array $data): bool
    {
        return $course->update($data);
    }

    public function deleteCourse(Course $course): bool
    {
        return $course->delete();
    }

    public function getTeacherCoursesWithStats(int $teacherId): Collection
    {
        return Course::where('teacher_id', $teacherId)
            ->withCount(['enrollments', 'groups'])
            ->get();
    }
}
