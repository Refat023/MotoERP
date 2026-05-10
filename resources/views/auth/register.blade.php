@extends('auth.layout')

@section('title', 'Register - Super Shop POS')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <h1><i class="bi bi-shop"></i> Super Shop</h1>
        <p>Create Your Account</p>
    </div>

    <div class="auth-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <div>
                        <strong>Registration Error!</strong>
                        <ul class="mb-0 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">
                    <i class="bi bi-person"></i> Full Name
                </label>
                <input 
                    type="text" 
                    class="form-control @error('name') is-invalid @enderror" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}" 
                    placeholder="Enter your full name"
                    required
                    autofocus
                >
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

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
                        placeholder="At least 8 characters"
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

            <div class="form-group">
                <label for="password_confirmation" class="form-label">
                    <i class="bi bi-lock-check"></i> Confirm Password
                </label>
                <div class="input-group">
                    <input 
                        type="password" 
                        class="form-control @error('password_confirmation') is-invalid @enderror" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        placeholder="Re-enter your password"
                        required
                    >
                    <span class="password-toggle" onclick="togglePassword('password_confirmation')">
                        <i class="bi bi-eye"></i>
                    </span>
                </div>
                @error('password_confirmation')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-check">
                <input 
                    class="form-check-input" 
                    type="checkbox" 
                    id="agree" 
                    name="agree"
                    required
                >
                <label class="form-check-label" for="agree">
                    I agree to the terms and conditions
                </label>
            </div>

            <button type="submit" class="btn btn-auth">
                <i class="bi bi-person-plus"></i> Create Account
            </button>
        </form>
    </div>

    <div class="auth-footer">
        Already have an account? 
        <a href="{{ route('login') }}">
            Login here
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
