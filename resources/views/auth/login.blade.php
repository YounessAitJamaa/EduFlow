@extends('layouts.base')

@section('title', 'Login')

@section('content')
<div class="form-card">
    <h2 class="text-center mb-4">Welcome Back</h2>
    <p class="text-center" style="color: var(--gray); margin-top: -15px; margin-bottom: 30px;">
        Please enter your details to sign in.
    </p>

    <form id="loginForm">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="name@example.com" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required>
        </div>
        
        <div id="errorMessage" style="color: #ef4444; font-size: 0.9rem; margin-bottom: 20px; display: none;">
            <!-- Error message will appear here -->
        </div>

        <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Sign In</button>
    </form>

    <div class="text-center mt-4">
        <p style="color: var(--gray);">Don't have an account? <a href="/register" style="color: var(--primary); font-weight: 500; text-decoration: none;">Create one</a></p>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('loginForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const errorDiv = document.getElementById('errorMessage');
        
        // Clear previous errors
        errorDiv.style.display = 'none';

        try {
            // Use our api helper
            const response = await api.post('/login', { email, password });
            
            if (response && response.token) {
                // Save token and user info
                localStorage.setItem('token', response.token);
                localStorage.setItem('user', JSON.stringify(response.user));
                
                // Redirect based on role
                if (response.user.role === 'teacher') {
                    window.location.href = '/teacher/dashboard';
                } else {
                    window.location.href = '/dashboard';
                }
            }
        } catch (error) {
            errorDiv.textContent = error.message || 'Verification failed. Please check your credentials.';
            errorDiv.style.display = 'block';
        }
    });
</script>
@endsection
