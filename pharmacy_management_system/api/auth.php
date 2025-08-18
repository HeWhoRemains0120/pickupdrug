<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['action'])) {
    sendJsonResponse(['error' => 'Invalid request method'], 405);
}

$database = new Database();
$db = $database->getConnection();

// Handle logout actions
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'logout') {
        session_destroy();
        redirect('../customer/login.php');
    } elseif ($_GET['action'] === 'admin_logout') {
        session_destroy();
        redirect('../admin/login.php');
    }
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'customer_login':
        handleCustomerLogin($db);
        break;
    
    case 'customer_register':
        handleCustomerRegister($db);
        break;
    
    case 'admin_login':
        handleAdminLogin($db);
        break;
    
    default:
        $_SESSION['error'] = 'Invalid action';
        redirect('../index.php');
}

function handleCustomerLogin($db) {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Email and password are required';
        redirect('../customer/login.php');
    }
    
    try {
        $query = "SELECT id, name, email, password, is_active FROM users WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user['is_active']) {
                $_SESSION['error'] = 'Your account has been deactivated. Please contact support.';
                redirect('../customer/login.php');
            }
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                
                // Update last login
                $updateQuery = "UPDATE users SET updated_at = CURRENT_TIMESTAMP WHERE id = :id";
                $updateStmt = $db->prepare($updateQuery);
                $updateStmt->bindParam(':id', $user['id']);
                $updateStmt->execute();
                
                redirect('../customer/dashboard.php');
            } else {
                $_SESSION['error'] = 'Invalid email or password';
                redirect('../customer/login.php');
            }
        } else {
            $_SESSION['error'] = 'Invalid email or password';
            redirect('../customer/login.php');
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Login failed. Please try again.';
        redirect('../customer/login.php');
    }
}

function handleCustomerRegister($db) {
    $firstName = sanitizeInput($_POST['first_name']);
    $lastName = sanitizeInput($_POST['last_name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $address = sanitizeInput($_POST['address']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($address) || empty($password)) {
        $_SESSION['error'] = 'All fields are required';
        redirect('../customer/register.php');
    }
    
    if ($password !== $confirmPassword) {
        $_SESSION['error'] = 'Passwords do not match';
        redirect('../customer/register.php');
    }
    
    if (strlen($password) < 6) {
        $_SESSION['error'] = 'Password must be at least 6 characters long';
        redirect('../customer/register.php');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Invalid email format';
        redirect('../customer/register.php');
    }
    
    try {
        // Check if email already exists
        $checkQuery = "SELECT id FROM users WHERE email = :email";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(':email', $email);
        $checkStmt->execute();
        
        if ($checkStmt->rowCount() > 0) {
            $_SESSION['error'] = 'Email address is already registered';
            redirect('../customer/register.php');
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $fullName = $firstName . ' ' . $lastName;
        
        // Insert new user
        $insertQuery = "INSERT INTO users (name, email, phone, address, password) VALUES (:name, :email, :phone, :address, :password)";
        $insertStmt = $db->prepare($insertQuery);
        $insertStmt->bindParam(':name', $fullName);
        $insertStmt->bindParam(':email', $email);
        $insertStmt->bindParam(':phone', $phone);
        $insertStmt->bindParam(':address', $address);
        $insertStmt->bindParam(':password', $hashedPassword);
        
        if ($insertStmt->execute()) {
            $_SESSION['success'] = 'Registration successful! Please login with your credentials.';
            redirect('../customer/login.php');
        } else {
            $_SESSION['error'] = 'Registration failed. Please try again.';
            redirect('../customer/register.php');
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Registration failed. Email might already be registered.';
        redirect('../customer/register.php');
    }
}

function handleAdminLogin($db) {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Email and password are required';
        redirect('../admin/login.php');
    }
    
    try {
        $query = "SELECT id, name, email, password, role, is_active FROM admins WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$admin['is_active']) {
                $_SESSION['error'] = 'Your admin account has been deactivated.';
                redirect('../admin/login.php');
            }
            
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['name'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_role'] = $admin['role'];
                
                redirect('../admin/dashboard.php');
            } else {
                $_SESSION['error'] = 'Invalid email or password';
                redirect('../admin/login.php');
            }
        } else {
            $_SESSION['error'] = 'Invalid email or password';
            redirect('../admin/login.php');
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Login failed. Please try again.';
        redirect('../admin/login.php');
    }
}
?>