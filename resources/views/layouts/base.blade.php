<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFlow - @yield('title', 'Learning Platform')</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom Style -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- API Helper -->
    <script src="{{ asset('js/api.js') }}"></script>
    @yield('styles')
</head>
<body>
    <nav class="navbar">
        <div class="container nav-content">
            <a href="/" class="logo">Edu<span>Flow</span></a>
            <ul class="nav-links" id="navLinks">
                <!-- Links will be added dynamically based on auth status -->
            </ul>
        </div>
    </nav>

    <main class="container">
        @yield('content')
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 EduFlow. All rights reserved.</p>
        </div>
    </footer>

    <!-- Global Scripts -->
    <script>
        // Simple auth check to update navigation
        document.addEventListener('DOMContentLoaded', () => {
            const token = localStorage.getItem('token');
            const navLinks = document.getElementById('navLinks');
            
            if (token) {
                const user = JSON.parse(localStorage.getItem('user') || '{}');
                navLinks.innerHTML = `
                    <li><a href="/courses">Courses</a></li>
                    ${user.role === 'teacher' ? '<li><a href="/teacher/dashboard">Teacher Dashboard</a></li>' : '<li><a href="/dashboard">My Learning</a></li>'}
                    <li><a href="#" id="logoutBtn" class="btn-secondary">Logout</a></li>
                `;
                
                document.getElementById('logoutBtn').addEventListener('click', (e) => {
                    e.preventDefault();
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                    window.location.href = '/login';
                });
            } else {
                navLinks.innerHTML = `
                    <li><a href="/courses">Browse Courses</a></li>
                    <li><a href="/login">Login</a></li>
                    <li><a href="/register" class="btn-primary">Register</a></li>
                `;
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
