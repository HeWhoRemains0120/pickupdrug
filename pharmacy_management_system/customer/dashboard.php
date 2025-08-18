<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$database = new Database();
$db = $database->getConnection();

// Get medicines with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * RECORDS_PER_PAGE;

// Search functionality
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;

$whereClause = "WHERE m.is_active = 1";
$params = [];

if (!empty($search)) {
    $whereClause .= " AND (m.name LIKE :search OR m.description LIKE :search)";
    $params[':search'] = "%$search%";
}

if ($category > 0) {
    $whereClause .= " AND m.category_id = :category";
    $params[':category'] = $category;
}

try {
    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM medicines m $whereClause";
    $countStmt = $db->prepare($countQuery);
    foreach ($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }
    $countStmt->execute();
    $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalRecords / RECORDS_PER_PAGE);

    // Get medicines
    $query = "SELECT m.*, c.name as category_name 
              FROM medicines m 
              LEFT JOIN categories c ON m.category_id = c.id 
              $whereClause 
              ORDER BY m.created_at DESC 
              LIMIT :limit OFFSET :offset";
    
    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', RECORDS_PER_PAGE, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $medicines = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get categories for filter
    $categoryQuery = "SELECT * FROM categories WHERE is_active = 1 ORDER BY name";
    $categoryStmt = $db->prepare($categoryQuery);
    $categoryStmt->execute();
    $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $medicines = [];
    $categories = [];
    $totalPages = 1;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - <?php echo SITE_NAME; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <!-- Welcome Section -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-tachometer-alt"></i>
                    Welcome back, <?php echo $_SESSION['user_name']; ?>!
                </h2>
                <div>
                    <a href="cart.php" class="btn btn-primary">
                        <i class="fas fa-shopping-cart"></i> 
                        View Cart <span id="cartCount" class="badge">0</span>
                    </a>
                </div>
            </div>
            
            <!-- Search and Filter -->
            <form method="GET" class="form-row" style="margin-bottom: 2rem;">
                <div class="form-group" style="flex: 2;">
                    <input type="text" class="form-control" name="search" 
                           placeholder="Search medicines..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="form-group">
                    <select class="form-control" name="category">
                        <option value="0">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" 
                                    <?php echo ($category == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </form>
        </div>

        <!-- Medicines Grid -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-pills"></i>
                    Available Medicines (<?php echo $totalRecords; ?> items)
                </h3>
            </div>

            <?php if (empty($medicines)): ?>
                <div style="text-align: center; padding: 3rem;">
                    <i class="fas fa-search" style="font-size: 4rem; color: #ccc; margin-bottom: 1rem;"></i>
                    <h3>No medicines found</h3>
                    <p>Try adjusting your search criteria or browse all medicines.</p>
                    <a href="dashboard.php" class="btn btn-primary">
                        <i class="fas fa-pills"></i> View All Medicines
                    </a>
                </div>
            <?php else: ?>
                <div class="grid-3">
                    <?php foreach ($medicines as $medicine): ?>
                        <div class="medicine-card" data-medicine-id="<?php echo $medicine['id']; ?>">
                            <div class="medicine-image">
                                <?php if ($medicine['image']): ?>
                                    <img src="../uploads/medicines/<?php echo $medicine['image']; ?>" 
                                         alt="<?php echo htmlspecialchars($medicine['name']); ?>"
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <i class="fas fa-pills"></i>
                                <?php endif; ?>
                            </div>
                            <div class="medicine-info">
                                <h3 class="medicine-title"><?php echo htmlspecialchars($medicine['name']); ?></h3>
                                <p class="medicine-description"><?php echo htmlspecialchars($medicine['description']); ?></p>
                                
                                <div class="medicine-meta">
                                    <span class="medicine-price"><?php echo formatCurrency($medicine['price']); ?></span>
                                    <span class="stock-badge <?php echo getStockStatus($medicine['stock_quantity']); ?>">
                                        <?php 
                                        if ($medicine['stock_quantity'] == 0) {
                                            echo 'Out of Stock';
                                        } elseif ($medicine['stock_quantity'] <= $medicine['min_stock_level']) {
                                            echo 'Low Stock';
                                        } else {
                                            echo 'In Stock';
                                        }
                                        ?>
                                    </span>
                                </div>

                                <div style="margin-top: 1rem;">
                                    <div class="form-row" style="margin-bottom: 1rem;">
                                        <input type="number" class="form-control quantity-input" 
                                               min="1" max="<?php echo $medicine['stock_quantity']; ?>" 
                                               value="1" style="max-width: 80px;">
                                        <span style="align-self: center; margin-left: 10px; color: #666;">
                                            Available: <?php echo $medicine['stock_quantity']; ?>
                                        </span>
                                    </div>
                                    
                                    <?php if ($medicine['stock_quantity'] > 0): ?>
                                        <button class="btn btn-primary add-to-cart-btn" 
                                                style="width: 100%;" 
                                                onclick="addToCart(<?php echo $medicine['id']; ?>, this)">
                                            <i class="fas fa-cart-plus"></i> Add to Cart
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-secondary" disabled style="width: 100%;">
                                            <i class="fas fa-times"></i> Out of Stock
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div style="text-align: center; margin-top: 2rem;">
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category; ?>" 
                                   class="btn btn-secondary">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php if ($i == $page): ?>
                                    <span class="btn btn-primary"><?php echo $i; ?></span>
                                <?php else: ?>
                                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category; ?>" 
                                       class="btn btn-secondary"><?php echo $i; ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category; ?>" 
                                   class="btn btn-secondary">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions Modal -->
    <div class="modal" id="quickActionsModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Quick Actions</h3>
                <button class="close" onclick="closeModal('quickActionsModal')">&times;</button>
            </div>
            <div style="text-align: center;">
                <a href="orders.php" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem;">
                    <i class="fas fa-list"></i> View Order History
                </a>
                <a href="profile.php" class="btn btn-secondary" style="width: 100%; margin-bottom: 1rem;">
                    <i class="fas fa-user"></i> Update Profile
                </a>
                <a href="#" onclick="trackOrder()" class="btn btn-secondary" style="width: 100%;">
                    <i class="fas fa-truck"></i> Track Order
                </a>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/customer.js"></script>
</body>
</html>