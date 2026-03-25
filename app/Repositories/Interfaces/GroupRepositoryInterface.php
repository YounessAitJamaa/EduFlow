<?php

namespace App\Repositories\Interfaces;

use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;

interface GroupRepositoryInterface
{
    public function getAvailableGroupForCourse(int $courseId): ?Group;
    public function createGroup(array $data): Group;
    public function countGroupsForCourse(int $courseId): int;
    public function attachStudentToGroup(Group $group, int $studentId): void;
    public function getCourseGroups(int $courseId): Collection;
    public function getGroupWithStudents(int $groupId): ?Group;
}
