<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Group;
use App\Repositories\Interfaces\EnrollmentRepositoryInterface;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use App\Services\EnrollmentService;
use Mockery;
use Tests\TestCase;

class EnrollmentServiceTest extends TestCase
{
    protected $enrollmentRepository;
    protected $groupRepository;
    protected $enrollmentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->enrollmentRepository = Mockery::mock(EnrollmentRepositoryInterface::class);
        $this->groupRepository = Mockery::mock(GroupRepositoryInterface::class);
        $this->enrollmentService = new EnrollmentService($this->enrollmentRepository, $this->groupRepository);
    }

    public function test_enroll_student_in_course()
    {
        $studentId = 1;
        $courseId = 10;
        $course = new Course();
        $course->id = $courseId;
        
        $enrollment = new Enrollment(['id' => 1, 'student_id' => $studentId, 'course_id' => $courseId]);
        $group = new Group(['id' => 100, 'name' => 'Group 1']);

        // 1. Check if already enrolled
        $this->enrollmentRepository->shouldReceive('getStudentEnrollmentInCourse')
            ->with($studentId, $courseId)
            ->once()
            ->andReturn(null);

        // 2. Create enrollment
        $this->enrollmentRepository->shouldReceive('createEnrollment')
            ->once()
            ->andReturn($enrollment);

        // 3. Assign to group
        $this->groupRepository->shouldReceive('getAvailableGroupForCourse')
            ->with($courseId)
            ->once()
            ->andReturn($group);

        // 4. Attach student to group
        $this->groupRepository->shouldReceive('attachStudentToGroup')
            ->with($group, $studentId)
            ->once();

        $result = $this->enrollmentService->enrollStudent($course, $studentId);

        $this->assertEquals($enrollment, $result['enrollment']);
        $this->assertEquals($group, $result['group']);
    }

    public function test_enroll_student_already_enrolled_throws_exception()
    {
        $studentId = 1;
        $courseId = 10;
        $course = new Course();
        $course->id = $courseId;
        $enrollment = new Enrollment(['id' => 1]);

        $this->enrollmentRepository->shouldReceive('getStudentEnrollmentInCourse')
            ->with($studentId, $courseId)
            ->once()
            ->andReturn($enrollment);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('You are already Enrolled in this course');

        $this->enrollmentService->enrollStudent($course, $studentId);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
