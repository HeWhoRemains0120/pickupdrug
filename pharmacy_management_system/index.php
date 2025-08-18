<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <!-- Hero Section -->
        <div class="card" style="text-align: center; background: linear-gradient(135deg, rgba(255,255,255,0.9), rgba(255,255,255,0.7));">
            <h1 style="font-size: 3rem; margin-bottom: 1rem; color: #333;">
                <i class="fas fa-pills" style="color: var(--primary-color);"></i>
                Welcome to MediStore
            </h1>
            <p style="font-size: 1.2rem; margin-bottom: 2rem; color: #666;">
                Your trusted online pharmacy management system. Order medicines online, track deliveries, and manage your health with ease.
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="customer/login.php" class="btn btn-primary" style="font-size: 1.1rem; padding: 15px 30px;">
                    <i class="fas fa-user"></i> Customer Portal
                </a>
                <a href="admin/login.php" class="btn btn-secondary" style="font-size: 1.1rem; padding: 15px 30px;">
                    <i class="fas fa-user-shield"></i> Admin Portal
                </a>
            </div>
        </div>

        <!-- Features Section -->
        <div class="grid-3">
            <div class="card">
                <div style="text-align: center;">
                    <i class="fas fa-shopping-cart" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                    <h3>Online Ordering</h3>
                    <p>Browse and order medicines online with ease. Our user-friendly interface makes it simple to find what you need.</p>
                </div>
            </div>

            <div class="card">
                <div style="text-align: center;">
                    <i class="fas fa-truck" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                    <h3>Order Tracking</h3>
                    <p>Track your orders in real-time with unique tracking IDs. Know exactly when your medicines will arrive.</p>
                </div>
            </div>

            <div class="card">
                <div style="text-align: center;">
                    <i class="fas fa-money-bill-wave" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                    <h3>Cash on Delivery</h3>
                    <p>Pay when you receive your order. We accept cash on delivery for your convenience and security.</p>
                </div>
            </div>

            <div class="card">
                <div style="text-align: center;">
                    <i class="fas fa-envelope" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                    <h3>Email Confirmations</h3>
                    <p>Receive instant email confirmations and invoices for all your orders. Stay updated every step of the way.</p>
                </div>
            </div>

            <div class="card">
                <div style="text-align: center;">
                    <i class="fas fa-chart-line" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                    <h3>Analytics Dashboard</h3>
                    <p>Comprehensive admin dashboard with sales analytics, inventory management, and customer insights.</p>
                </div>
            </div>

            <div class="card">
                <div style="text-align: center;">
                    <i class="fas fa-shield-alt" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                    <h3>Secure & Reliable</h3>
                    <p>Your data is protected with enterprise-level security. Role-based access control keeps everything safe.</p>
                </div>
            </div>
        </div>

        <!-- Statistics Section -->
        <div class="card">
            <h2 class="card-title">
                <i class="fas fa-chart-bar"></i>
                System Statistics
            </h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <h3>1,250+</h3>
                    <p>Registered Users</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-pills"></i>
                    <h3>500+</h3>
                    <p>Medicines Available</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>2,890+</h3>
                    <p>Orders Processed</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-star"></i>
                    <h3>4.8/5</h3>
                    <p>Customer Rating</p>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>