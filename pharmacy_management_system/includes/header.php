<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<header class="header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <i class="fas fa-pills"></i>
                <h1><a href="<?php echo SITE_URL; ?>" style="text-decoration: none; color: inherit;">MediStore</a></h1>
            </div>
            <nav class="nav-menu">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo SITE_URL; ?>/customer/dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="<?php echo SITE_URL; ?>/customer/cart.php" class="btn btn-secondary">
                        <i class="fas fa-shopping-cart"></i> Cart
                    </a>
                    <a href="<?php echo SITE_URL; ?>/customer/orders.php" class="btn btn-secondary">
                        <i class="fas fa-list"></i> Orders
                    </a>
                    <a href="<?php echo SITE_URL; ?>/api/auth.php?action=logout" class="btn btn-primary">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                <?php elseif (isset($_SESSION['admin_id'])): ?>
                    <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-tachometer-alt"></i> Admin Dashboard
                    </a>
                    <a href="<?php echo SITE_URL; ?>/admin/medicines.php" class="btn btn-secondary">
                        <i class="fas fa-pills"></i> Medicines
                    </a>
                    <a href="<?php echo SITE_URL; ?>/admin/orders.php" class="btn btn-secondary">
                        <i class="fas fa-shopping-cart"></i> Orders
                    </a>
                    <a href="<?php echo SITE_URL; ?>/api/auth.php?action=admin_logout" class="btn btn-primary">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>/customer/login.php" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    <a href="<?php echo SITE_URL; ?>/customer/register.php" class="btn btn-secondary">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                    <a href="<?php echo SITE_URL; ?>/admin/login.php" class="btn btn-secondary">
                        <i class="fas fa-user-shield"></i> Admin
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</header>