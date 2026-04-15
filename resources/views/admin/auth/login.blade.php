<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="shortcut icon" href="admin-assets/img/logo dmi.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <div class="container">
        <div class="login-card">
            <div class="login-image">
                <img src="{{ asset('admin-assets/img/mosque-bg.jpg') }}" alt="Background">
                <div class="side-label">LOGIN</div>
            </div>

            <div class="login-form">
                <div class="logo-container">
                    <img src="{{ asset('admin-assets/img/logo.png') }}" alt="Logo" class="main-logo">
                    <h2>LOGIN</h2>
                </div>

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <i class="fas fa-user-circle"></i>
                        <input type="email" name="email" placeholder="Email" required>
                    </div>

                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>

                    <div class="form-footer">
                        <a href="{{ route('password.request') }}" class="forgot-link">Forgot Password?</a>
                        <button type="submit" class="btn-login">LOGIN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>