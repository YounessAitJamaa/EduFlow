@extends('layouts.base')

@section('title', 'Browse Courses')

@section('content')
<div class="courses-header" style="margin-bottom: 40px; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 style="font-size: 2.5rem;">Explore <span style="color: var(--primary);">Courses</span></h1>
        <p style="color: var(--gray);">Find the perfect course to advance your career.</p>
    </div>
    <div class="search-box">
        <input type="text" id="courseSearch" placeholder="Search courses..." style="padding: 12px 20px; border-radius: 30px; border: 1.5px solid #e2e8f0; width: 300px;">
    </div>
</div>

<div id="loadingState" class="text-center" style="padding: 100px 0;">
    <div class="spinner"></div>
    <p style="color: var(--gray); margin-top: 20px;">Fetching the latest courses for you...</p>
</div>

<div id="coursesGrid" class="courses-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px; display: none;">
    <!-- Course cards will be injected here -->
</div>

<div id="noCourses" class="text-center" style="display: none; padding: 100px 0;">
    <div style="font-size: 4rem; margin-bottom: 20px; color: var(--gray);">
        <i class="fas fa-search"></i>
    </div>
    <h3>No courses found</h3>
    <p style="color: var(--gray);">Try adjusting your search or check back later.</p>
</div>

<style>
    .course-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: var(--shadow);
        transition: var(--transition);
        display: flex;
        flex-direction: column;
        border: 1px solid var(--glass-border);
    }
    .course-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .course-img {
        height: 180px;
        background: linear-gradient(45deg, var(--primary), #818cf8);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 3rem;
    }
    .course-body {
        padding: 24px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .course-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 12px;
        color: var(--dark);
    }
    .course-desc {
        color: var(--gray);
        font-size: 0.95rem;
        margin-bottom: 20px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .course-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
        padding-top: 20px;
        border-top: 1px solid #f1f5f9;
    }
    .course-price {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--primary);
    }
    
    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid rgba(79, 70, 229, 0.1);
        border-left-color: var(--primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>
@endsection

@section('scripts')
<script>
    let allCourses = [];

    async function loadCourses() {
        const loading = document.getElementById('loadingState');
        const grid = document.getElementById('coursesGrid');
        const noCourses = document.getElementById('noCourses');

        try {
            const response = await api.get('/courses');
            allCourses = response.courses || [];
            
            loading.style.display = 'none';
            
            if (allCourses.length === 0) {
                noCourses.style.display = 'block';
                grid.style.display = 'none';
            } else {
                renderCourses(allCourses);
                grid.style.display = 'grid';
                noCourses.style.display = 'none';
            }
        } catch (error) {
            loading.innerHTML = `<p style="color: #ef4444;">Error: ${error.message}</p>`;
        }
    }

    function renderCourses(courses) {
        const grid = document.getElementById('coursesGrid');
        grid.innerHTML = courses.map(course => `
            <div class="course-card">
                <div class="course-img">
                    <i class="fas fa-book"></i>
                </div>
                <div class="course-body">
                    <h3 class="course-title">${course.title}</h3>
                    <p class="course-desc">${course.description || 'No description available for this course yet.'}</p>
                    <div class="course-footer">
                        <span class="course-price">$${parseFloat(course.price).toFixed(2)}</span>
                        <a href="/courses/${course.id}" class="btn-primary" style="padding: 8px 20px; font-size: 0.9rem;">View Details</a>
                    </div>
                </div>
            </div>
        `).join('');
    }

    // Search functionality
    document.getElementById('courseSearch').addEventListener('input', (e) => {
        const term = e.target.value.toLowerCase();
        const filtered = allCourses.filter(c => 
            c.title.toLowerCase().includes(term) || 
            (c.description && c.description.toLowerCase().includes(term))
        );
        renderCourses(filtered);
        
        const noCourses = document.getElementById('noCourses');
        const grid = document.getElementById('coursesGrid');
        if (filtered.length === 0) {
            noCourses.style.display = 'block';
            grid.style.display = 'none';
        } else {
            noCourses.style.display = 'none';
            grid.style.display = 'grid';
        }
    });

    document.addEventListener('DOMContentLoaded', loadCourses);
</script>
@endsection
