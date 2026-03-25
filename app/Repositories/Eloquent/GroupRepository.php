<?php

namespace App\Repositories\Eloquent;

use App\Models\Group;
use App\Repositories\Interfaces\GroupRepositoryInterface;

class GroupRepository implements GroupRepositoryInterface
{
    public function getAvailableGroupForCourse(int $courseId): ?Group
    {
        return Group::where('course_id', $courseId)
            ->withCount('students')
            ->orderBy('id', 'desc')
            ->get()
            ->first(fn($g) => $g->students_count < 25);
    }

    public function createGroup(array $data): Group
    {
        return Group::create($data);
    }

    public function countGroupsForCourse(int $courseId): int
    {
        return Group::where('course_id', $courseId)->count();
    }

    public function attachStudentToGroup(Group $group, int $studentId): void
    {
        $group->students()->attach($studentId);
    }
}
