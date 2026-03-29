@extends('layouts.base')

@section('title', 'Student Dashboard')

@section('content')
<div class="dashboard-container">
    <header class="mb-4" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>Welcome back, <span id="userName" style="color: var(--primary);">Student</span>!</h1>
            <p style="color: var(--gray);">Here's what's happening with your learning journey.</p>
        </div>
        <a href="/student/interests" class="btn-secondary" style="text-decoration: none; padding: 10px 20px; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-tags"></i> Edit Interests
        </a>
    </header>

    <div class="stats-grid mb-4" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
        <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid var(--glass-border); display: flex; align-items: center; gap: 15px;">
            <div style="width: 50px; height: 50px; background: rgba(79, 70, 229, 0.1); color: var(--primary); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div>
                <div style="font-size: 1.5rem; font-weight: 700;" id="courseCount">0</div>
                <div style="color: var(--gray); font-size: 0.9rem;">Enrolled Courses</div>
            </div>
        </div>
        <!-- Add more stats if needed -->
    </div>

    <section class="mb-4">
        <h2 style="font-size: 1.5rem; margin-bottom: 20px;">My Learning</h2>
        <div id="enrolledCourses" class="courses-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px;">
            <!-- Enrolled courses will be injected here -->
            <div class="text-center" style="grid-column: 1/-1; padding: 40px;">
                <p style="color: var(--gray);">Loading your courses...</p>
            </div>
        </div>
    </section>

    <section>
        <h2 style="font-size: 1.5rem; margin-bottom: 20px;">Suggestions for You</h2>
        <div id="recommendedCourses" class="courses-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px;">
            <!-- Recommended courses will be injected here -->
        </div>
    </section>
</div>

<style>
    .dashboard-course-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        transition: var(--transition);
        display: flex;
        flex-direction: column;
    }
    .dashboard-course-card:hover { border-color: var(--primary); transform: translateY(-3px); }
    .course-mini-img { height: 120px; background: linear-gradient(45deg, var(--primary), #818cf8); display: flex; align-items: center; justify-content: center; color: white; font-size: 2.5rem; }
    .course-mini-body { padding: 20px; flex-grow: 1; }
    .course-mini-title { font-weight: 700; margin-bottom: 10px; }
    .course-mini-btn { width: 100%; border-radius: 6px; padding: 8px; font-size: 0.9rem; text-decoration: none; display: inline-block; text-align: center; }
</style>
@endsection

@section('scripts')
<script>
    async function loadDashboard() {
        // Set user name from local storage
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        if (user.name) {
            document.getElementById('userName').textContent = user.name;
        }

        try {
            // Fetch Enrollments
            const enrollmentResponse = await api.get('/enrollments');
            const enrollments = enrollmentResponse.enrollments || [];
            document.getElementById('courseCount').textContent = enrollments.length;
            renderEnrolled(enrollments);

            // Fetch Recommendations
            const recommendationResponse = await api.get('/student/recommended-courses');
            const recommended = recommendationResponse.recommended_courses || [];
            renderRecommended(recommended);

        } catch (error) {
            console.error('Dashboard error:', error);
        }
    }

    function renderEnrolled(enrollments) {
        const container = document.getElementById('enrolledCourses');
        if (enrollments.length === 0) {
            container.innerHTML = `
                <div class="text-center" style="grid-column: 1/-1; padding: 40px; background: white; border-radius: 12px; border: 1px dashed var(--gray);">
                    <p style="color: var(--gray);">You aren't enrolled in any courses yet.</p>
                    <a href="/courses" class="btn-primary mt-4" style="display: inline-block; text-decoration: none;">Browse Catalog</a>
                </div>`;
            return;
        }

        container.innerHTML = enrollments.map(en => `
            <div class="dashboard-course-card">
                <div class="course-mini-img"><i class="fas fa-book"></i></div>
                <div class="course-mini-body">
                    <h3 class="course-mini-title">${en.course.title}</h3>
                    <p style="color: var(--gray); font-size: 0.85rem; margin-bottom: 15px;">Progress: 0%</p>
                    <a href="/courses/${en.course.id}" class="btn-primary course-mini-btn">Continue Learning</a>
                </div>
            </div>
        `).join('');
    }

    function renderRecommended(courses) {
        const container = document.getElementById('recommendedCourses');
        if (courses.length === 0) {
            container.innerHTML = `<p style="color: var(--gray);">Setting up interests to get better suggestions.</p>`;
            return;
        }

        container.innerHTML = courses.map(c => `
            <div class="dashboard-course-card">
                <div class="course-mini-img" style="background: linear-gradient(45deg, var(--secondary), #34d399); font-size: 1.5rem;"><i class="fas fa-star text-white"></i></div>
                <div class="course-mini-body">
                    <h4 class="course-mini-title">${c.title}</h4>
                    <p style="color: var(--gray); font-size: 0.85rem; margin-bottom: 12px;">Based on your interests</p>
                    <a href="/courses/${c.id}" class="btn-secondary course-mini-btn">View Course</a>
                </div>
            </div>
        `).join('');
    }

    document.addEventListener('DOMContentLoaded', loadDashboard);
</script>
@endsection
