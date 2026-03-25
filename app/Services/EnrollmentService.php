<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Group;
use App\Repositories\Interfaces\EnrollmentRepositoryInterface;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class EnrollmentService
{
    protected $enrollmentRepository;
    protected $groupRepository;

    public function __construct(
        EnrollmentRepositoryInterface $enrollmentRepository,
        GroupRepositoryInterface $groupRepository
    ) {
        $this->enrollmentRepository = $enrollmentRepository;
        $this->groupRepository = $groupRepository;
    }

    public function enrollStudent(Course $course, int $studentId): array
    {
        $existingEnrollment = $this->enrollmentRepository->getStudentEnrollmentInCourse($studentId, $course->id);

        if ($existingEnrollment) {
            throw new Exception("You are already Enrolled in this course", 409);
        }

        $enrollment = $this->enrollmentRepository->createEnrollment([
            'student_id' => $studentId,
            'course_id' => $course->id,
            'status' => 'active',
        ]);

        $group = $this->assignStudentToGroup($studentId, $course->id);

        return [
            'enrollment' => $enrollment,
            'group' => $group,
        ];
    }

    private function assignStudentToGroup(int $studentId, int $courseId): Group
    {
        $group = $this->groupRepository->getAvailableGroupForCourse($courseId);
        
        if (!$group) {
            $groupCount = $this->groupRepository->countGroupsForCourse($courseId);
            $group = $this->groupRepository->createGroup([
                'course_id' => $courseId,
                'name' => 'Group ' . ($groupCount + 1)
            ]);
        }

        $this->groupRepository->attachStudentToGroup($group, $studentId);

        return $group;
    }

    public function leaveCourse(Course $course, int $studentId): bool
    {
        $enrollment = $this->enrollmentRepository->getStudentEnrollmentInCourse($studentId, $course->id);
        
        if (!$enrollment) {
            throw new Exception("Enrollment not found", 404);
        }

        return $this->enrollmentRepository->deleteEnrollment($enrollment);
    }

    public function getMyEnrollments(int $studentId): Collection
    {
        return $this->enrollmentRepository->getStudentEnrollments($studentId);
    }

    public function getCourseStudents(Course $course, int $teacherId): Collection
    {
        if ($course->teacher_id !== $teacherId) {
            throw new Exception("Course not found or access denied", 404);
        }

        $enrollments = $this->enrollmentRepository->getCourseEnrollments($course->id);
        
        return $enrollments->map(function ($enrollment) {
            return [
                'id' => $enrollment->student->id,
                'name' => $enrollment->student->name,
                'email' => $enrollment->student->email,
            ];
        });
    }
}
