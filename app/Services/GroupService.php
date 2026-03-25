<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Group;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class GroupService
{
    protected $groupRepository;

    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function getCourseGroups(Course $course, int $teacherId): Collection
    {
        if ($course->teacher_id !== $teacherId) {
            throw new Exception("Course not found or access denied", 404);
        }

        return $this->groupRepository->getCourseGroups($course->id);
    }

    public function getGroupParticipants(Group $group, int $teacherId): array
    {
        $groupWithRelations = $this->groupRepository->getGroupWithStudents($group->id);

        if (!$groupWithRelations || $groupWithRelations->course->teacher_id !== $teacherId) {
            throw new Exception("Group not found or access denied", 404);
        }

        $students = $groupWithRelations->students->map(function ($student) {
            return [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
            ];
        });

        return [
            'group id' => $groupWithRelations->id,
            'group name' => $groupWithRelations->name,
            'course id' => $groupWithRelations->course->id,
            'course title' => $groupWithRelations->course->title,
            'students' => $students
        ];
    }
}
