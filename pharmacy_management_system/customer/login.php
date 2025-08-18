<?php
require_once '../config/config.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

$success = '';
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login - <?php echo SITE_NAME; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div class="grid-2">
            <div class="card">
                <h2 class="card-title">
                    <i class="fas fa-sign-in-alt"></i>
                    Customer Login
                </h2>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <form action="../api/auth.php" method="POST" id="loginForm">
                    <input type="hidden" name="action" value="customer_login">
                    
                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <input type="email" class="form-control" id="email" name="email" required 
                               placeholder="Enter your email address">
                    </div>

                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <input type="password" class="form-control" id="password" name="password" required 
                               placeholder="Enter your password">
                    </div>

                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="remember_me"> Remember me
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>

                <div style="text-align: center; margin-top: 1.5rem;">
                    <p>Don't have an account? <a href="register.php" style="color: var(--primary-color);">Register here</a></p>
                    <p><a href="#" style="color: var(--primary-color);">Forgot your password?</a></p>
                </div>
            </div>

            <div class="card">
                <h2 class="card-title">
                    <i class="fas fa-info-circle"></i>
                    Why Choose MediStore?
                </h2>
                
                <div style="margin-bottom: 1.5rem;">
                    <h4><i class="fas fa-check" style="color: var(--success-color);"></i> Verified Medicines</h4>
                    <p>All medicines are sourced from licensed manufacturers and are quality assured.</p>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <h4><i class="fas fa-shipping-fast" style="color: var(--success-color);"></i> Fast Delivery</h4>
                    <p>Quick and reliable delivery to your doorstep with real-time tracking.</p>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <h4><i class="fas fa-user-md" style="color: var(--success-color);"></i> Expert Support</h4>
                    <p>Get professional advice from our qualified pharmacists.</p>
                </div>

                <div>
                    <h4><i class="fas fa-shield-alt" style="color: var(--success-color);"></i> Secure & Safe</h4>
                    <p>Your personal and payment information is protected with enterprise-level security.</p>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/main.js"></script>
</body>
</html>