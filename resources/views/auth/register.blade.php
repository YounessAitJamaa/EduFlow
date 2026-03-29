@extends('layouts.base')

@section('title', 'Register')

@section('content')
<div class="form-card">
    <h2 class="text-center mb-4">Create Account</h2>
    <p class="text-center" style="color: var(--gray); margin-top: -15px; margin-bottom: 30px;">
        Join EduFlow and start your journey today.
    </p>

    <form id="registerForm">
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" placeholder="John Doe" required>
        </div>
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="name@example.com" required>
        </div>
        <div class="form-group">
            <label for="role">I want to join as a</label>
            <select id="role" name="role" required>
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
            </select>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required>
        </div>
        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required>
        </div>
        
        <div id="errorMessage" style="color: #ef4444; font-size: 0.9rem; margin-bottom: 20px; display: none;">
            <!-- Error message will appear here -->
        </div>

        <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Create Account</button>
    </form>

    <div class="text-center mt-4">
        <p style="color: var(--gray);">Already have an account? <a href="/login" style="color: var(--primary); font-weight: 500; text-decoration: none;">Sign in</a></p>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('registerForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const role = document.getElementById('role').value;
        const password = document.getElementById('password').value;
        const password_confirmation = document.getElementById('password_confirmation').value;
        const errorDiv = document.getElementById('errorMessage');
        
        // Clear previous errors
        errorDiv.style.display = 'none';

        if (password !== password_confirmation) {
            errorDiv.textContent = 'Passwords do not match.';
            errorDiv.style.display = 'block';
            return;
        }

        try {
            // Use our api helper
            const response = await api.post('/register', { 
                name, 
                email, 
                role, 
                password, 
                password_confirmation 
            });
            
            if (response && response.token) {
                // Save token and user info
                localStorage.setItem('token', response.token);
                localStorage.setItem('user', JSON.stringify(response.user || response.data)); // Check if structure is nested
                
                // Redirect based on role
                if (role === 'teacher') {
                    window.location.href = '/teacher/dashboard';
                } else {
                    window.location.href = '/dashboard';
                }
            }
        } catch (error) {
            errorDiv.textContent = error.message || 'Registration failed. Please try again.';
            errorDiv.style.display = 'block';
        }
    });
</script>
@endsection
