<?php

namespace App\Repositories\Interfaces;

use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Collection;

interface EnrollmentRepositoryInterface
{
    public function getStudentEnrollmentInCourse(int $studentId, int $courseId): ?Enrollment;
    public function createEnrollment(array $data): Enrollment;
    public function deleteEnrollment(Enrollment $enrollment): bool;
    public function getStudentEnrollments(int $studentId): Collection;
    public function getCourseEnrollments(int $courseId): Collection;
}
