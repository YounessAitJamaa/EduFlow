<?php

namespace App\Providers;

use App\Repositories\Eloquent\AuthRepository;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Repositories\Eloquent\CourseRepository;
use App\Repositories\Interfaces\EnrollmentRepositoryInterface;
use App\Repositories\Eloquent\EnrollmentRepository;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use App\Repositories\Eloquent\GroupRepository;
use App\Repositories\Interfaces\SavedCourseRepositoryInterface;
use App\Repositories\Eloquent\SavedCourseRepository;

use App\Repositories\Interfaces\InterestRepositoryInterface;
use App\Repositories\Eloquent\InterestRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CourseRepositoryInterface::class, CourseRepository::class);
        $this->app->bind(EnrollmentRepositoryInterface::class, EnrollmentRepository::class);
        $this->app->bind(GroupRepositoryInterface::class, GroupRepository::class);
        $this->app->bind(SavedCourseRepositoryInterface::class, SavedCourseRepository::class);
        $this->app->bind(InterestRepositoryInterface::class, InterestRepository::class);
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
