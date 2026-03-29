@extends('layouts.base')

@section('title', 'Welcome to EduFlow')

@section('content')
<div class="hero-section text-center" style="padding: 80px 0;">
    <h1 style="font-size: 3.5rem; margin-bottom: 20px;">Master New Skills with <span style="color: var(--primary);">EduFlow</span></h1>
    <p style="font-size: 1.2rem; color: var(--gray); max-width: 700px; margin: 0 auto 40px auto;">
        Join thousands of students and expert teachers in a modern learning experience. 
        Interactive courses, personal mentorship, and career growth.
    </p>
    <div class="hero-btns" style="display: flex; gap: 20px; justify-content: center;">
        <a href="/register" class="btn-primary" style="font-size: 1.1rem; padding: 15px 40px;">Get Started for Free</a>
        <a href="/courses" class="btn-secondary" style="font-size: 1.1rem; padding: 15px 40px;">Browse Courses</a>
    </div>
</div>

<div class="features-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; margin-top: 60px;">
    <div style="background: white; padding: 30px; border-radius: 16px; box-shadow: var(--shadow); text-align: center;">
        <div style="font-size: 2.5rem; margin-bottom: 15px;">📚</div>
        <h3>Quality Content</h3>
        <p style="color: var(--gray);">Access courses created by industry experts and experienced educators.</p>
    </div>
    <div style="background: white; padding: 30px; border-radius: 16px; box-shadow: var(--shadow); text-align: center;">
        <div style="font-size: 2.5rem; margin-bottom: 15px;">🔒</div>
        <h3>Secure Learning</h3>
        <p style="color: var(--gray);">Safe payments and verified teacher profiles for a peace of mind.</p>
    </div>
    <div style="background: white; padding: 30px; border-radius: 16px; box-shadow: var(--shadow); text-align: center;">
        <div style="font-size: 2.5rem; margin-bottom: 15px;">📈</div>
        <h3>Track Progress</h3>
        <p style="color: var(--gray);">Detailed statistics and progress tracking for both students and teachers.</p>
    </div>
</div>
@endsection
