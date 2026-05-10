@extends('auth.layout')

@section('title', 'Login - Super Shop POS')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <h1><i class="bi bi-shop"></i> Super Shop</h1>
        <p>Point of Sale System</p>
    </div>

    <div class="auth-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <div>
                        <strong>Login Failed!</strong>
                        <ul class="mb-0 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">
                    <i class="bi bi-envelope"></i> Email Address
                </label>
                <input 
                    type="email" 
                    class="form-control @error('email') is-invalid @enderror" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    placeholder="Enter your email"
                    required
                    autofocus
                >
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">
                    <i class="bi bi-lock"></i> Password
                </label>
                <div class="input-group">
                    <input 
                        type="password" 
                        class="form-control @error('password') is-invalid @enderror" 
                        id="password" 
                        name="password" 
                        placeholder="Enter your password"
                        required
                    >
                    <span class="password-toggle" onclick="togglePassword('password')">
                        <i class="bi bi-eye"></i>
                    </span>
                </div>
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-check">
                <input 
                    class="form-check-input" 
                    type="checkbox" 
                    id="remember" 
                    name="remember"
                >
                <label class="form-check-label" for="remember">
                    Remember me
                </label>
            </div>

            <button type="submit" class="btn btn-auth">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </button>
        </form>
    </div>

    <div class="auth-footer">
        Don't have an account? 
        <a href="{{ route('register') }}">
            Create one now
        </a>
    </div>
</div>

<script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = event.target.closest('.password-toggle').querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
</script>
@endsection
