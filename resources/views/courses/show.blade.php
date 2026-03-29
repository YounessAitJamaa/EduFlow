@extends('layouts.base')

@section('title', 'Course Details')

@section('content')
<div id="loadingState" class="text-center" style="padding: 100px 0;">
    <div class="spinner"></div>
    <p style="color: var(--gray); margin-top: 20px;">Loading course details...</p>
</div>

<div id="courseDetails" style="display: none;">
    <nav style="margin-bottom: 30px;">
        <a href="/courses" style="text-decoration: none; color: var(--primary); font-weight: 500;">&larr; Back to Courses</a>
    </nav>

    <div class="course-detail-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 40px;">
        <div class="main-info">
            <div class="detail-img" id="detailImg" style="height: 300px; background: linear-gradient(45deg, var(--primary), #818cf8); border-radius: 20px; display: flex; align-items: center; justify-content: center; color: white; font-size: 5rem; margin-bottom: 30px;">
                <!-- Initial will be placed here -->
            </div>
            <h1 id="courseTitle" style="font-size: 2.5rem; margin-bottom: 15px;"></h1>
            <div class="instructor" style="display: flex; align-items: center; gap: 12px; margin-bottom: 30px;">
                <div style="width: 40px; height: 40px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; color: var(--primary);">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div>
                    <span style="color: var(--gray);">Instructor:</span>
                    <span id="instructorName" style="font-weight: 600;"></span>
                </div>
            </div>
            <div class="tabs" style="border-bottom: 1px solid #e2e8f0; margin-bottom: 25px; display: flex; gap: 30px;">
                <a href="#" style="padding-bottom: 10px; border-bottom: 2px solid var(--primary); color: var(--dark); text-decoration: none; font-weight: 600;">Description</a>
                <a href="#" style="padding-bottom: 10px; color: var(--gray); text-decoration: none;">Curriculum</a>
                <a href="#" style="padding-bottom: 10px; color: var(--gray); text-decoration: none;">Reviews</a>
            </div>
            <div id="courseDescription" style="color: #475569; line-height: 1.8; font-size: 1.1rem;">
                <!-- Description will be placed here -->
            </div>
        </div>

        <div class="sidebar">
            <div class="price-card" style="background: white; padding: 30px; border-radius: 20px; box-shadow: var(--shadow); position: sticky; top: 120px; border: 1px solid var(--glass-border);">
                <div style="font-size: 2rem; font-weight: 800; color: var(--primary); margin-bottom: 20px;">
                    $<span id="coursePrice"></span>
                </div>
                <button id="enrollBtn" class="btn-primary" style="width: 100%; padding: 15px; font-size: 1.1rem; margin-bottom: 15px;">Enroll Now</button>
                <button id="wishlistBtn" class="btn-secondary" style="width: 100%; padding: 15px; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; gap: 10px;">
                    <i class="far fa-heart"></i> Save to Wishlist
                </button>
                <div style="margin-top: 25px; border-top: 1px solid #f1f5f9; padding-top: 25px;">
                    <p style="font-weight: 600; margin-bottom: 15px;">This course includes:</p>
                    <ul style="list-style: none; color: #64748b; font-size: 0.95rem;">
                        <li style="margin-bottom: 12px;">
                            <i class="fas fa-check-circle" style="color: var(--secondary); margin-right: 10px;"></i> Full lifetime access
                        </li>
                        <li style="margin-bottom: 12px;">
                            <i class="fas fa-mobile-alt" style="color: var(--primary); margin-right: 12px; width: 14px;"></i> Access on mobile and TV
                        </li>
                        <li style="margin-bottom: 12px;">
                            <i class="fas fa-certificate" style="color: #f59e0b; margin-right: 10px;"></i> Certificate of completion
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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
    const courseId = "{{ $id }}";

    async function loadCourseDetails() {
        const loading = document.getElementById('loadingState');
        const content = document.getElementById('courseDetails');

        try {
            const response = await api.get('/courses/' + courseId);
            const course = response.course;

            if (!course) throw new Error('Course not found');

            // Fill details
            document.getElementById('courseTitle').textContent = course.title;
            document.getElementById('courseDescription').textContent = course.description || 'No description provided for this course.';
            document.getElementById('coursePrice').textContent = parseFloat(course.price).toFixed(2);
            document.getElementById('instructorName').textContent = course.teacher ? course.teacher.name : 'Unknown Instructor';
            document.getElementById('detailImg').textContent = course.title.charAt(0);

            // Check enrollment status ONLY if logged in
            const token = localStorage.getItem('token');
            let isEnrolled = false;
            
            if (token) {
                const enrollmentResponse = await api.get('/enrollments');
                if (enrollmentResponse && enrollmentResponse.enrollments) {
                    isEnrolled = enrollmentResponse.enrollments.some(en => en.course_id == courseId);
                }
            }

            const enrollBtn = document.getElementById('enrollBtn');
            if (isEnrolled) {
                enrollBtn.textContent = 'Continue Learning';
                enrollBtn.classList.add('btn-secondary');
                enrollBtn.classList.remove('btn-primary');
                enrollBtn.onclick = null; // Clear any old listeners
                enrollBtn.addEventListener('click', () => {
                    console.log('Redirecting to dashboard...');
                    window.location.href = '/dashboard';
                });
            } else {
                enrollBtn.onclick = null;
                enrollBtn.addEventListener('click', () => {
                    console.log('Enroll button clicked!');
                    handleEnrollment();
                });
            }

            loading.style.display = 'none';
            content.style.display = 'block';

            // Store course price for enrollment logic
            window.currentCoursePrice = parseFloat(course.price);

        } catch (error) {
            console.error('Course Load Error:', error);
            loading.innerHTML = `<div class="text-center" style="padding: 100px 0;">
                <div style="font-size: 4rem; margin-bottom: 20px; color: #f59e0b;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3>Oops! ${error.message}</h3>
                <a href="/courses" class="btn-primary" style="display: inline-block; margin-top: 20px;">Return to Catalog</a>
            </div>`;
        }
    }

    async function handleEnrollment() {
        const token = localStorage.getItem('token');
        if (!token) {
            window.location.href = '/login';
            return;
        }

        const enrollBtn = document.getElementById('enrollBtn');
        const originalText = enrollBtn.innerHTML;
        enrollBtn.disabled = true;
        enrollBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        try {
            const price = window.currentCoursePrice || 0;

            if (price === 0) {
                // Free course - Direct Enrollment
                await api.post(`/courses/${courseId}/enroll`);
                window.location.href = '/payment/success?course_id=' + courseId;
            } else {
                // Paid course - Stripe Checkout
                const response = await api.post(`/payment/checkout/${courseId}`);
                if (response && response.checkout_url) {
                    window.location.href = response.checkout_url;
                } else {
                    throw new Error('Could not initiate payment. Please try again.');
                }
            }
        } catch (error) {
            console.error('Enrollment Error:', error);
            alert('Error: ' + error.message);
            enrollBtn.disabled = false;
            enrollBtn.innerHTML = originalText;
        }
    }

    document.addEventListener('DOMContentLoaded', loadCourseDetails);
</script>
@endsection
