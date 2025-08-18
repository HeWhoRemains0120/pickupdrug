<?php
// General Configuration
define('SITE_NAME', 'MediStore - Pharmacy Management System');
define('SITE_URL', 'http://localhost/pharmacy_management_system');
define('ADMIN_EMAIL', 'admin@medistore.com');

// Email Configuration (for order confirmations)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');

// Security Settings
define('ENCRYPTION_KEY', 'your-secret-key-here');
define('SESSION_TIMEOUT', 3600); // 1 hour

// File Upload Settings
define('MAX_FILE_SIZE', 5242880); // 5MB
define('UPLOAD_PATH', 'uploads/medicines/');
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Pagination
define('RECORDS_PER_PAGE', 12);

// Order Settings
define('ORDER_PREFIX', 'ORD');
define('TRACKING_PREFIX', 'TRK');

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>