<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExamInEase - Modern Exam Administration Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #5f9eb7 0%, #4a7a8f 100%);
            --secondary-gradient: linear-gradient(135deg, #7bb3c9 0%, #5f9eb7 100%);
            --dark-bg: #1a1a2e;
            --light-text: #ffffff;
        }
        body {
            background: linear-gradient(135deg, #7bb3c9 0%, #5f9eb7 100%);
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        /* Modern Header */
        .modern-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .modern-header .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .btn-login {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(95, 158, 183, 0.4);
            color: white;
        }
        .btn-dashboard {
            background: var(--secondary-gradient);
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .btn-dashboard:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(123, 179, 201, 0.4);
            color: white;
        }
        /* Hero Section */
        .hero-section {
            padding: 5rem 1rem;
            text-align: center;
        }
        .hero-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 3rem 2rem;
            max-width: 700px;
            margin: 0 auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
        }
        .hero-card h1 {
            font-size: 3rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }
        .hero-card p {
            font-size: 1.2rem;
            color: #666;
            line-height: 1.8;
        }
        /* Features Section */
        .features-section {
            padding: 4rem 1rem;
            background: rgba(255, 255, 255, 0.1);
        }
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }
        .feature-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 1rem;
            background: var(--primary-gradient);
            color: white;
        }
        .feature-card h4 {
            font-weight: 700;
            margin-bottom: 1rem;
            color: #333;
        }
        .feature-card p {
            color: #666;
            line-height: 1.6;
        }
        /* Developers Section */
        .developers-section {
            padding: 4rem 1rem;
            background: rgba(255, 255, 255, 0.95);
        }
        .section-title {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 3rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .developer-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            height: 100%;
        }
        .developer-card:hover {
            transform: translateY(-5px);
        }
        .developer-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
        }
        .lead-dev .developer-avatar {
            background: var(--primary-gradient);
        }
        .assistant-dev .developer-avatar {
            background: var(--secondary-gradient);
        }
        .developer-card h5 {
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }
        .developer-card .role {
            color: #5f9eb7;
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .developer-card .course {
            color: #999;
            font-size: 0.9rem;
        }
        /* Footer */
        .modern-footer {
            background: rgba(26, 26, 46, 0.95);
            color: white;
            padding: 2rem 1rem;
            text-align: center;
            margin-top: auto;
        }
        .modern-footer p {
            margin: 0;
            color: rgba(255, 255, 255, 0.8);
        }
        /* Responsive */
        @media (max-width: 768px) {
            .hero-card h1 {
                font-size: 2rem;
            }
            .hero-card p {
                font-size: 1rem;
            }
            .section-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body class="d-flex flex-column">

    <!-- Modern Header -->
    <nav class="navbar navbar-expand-lg modern-header">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-graduation-cap"></i> ExamInEase
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <?php if(Route::has('login')): ?>
                        <?php if(auth()->guard()->check()): ?>
                            <li class="nav-item">
                                <a class="btn btn-dashboard" href="<?php echo e(url('/dashboard')); ?>">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="btn btn-login" href="<?php echo e(route('login')); ?>">
                                    <i class="fas fa-sign-in-alt"></i> Login
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-card">
            <h1>ExamInEase</h1>
            <p>
                A modern, all-in-one exam administration platform designed for the College of Information and Computing Sciences. Streamline your exam management with powerful tools and intuitive design.
            </p>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title" style="color: white;">Features</h2>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h4>Exam Management</h4>
                        <p>Create, organize, and manage exams effortlessly with our comprehensive administration tools. Schedule exams, set time limits, and monitor progress in real-time.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>Analytics & Reporting</h4>
                        <p>Gain insights with detailed analytics and automated reporting. Track student performance, identify trends, and make data-driven decisions.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Developers Section -->
    <section class="developers-section">
        <div class="container">
            <h2 class="section-title">Meet the Developers</h2>
            <div class="row justify-content-center">
                <div class="col-md-4 mb-4">
                    <div class="developer-card lead-dev">
                        <div class="developer-avatar">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h5>Elian Benjamin Aglugub</h5>
                        <p class="role">Lead Developer</p>
                        <p class="course">BSIT - 4F</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="developer-card assistant-dev">
                        <div class="developer-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <h5>Angelika Mae F. Delos Santos</h5>
                        <p class="role">Assistant Developer</p>
                        <p class="course">BSIT - 4A</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="developer-card assistant-dev">
                        <div class="developer-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <h5>Gladys Ann P. Daniel</h5>
                        <p class="role">Assistant Developer</p>
                        <p class="course">BSIT - 4A</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="modern-footer">
        <div class="container">
            <p><i class="fas fa-university"></i> CSU © 2025, College of Information Sciences | csua@gmail.com</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</html>
<?php /**PATH C:\xampp\htdocs\exam1\resources\views/welcome.blade.php ENDPATH**/ ?>