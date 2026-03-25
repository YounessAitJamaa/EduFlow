<?php

namespace App\Repositories\Eloquent;

use App\Models\Group;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

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

    public function getCourseGroups(int $courseId): Collection
    {
        return Group::where('course_id', $courseId)->get();
    }

    public function getGroupWithStudents(int $groupId): ?Group
    {
        return Group::with('students', 'course')->where('id', $groupId)->first();
    }
}
