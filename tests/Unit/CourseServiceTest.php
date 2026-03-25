<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\User;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Services\CourseService;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

class CourseServiceTest extends TestCase
{
    protected $courseRepository;
    protected $courseService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->courseRepository = Mockery::mock(CourseRepositoryInterface::class);
        $this->courseService = new CourseService($this->courseRepository);
    }

    public function test_get_all_courses_returns_collection()
    {
        $this->courseRepository->shouldReceive('getAllCourses')
            ->once()
            ->andReturn(new Collection());

        $result = $this->courseService->getAllCourses();

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function test_get_course_by_id_returns_course()
    {
        $course = new Course(['id' => 1]);
        $result = $this->courseService->getCourseById($course);

        $this->assertEquals($course->id, $result->id);
    }

    public function test_create_course_calls_repository()
    {
        $data = ['title' => 'New Course'];
        $teacherId = 1;
        $course = new Course($data + ['teacher_id' => $teacherId]);
        
        $this->courseRepository->shouldReceive('createCourse')
            ->once()
            ->andReturn($course);

        $result = $this->courseService->createCourse($data, $teacherId);

        $this->assertEquals('New Course', $result->title);
        $this->assertEquals($teacherId, $result->teacher_id);
    }

    public function test_update_course_calls_repository()
    {
        $course = new Course(['id' => 1, 'teacher_id' => 1]);
        $data = ['title' => 'Updated Title'];

        $this->courseRepository->shouldReceive('updateCourse')
            ->with($course, $data)
            ->once()
            ->andReturn(true);

        $result = $this->courseService->updateCourse($course, $data, 1);

        $this->assertInstanceOf(Course::class, $result);
    }

    public function test_update_course_denies_access_for_wrong_teacher()
    {
        $course = new Course(['id' => 1, 'teacher_id' => 1]);
        $data = ['title' => 'Updated Title'];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Access Denied');

        $this->courseService->updateCourse($course, $data, 2);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
