<?php
session_start();
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Arup Enterprise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold: #EF4444;
            --gold-dark: #DC2626;
            --cream: #FFF5F5;
            --charcoal: #111827;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: #FFF5F5;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .login-card {
            background: #ffffff;
            padding: 45px 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(239, 68, 68, 0.12);
            width: 100%;
            max-width: 420px;
            border: 1px solid rgba(239, 68, 68, 0.2);
            text-align: center;
        }
        .login-logo img {
            width: 80%;
            max-width: 280px;
            margin-bottom: 3px;
        }
        .login-title {
            color: var(--charcoal);
            font-weight: 800;
            margin-bottom: 5px;
        }
        .login-subtitle {
            color: #EF4444;
            font-size: 0.9rem;
            margin-bottom: 1px;
            font-weight: 600;
        }
        .input-group-text.left-icon {
            background: #fff;
            border: 1px solid #FECACA;
            border-right: none;
            border-radius: 8px 0 0 8px;
            color: #9CA3AF;
        }
        .input-group-text.right-icon {
            background: #fff;
            border: 1px solid #FECACA;
            border-left: none;
            border-radius: 0 8px 8px 0;
            color: #9CA3AF;
            cursor: pointer;
            transition: color 0.2s;
        }
        .input-group-text.right-icon:hover {
            color: #EF4444;
        }
        .form-control {
            padding: 12px 15px;
            border: 1px solid #FECACA;
            border-left: none;
            border-right: none; /* Removed right border to connect with right icon */
            background: #fff;
            color: var(--charcoal);
            border-radius: 0; /* Reset radius so right icon can be rounded */
        }
        /* Email field has no right icon, so we round it */
        input[type="email"].form-control {
            border-right: 1px solid #FECACA;
            border-radius: 0 8px 8px 0;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #EF4444;
        }
        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control {
            border-color: #EF4444;
            color: #EF4444;
        }
        /* Autofill background fix */
        input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 1000px white inset !important;
        }
        .btn-login {
            background: linear-gradient(135deg, #EF4444, #DC2626);
            color: #fff;
            border: none;
            width: 100%;
            padding: 14px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
            margin-top: 10px;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #DC2626, #B91C1C);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.35);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">
            <a href="../">
                <img src="images/logo.png" alt="Arup Enterprise" onerror="this.src='../assets/images/logo.png'">
            </a>
        </div>
        <h3 class="login-title">Welcome Back</h3>
        <p class="login-subtitle">Arup Enterprise Admin Panel</p>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger" role="alert" style="font-size: 0.85rem; padding: 10px;">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success" role="alert" style="font-size: 0.85rem; padding: 10px;">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <form action="do_login.php" method="POST">
            <div class="input-group mb-3">
                <span class="input-group-text left-icon"><i class="fas fa-envelope"></i></span>
                <input type="email" name="email" class="form-control" placeholder="Email Address" required>
            </div>
            <div class="input-group mb-4">
                <span class="input-group-text left-icon"><i class="fas fa-lock"></i></span>
                <input type="password" name="password" id="passwordField" class="form-control" placeholder="Password" required>
                <span class="input-group-text right-icon" id="togglePassword">
                    <i class="fas fa-eye" id="eyeIcon"></i>
                </span>
            </div>
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        <div style="margin-top: 25px; font-size: 0.82rem; color: #6b7280;">
            Design & Developed by <a href="https://aidigitalinnovation.com/" target="_blank" style="color: #EF4444; text-decoration: none; font-weight: 600;">AI Digital Innovation</a>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const passwordField = document.querySelector('#passwordField');
        const eyeIcon = document.querySelector('#eyeIcon');

        togglePassword.addEventListener('click', function (e) {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            
            if(type === 'password') {
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        });
    </script>
</body>
</html>
