<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExamInEase - Landing Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to bottom, #5a8dee, #3572d4);
            color: #000;
        }
        .hero-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            max-width: 600px;
            margin: 2rem auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        section {
            padding: 3rem 1rem;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary px-3">
        <a class="navbar-brand fw-bold" href="#">ExamInEase</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#support">Support</a></li>
                @if (Route::has('login'))
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/dashboard') }}">Dashboard</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">Register</a>
                            </li>
                        @endif
                    @endauth
                @endif
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-card text-center">
        <h2 class="fw-bold">ExamInEase</h2>
        <p class="text-muted">
            An all-in-one exam administration platform <br>
            for the College of Information and Computing Sciences.
        </p>
    </div>

    Sections
    <section id="functions" class="bg-light">
        <div class="container">
            <h5 class="fw-bold">Basic Functions *</h5>
            <div class="bg-secondary bg-opacity-25 p-5 rounded mt-3"></div>
        </div>
    </section>

    <section id="mobile" class="bg-info bg-opacity-25">
        <div class="container">
            <h5 class="fw-bold">Mobile App for Students</h5>
            <div class="p-5 rounded mt-3 bg-light"></div>
        </div>
    </section>

    <section id="developers" class="bg-white">
        <div class="container">
            <h5 class="fw-bold">Developers</h5>
            <div class="p-5 rounded mt-3 bg-light"></div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light text-center py-3 mt-auto">
        <small>CSU © 2025, College of Information Sciences | csua@gmail.com</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
