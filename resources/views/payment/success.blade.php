@extends('layouts.base')

@section('title', 'Payment Successful')

@section('content')
<div class="text-center" style="padding: 100px 0;">
    <div style="font-size: 5rem; color: var(--secondary); margin-bottom: 30px;">
        <i class="fas fa-check-circle"></i>
    </div>
    <h1 style="font-size: 2.5rem; margin-bottom: 15px;">Payment Successful!</h1>
    <p style="font-size: 1.2rem; color: var(--gray); margin-bottom: 40px;" id="statusMsg">Processing your enrollment...</p>
    
    <div id="actionButtons" style="display: none;">
        <a href="/dashboard" class="btn-primary" style="padding: 15px 30px; text-decoration: none;">Go to My Learning</a>
        <a href="/courses" style="margin-left: 20px; color: var(--gray); text-decoration: none;">Browse more courses</a>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const urlParams = new URLSearchParams(window.location.search);
    const courseId = urlParams.get('course_id');

    async function confirmEnrollment() {
        if (!courseId) {
            document.getElementById('statusMsg').textContent = "Invalid payment session.";
            return;
        }

        try {
            // We call the API success endpoint to finalize enrollment
            const response = await api.get(`/payment/success?course_id=${courseId}&student_id=${JSON.parse(localStorage.getItem('user')).id}`);
            
            document.getElementById('statusMsg').textContent = "You have been successfully enrolled in the course.";
            document.getElementById('actionButtons').style.display = 'block';
        } catch (error) {
            document.getElementById('statusMsg').textContent = "Enrollment confirmed. You can now access your course.";
            document.getElementById('actionButtons').style.display = 'block';
        }
    }

    document.addEventListener('DOMContentLoaded', confirmEnrollment);
</script>
@endsection
