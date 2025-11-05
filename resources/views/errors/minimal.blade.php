<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title') - {{ config('app.name', 'ExamInEase') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('images/Gemini_Generated_Image_epcsx0epcsx0epcs-removebg-preview.ico') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
                background-color: #ffffff;
                margin: 0;
                padding: 0;
            }
            .error-container {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                min-height: calc(100vh - 80px);
                padding: 2rem;
                text-align: center;
            }
            .error-code {
                font-size: 6rem;
                font-weight: 700;
                color: #5f8a9a;
                margin-bottom: 1rem;
                line-height: 1;
            }
            .error-message {
                font-size: 1.5rem;
                font-weight: 600;
                color: #5f8a9a;
                margin-bottom: 0.5rem;
            }
            .error-description {
                font-size: 1rem;
                color: #6b7280;
                margin-bottom: 2rem;
            }
            .back-button {
                background-color: #5f8a9a;
                color: white;
                padding: 0.75rem 2rem;
                border-radius: 8px;
                text-decoration: none;
                font-weight: 500;
                transition: background-color 0.2s;
            }
            .back-button:hover {
                background-color: #4a6d7a;
                color: white;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <!-- Include appropriate navigation based on user role -->
        @auth
            @php
                $user = auth()->user();
                $role = $user->role ?? null;
            @endphp
            
            @if($role === 'admin')
                @include('layouts.Admin.navigation')
            @elseif($role === 'program chair')
                @include('layouts.ProgramChair.navigation')
            @elseif($role === 'instructor')
                @include('layouts.Instructor.navigation')
            @else
                @include('layouts.navigation')
            @endif
        @endauth

        <div class="error-container">
            <div class="error-code">
                @yield('code')
            </div>
            <div class="error-message">
                @yield('message')
            </div>
            <div class="error-description">
                This page does not exist on this server.
            </div>
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : '/' }}" class="back-button">
                <i class="bi bi-arrow-left me-2"></i>Go Back
            </a>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
