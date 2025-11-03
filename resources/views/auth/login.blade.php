<x-guest-layout>
    <div class="login-header">
        <div class="login-icon">
            <i class="bi bi-clipboard-check"></i>
        </div>
        <h1>Welcome Back</h1>
        <p>Sign in to ExamInEase</p>
    </div>

    <div class="login-body">
        <!-- Session Status -->
        @if (session('status'))
            <div class="status-message">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="form-group">
                <label for="email" class="form-label">
                    <i class="bi bi-envelope me-1"></i> Email Address
                </label>
                <input id="email" 
                       class="form-input" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus 
                       autocomplete="username"
                       placeholder="Enter your email">
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password" class="form-label">
                    <i class="bi bi-lock me-1"></i> Password
                </label>
                <input id="password" 
                       class="form-input"
                       type="password"
                       name="password"
                       required 
                       autocomplete="current-password"
                       placeholder="Enter your password">
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="form-group">
                <div class="remember-checkbox">
                    <input id="remember_me" 
                           type="checkbox" 
                           name="remember">
                    <label for="remember_me">
                        Remember me
                    </label>
                </div>
            </div>

            <div class="form-footer">
                @if (Route::has('password.request'))
                    <a class="forgot-password-link" href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif

                <button type="submit" class="btn-primary">
                    <i class="bi bi-box-arrow-in-right me-2"></i>
                    Sign In
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
