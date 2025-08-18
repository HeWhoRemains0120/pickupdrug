<?php
require_once '../config/config.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (isAdminLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo SITE_NAME; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div style="max-width: 400px; margin: 5rem auto;">
            <div class="card">
                <div style="text-align: center; margin-bottom: 2rem;">
                    <i class="fas fa-user-shield" style="font-size: 4rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                    <h2>Admin Portal</h2>
                    <p>Access the pharmacy management system</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form action="../api/auth.php" method="POST">
                    <input type="hidden" name="action" value="admin_login">
                    
                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i> Admin Email
                        </label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="admin@medistore.com" required 
                               placeholder="Enter admin email">
                    </div>

                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <input type="password" class="form-control" id="password" name="password" 
                               value="admin123" required 
                               placeholder="Enter admin password">
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-sign-in-alt"></i> Login to Admin Panel
                    </button>
                </form>

                <div style="text-align: center; margin-top: 1.5rem;">
                    <a href="../index.php" style="color: var(--primary-color);">
                        <i class="fas fa-arrow-left"></i> Back to Main Site
                    </a>
                </div>

                <div style="background: #f8f9fa; padding: 1rem; margin-top: 2rem; border-radius: 8px; font-size: 0.9rem;">
                    <strong>Demo Credentials:</strong><br>
                    Email: admin@medistore.com<br>
                    Password: admin123
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
</body>
</html>