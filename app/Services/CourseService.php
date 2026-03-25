<?php

namespace App\Services;

use App\Models\Course;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class CourseService
{
    protected $courseRepository;

    public function __construct(CourseRepositoryInterface $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    public function getAllCourses(): Collection
    {
        return $this->courseRepository->getAllCourses();
    }

    public function getCourseById(Course $course): Course
    {
        return $course->load('teacher');
    }

    public function createCourse(array $data, int $teacherId): Course
    {
        $data['teacher_id'] = $teacherId;
        return $this->courseRepository->createCourse($data);
    }

    public function updateCourse(Course $course, array $data, int $teacherId): Course
    {
        if ($course->teacher_id !== $teacherId) {
            throw new Exception("Access Denied", 403);
        }

        $this->courseRepository->updateCourse($course, $data);
        return $course->refresh();
    }

    public function deleteCourse(Course $course, int $teacherId): bool
    {
        if ($course->teacher_id !== $teacherId) {
            throw new Exception("Access Denied", 403);
        }

        return $this->courseRepository->deleteCourse($course);
    }
}
