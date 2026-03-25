<?php

namespace App\Services;

use App\Repositories\Interfaces\CourseRepositoryInterface;

class StatisticsService
{
    protected $courseRepository;

    public function __construct(CourseRepositoryInterface $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    public function getTeacherStatistics(int $teacherId): array
    {
        $courses = $this->courseRepository->getTeacherCoursesWithStats($teacherId);

        $totalCourses = $courses->count();
        $totalStudents = $courses->sum('enrollments_count');

        $coursesBreakdown = $courses->map(function ($course) {
            return [
                'id' => $course->id,
                'title' => $course->title,
                'enrollments' => $course->enrollments_count,
                'groups' => $course->groups_count,
                'revenue' => $course->enrollments_count * $course->price,
            ];
        });

        $mostPopularCourse = $courses->sortByDesc('enrollments_count')->first();

        return [
            'overview' => [
                'total_courses' => $totalCourses,
                'total_students' => $totalStudents,
                'most_popular_course' => $mostPopularCourse ? [
                    'id' => $mostPopularCourse->id,
                    'title' => $mostPopularCourse->title,
                    'enrollments' => $mostPopularCourse->enrollments_count,
                ] : null,
            ],
            'courses_breakdown' => $coursesBreakdown->values()->all(),
        ];
    }
}
