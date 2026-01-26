<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExamInEase - Modern Exam Administration Platform</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/Gemini_Generated_Image_epcsx0epcsx0epcs-removebg-preview.ico') }}">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --distinguishing-color: #f54842;
            --primary-gradient: linear-gradient(135deg, #5f9eb7 0%, #4a7a8fff 100%);
            --secondary-gradient: linear-gradient(135deg, #7bb3c9 0%, #5f9eb7 100%);
            --accent-gradient: linear-gradient(135deg, #f54842 0%, #ff9f51 100%);
            --dark-bg: #1a1a2e;
            --light-text: #ffffff;
            --surface-light: #f8f9fa;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background: linear-gradient(135deg, #7bb3c9 0%, #5f9eb7 100%);
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
        }

        /* Modern Header */
        .modern-header {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.12);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(95, 158, 183, 0.1);
        }

        .modern-header .navbar-brand {
            font-size: 1.5rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: opacity 0.3s ease;
        }

        .modern-header .navbar-brand:hover {
            opacity: 0.8;
        }

        .btn-login {
            background: var(--accent-gradient);
            background-size: 200% 200%;
            background-position: left center;
            color: white;
            border: none;
            padding: 0.6rem 1.8rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            box-shadow: 0 4px 15px rgba(245, 72, 66, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(245, 72, 66, 0.4);
            background-position: right center;
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        .btn-dashboard {
            background: var(--secondary-gradient);
            color: white;
            border: none;
            padding: 0.6rem 1.8rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            box-shadow: 0 4px 15px rgba(95, 158, 183, 0.3);
        }

        .btn-dashboard:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(95, 158, 183, 0.4);
            color: white;
        }

        /* Hero Section */
        .hero-section {
            padding: 6rem 1rem;
            text-align: center;
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;                                                                               
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(30px);
            }
        }

        .hero-card {
            background: rgba(255, 255, 255, 0.97);
            border-radius: 24px;
            padding: 4rem 2.5rem;
            max-width: 800px;
            margin: 0 auto;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            position: relative;
            z-index: 1;
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-card h1 {
            font-size: 3.5rem;
            font-weight: 900;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .hero-card p {
            font-size: 1.15rem;
            color: #555;
            line-height: 1.8;
            margin-bottom: 0;
            letter-spacing: 0.3px;
        }

        .cta-buttons {
            margin-top: 2.5rem;
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            position: relative;
            z-index: 2;
        }

        /* Metrics/Stats Section */
        .metrics-section {
            padding: 3rem 1rem;
            background: rgba(255, 255, 255, 0.15);
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .metric-card {
            text-align: center;
            color: white;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .metric-number {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .metric-label {
            font-size: 1rem;
            font-weight: 500;
            opacity: 0.95;
        }

        /* Features Section */
        .features-section {
            padding: 6rem 1rem;
            background: var(--surface-light);
            position: relative;
        }

        .section-title {
            text-align: center;
            font-size: 2.8rem;
            font-weight: 900;
            margin-bottom: 4rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: var(--accent-gradient);
            margin: 1.5rem auto 0;
            border-radius: 2px;
        }

        .feature-card {
            background: white;
            border-radius: 16px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            transition: all 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            height: 100%;
            border: 1px solid rgba(95, 158, 183, 0.1);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--accent-gradient);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 10px 25px rgba(95, 158, 183, 0.3);
        }

        .feature-card h4 {
            font-weight: 800;
            margin-bottom: 1rem;
            color: #333;
            font-size: 1.25rem;
        }

        .feature-card p {
            color: #666;
            line-height: 1.7;
            font-size: 0.95rem;
        }

        /* Developers Section */
        .developers-section {
            padding: 6rem 1rem;
            background: rgba(255, 255, 255, 0.98);
            position: relative;
        }

        .developer-card {
            background: white;
            border-radius: 16px;
            padding: 2.5rem;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            transition: all 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            height: 100%;
            border: 1px solid rgba(95, 158, 183, 0.1);
            position: relative;
        }

        .developer-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.15);
        }

        .developer-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }

        .developer-card:hover .developer-avatar {
            transform: scale(1.08) rotateZ(5deg);
        }

        .lead-dev .developer-avatar {
            background: var(--primary-gradient);
        }

        .assistant-dev .developer-avatar {
            background: var(--secondary-gradient);
        }

        .developer-card h5 {
            font-weight: 800;
            color: #333;
            margin-bottom: 0.5rem;
            font-size: 1.15rem;
        }

        .developer-card .role {
            color: #5f9eb7;
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .developer-card .course {
            color: #999;
            font-size: 0.9rem;
        }

        /* Footer */
        .modern-footer {
            background: linear-gradient(135deg, #2c3e50 0%, #1a1a2e 100%);
            color: white;
            padding: 3rem 1rem;
            text-align: center;
            margin-top: auto;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modern-footer p {
            margin: 0;
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-card {
                padding: 2.5rem 1.5rem;
            }

            .hero-card h1 {
                font-size: 2.2rem;
            }

            .hero-card p {
                font-size: 1rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .cta-buttons {
                flex-direction: column;
            }

            .btn-login, .btn-dashboard {
                width: 100%;
            }

            .metric-number {
                font-size: 2rem;
            }

            .developer-avatar {
                width: 100px;
                height: 100px;
                font-size: 2.5rem;
            }
        }

        @media (max-width: 576px) {
            .hero-section {
                padding: 4rem 1rem;
            }

            .hero-card h1 {
                font-size: 1.8rem;
            }

            .features-section, .developers-section {
                padding: 4rem 1rem;
            }
        }

        /* Utility animations */
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }

        .pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>
<body class="d-flex flex-column">

    <!-- Modern Header -->
    <nav class="navbar navbar-expand-lg modern-header">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="{{ asset('images/Asset 4.png') }}" alt="ExamInEase logo" style="height:40px; margin-right:10px; object-fit:contain;">
                <span>ExamInEase</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    @if (Route::has('login'))
                        @auth
                            <li class="nav-item">
                                <a class="btn btn-dashboard" href="{{ url('/dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="btn btn-login" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt"></i> Login
                                </a>
                            </li>
                        @endauth
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-card">
            <div style="display: flex; align-items: center; justify-content: center; gap: 1rem; margin-bottom: 1.5rem;">
                <img src="{{ asset('images/Asset 4.png') }}" alt="ExamInEase logo" style="height: 80px; object-fit: contain;">
                <h1 style="margin: 0;">EXAMINEASE</h1>
            </div>
            <p>
                A modern, all-in-one exam administration platform designed for the College of Information and Computing Sciences. Streamline your exam management with powerful tools and intuitive design.
            </p>
            
            @guest
            <div class="cta-buttons">
                <a href="{{ route('login') }}" id="loginBtn" class="btn btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i> Login to Get Started
                </a>
                <a href="#features" class="btn" style="background: white; color: #5f9eb7; border: 2px solid #5f9eb7; font-weight: 600; padding: 0.6rem 1.8rem; border-radius: 50px; transition: all 0.3s ease;">
                    <i class="fas fa-arrow-down me-2"></i> Explore Features
                </a>
            </div>
            @endguest

            @auth
            <div class="cta-buttons">
                <a href="{{ url('/dashboard') }}" class="btn btn-dashboard">
                    <i class="fas fa-tachometer-alt me-2"></i> Go to Dashboard
                </a>
            </div>
            @endauth
        </div>
    </section>

    <script>
        // Dynamic gradient following mouse movement
        const loginBtn = document.getElementById('loginBtn');
        if (loginBtn) {
            loginBtn.addEventListener('mousemove', function(e) {
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const xPercent = (x / rect.width) * 100;
                const yPercent = (y / rect.height) * 100;
                
                this.style.backgroundPosition = `${xPercent}% ${yPercent}%`;
            });
            
            loginBtn.addEventListener('mouseleave', function() {
                this.style.backgroundPosition = 'left center';
            });
        }

        // Smooth scroll for internal links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href !== '#' && document.querySelector(href)) {
                    e.preventDefault();
                    document.querySelector(href).scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="container">
            <h2 class="section-title">Key Features</h2>
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h4>Comprehensive Exam Management</h4>
                        <p>Create, organize, and manage exams effortlessly with our comprehensive administration tools. Schedule exams, set time limits, monitor progress in real-time, and access detailed student performance metrics.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>Advanced Analytics & Reporting</h4>
                        <p>Gain powerful insights with detailed analytics and automated reporting. Track student performance, identify trends, generate reports, and make data-driven decisions for improving educational outcomes.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h4>Secure & Reliable</h4>
                        <p>Enterprise-grade security with encrypted data storage, secure authentication, and role-based access control. Your exam data is protected with industry-standard security protocols and regular backups.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h4>Mobile-Friendly Interface</h4>
                        <p>Access ExamInEase from any device. Our fully responsive design ensures a seamless experience on desktops, tablets, and smartphones. Take exams and manage assessments on the go.</p>
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
