<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Super Shop POS')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #007bff;
            --dark-color: #343a40;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .auth-container {
            width: 100%;
            max-width: 450px;
        }

        .auth-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .auth-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }

        .auth-header h1 {
            font-size: 32px;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .auth-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .auth-body {
            padding: 40px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: block;
            font-size: 14px;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-auth {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .auth-footer {
            padding: 20px 40px;
            text-align: center;
            background-color: #f8f9fa;
            font-size: 14px;
        }

        .auth-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .auth-footer a:hover {
            color: #764ba2;
        }

        .form-check {
            margin-top: 20px;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            border: 2px solid #e9ecef;
            border-radius: 4px;
        }

        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        .form-check-label {
            margin-left: 8px;
            font-size: 14px;
            color: #666;
        }

        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: 20px;
        }

        .alert-danger {
            background-color: #fff5f5;
            color: #c53030;
        }

        .alert-success {
            background-color: #f0fdf4;
            color: #166534;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }

        .input-group {
            position: relative;
        }

        @media (max-width: 576px) {
            .auth-body {
                padding: 25px;
            }

            .auth-header {
                padding: 30px 20px;
            }

            .auth-header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
