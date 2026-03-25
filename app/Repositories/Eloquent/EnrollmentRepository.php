<?php

namespace App\Repositories\Eloquent;

use App\Models\Enrollment;
use App\Repositories\Interfaces\EnrollmentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EnrollmentRepository implements EnrollmentRepositoryInterface
{
    public function getStudentEnrollmentInCourse(int $studentId, int $courseId): ?Enrollment
    {
        return Enrollment::where('student_id', $studentId)
                         ->where('course_id', $courseId)
                         ->first();
    }

    public function createEnrollment(array $data): Enrollment
    {
        return Enrollment::create($data);
    }

    public function deleteEnrollment(Enrollment $enrollment): bool
    {
        return $enrollment->delete();
    }

    public function getStudentEnrollments(int $studentId): Collection
    {
        return Enrollment::with('course')->where('student_id', $studentId)->get();
    }

    public function getCourseEnrollments(int $courseId): Collection
    {
        return Enrollment::with('student')->where('course_id', $courseId)->get();
    }
}
