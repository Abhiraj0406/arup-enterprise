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
            --gold: #C9920A;
            --gold-dark: #a87a08;
            --cream: #fef7ed;
            --charcoal: #1e1e1e;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--cream);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .login-card {
            background: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(201,146,10,0.1);
            width: 100%;
            max-width: 400px;
            border: 1px solid rgba(201,146,10,0.2);
            text-align: center;
        }
        .login-logo img {
            max-width: 200px;
            margin-bottom: 20px;
        }
        .login-title {
            color: var(--charcoal);
            font-weight: 800;
            margin-bottom: 5px;
        }
        .login-subtitle {
            color: var(--gold);
            font-size: 0.9rem;
            margin-bottom: 30px;
            font-weight: 600;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(201,146,10,0.2);
            border-color: var(--gold);
        }
        .btn-login {
            background: var(--gold);
            color: #fff;
            border: none;
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background: var(--gold-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(201,146,10,0.3);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">
            <img src="../assets/images/logo.png" alt="Arup Enterprise" onerror="this.style.display='none'">
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
                <span class="input-group-text" style="background:#fff; border-right:none;"><i class="fas fa-envelope text-muted"></i></span>
                <input type="email" name="email" class="form-control" placeholder="Email Address" required style="border-left:none;">
            </div>
            <div class="input-group mb-4">
                <span class="input-group-text" style="background:#fff; border-right:none;"><i class="fas fa-lock text-muted"></i></span>
                <input type="password" name="password" class="form-control" placeholder="Password" required style="border-left:none;">
            </div>
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
    </div>
</body>
</html>
