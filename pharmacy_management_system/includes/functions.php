<?php
require_once('../config/database.php');

// Generate unique order number
function generateOrderNumber() {
    return ORDER_PREFIX . date('Ymd') . rand(1000, 9999);
}

// Generate unique tracking ID
function generateTrackingId() {
    return TRACKING_PREFIX . date('Ymd') . rand(10000, 99999);
}

// Sanitize input data
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

// Redirect function
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Send JSON response
function sendJsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

// Format currency
function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

// Calculate stock status
function getStockStatus($quantity, $minLevel = 10) {
    if ($quantity == 0) return 'out-of-stock';
    if ($quantity <= $minLevel) return 'low-stock';
    return 'in-stock';
}

// Send email function
function sendEmail($to, $subject, $body, $isHTML = true) {
    // Implementation depends on your email service
    // This is a basic example
    $headers = "From: " . ADMIN_EMAIL . "\r\n";
    $headers .= "Reply-To: " . ADMIN_EMAIL . "\r\n";
    if ($isHTML) {
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    }
    
    return mail($to, $subject, $body, $headers);
}

// Upload file function
function uploadFile($file, $uploadPath = UPLOAD_PATH) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload error'];
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File too large'];
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    $filename = uniqid() . '.' . $extension;
    $destination = $uploadPath . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $filename];
    }
    
    return ['success' => false, 'message' => 'Upload failed'];
}
?>