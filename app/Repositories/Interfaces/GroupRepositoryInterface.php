<?php

namespace App\Repositories\Interfaces;

use App\Models\Group;

interface GroupRepositoryInterface
{
    public function getAvailableGroupForCourse(int $courseId): ?Group;
    public function createGroup(array $data): Group;
    public function countGroupsForCourse(int $courseId): int;
    public function attachStudentToGroup(Group $group, int $studentId): void;
}
