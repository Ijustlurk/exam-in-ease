<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ExamInEase') }} - Login</title>

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('images/Gemini_Generated_Image_epcsx0epcsx0epcs-removebg-preview.ico') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap Icons -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                font-family: 'Figtree', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background: linear-gradient(135deg, #6b9aac 0%, #7ca5b8 50%, #8fb5c4 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1rem;
            }
            
            .login-container {
                background: white;
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
                overflow: hidden;
                max-width: 450px;
                width: 100%;
                animation: slideUp 0.5s ease-out;
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
            
            .login-header {
                background: linear-gradient(135deg, #6b9aac 0%, #7ca5b8 100%);
                padding: 2.5rem 2rem;
                text-align: center;
                color: white;
            }
            
            .login-header h1 {
                font-size: 1.75rem;
                font-weight: 700;
                margin: 0 0 0.5rem 0;
                letter-spacing: -0.5px;
            }
            
            .login-header p {
                margin: 0;
                opacity: 0.95;
                font-size: 0.95rem;
            }
            
            .login-icon {
                width: 70px;
                height: 70px;
                background: rgba(255, 255, 255, 0.2);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 1.5rem;
                backdrop-filter: blur(10px);
            }
            
            .login-icon i {
                font-size: 2rem;
                color: white;
            }
            
            .login-body {
                padding: 2rem;
            }
            
            .form-group {
                margin-bottom: 1.5rem;
            }
            
            .form-label {
                display: block;
                font-weight: 600;
                color: #374151;
                margin-bottom: 0.5rem;
                font-size: 0.9rem;
            }
            
            .form-input {
                width: 100%;
                padding: 0.75rem 1rem;
                border: 2px solid #e5e7eb;
                border-radius: 10px;
                font-size: 0.95rem;
                transition: all 0.3s ease;
                background: #f9fafb;
            }
            
            .form-input:focus {
                outline: none;
                border-color: #7ca5b8;
                background: white;
                box-shadow: 0 0 0 4px rgba(124, 165, 184, 0.1);
            }
            
            .form-input:hover {
                border-color: #d1d5db;
            }
            
            .error-message {
                color: #ef4444;
                font-size: 0.875rem;
                margin-top: 0.5rem;
                display: block;
            }
            
            .remember-checkbox {
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }
            
            .remember-checkbox input[type="checkbox"] {
                width: 18px;
                height: 18px;
                border-radius: 4px;
                border: 2px solid #d1d5db;
                cursor: pointer;
                accent-color: #7ca5b8;
            }
            
            .remember-checkbox label {
                color: #6b7280;
                font-size: 0.9rem;
                cursor: pointer;
                user-select: none;
            }
            
            .btn-primary {
                width: 100%;
                background: linear-gradient(135deg, #6b9aac 0%, #7ca5b8 100%);
                color: white;
                padding: 0.875rem 1.5rem;
                border: none;
                border-radius: 10px;
                font-weight: 600;
                font-size: 1rem;
                cursor: pointer;
                transition: all 0.3s ease;
                box-shadow: 0 4px 12px rgba(124, 165, 184, 0.3);
            }
            
            .btn-primary:hover {
                background: linear-gradient(135deg, #5a8999 0%, #6b94a7 100%);
                box-shadow: 0 6px 16px rgba(124, 165, 184, 0.4);
                transform: translateY(-2px);
            }
            
            .btn-primary:active {
                transform: translateY(0);
                box-shadow: 0 2px 8px rgba(124, 165, 184, 0.3);
            }
            
            .forgot-password-link {
                color: #7ca5b8;
                text-decoration: none;
                font-size: 0.9rem;
                font-weight: 500;
                transition: color 0.2s;
            }
            
            .forgot-password-link:hover {
                color: #6b9aac;
                text-decoration: underline;
            }
            
            .form-footer {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-top: 1.5rem;
                gap: 1rem;
            }
            
            .status-message {
                background: #d1fae5;
                color: #065f46;
                padding: 0.75rem 1rem;
                border-radius: 8px;
                font-size: 0.9rem;
                margin-bottom: 1.5rem;
                border-left: 4px solid #10b981;
            }
            
            @media (max-width: 480px) {
                .login-header {
                    padding: 2rem 1.5rem;
                }
                
                .login-header h1 {
                    font-size: 1.5rem;
                }
                
                .login-body {
                    padding: 1.5rem;
                }
                
                .form-footer {
                    flex-direction: column;
                    align-items: stretch;
                }
                
                .forgot-password-link {
                    text-align: center;
                }
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            {{ $slot }}
        </div>
    </body>
</html>
