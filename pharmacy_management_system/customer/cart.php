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

// Handle cart operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'update_quantity':
            updateCartQuantity($_POST['medicine_id'], $_POST['quantity']);
            break;
        case 'remove_item':
            removeFromCart($_POST['medicine_id']);
            break;
        case 'clear_cart':
            clearCart();
            break;
    }
    
    // Redirect to prevent form resubmission
    redirect('cart.php');
}

// Get cart items
$cartItems = getCartItems($db);
$cartTotal = calculateCartTotal($cartItems);

function getCartItems($db) {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return [];
    }
    
    $medicineIds = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($medicineIds) - 1) . '?';
    
    $query = "SELECT * FROM medicines WHERE id IN ($placeholders) AND is_active = 1";
    $stmt = $db->prepare($query);
    $stmt->execute($medicineIds);
    
    $medicines = [];
    while ($medicine = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $medicine['cart_quantity'] = $_SESSION['cart'][$medicine['id']];
        $medicine['subtotal'] = $medicine['price'] * $medicine['cart_quantity'];
        $medicines[] = $medicine;
    }
    
    return $medicines;
}

function calculateCartTotal($cartItems) {
    $total = 0;
    foreach ($cartItems as $item) {
        $total += $item['subtotal'];
    }
    return $total;
}

function updateCartQuantity($medicineId, $quantity) {
    $quantity = max(1, (int)$quantity);
    if (isset($_SESSION['cart'][$medicineId])) {
        $_SESSION['cart'][$medicineId] = $quantity;
    }
}

function removeFromCart($medicineId) {
    if (isset($_SESSION['cart'][$medicineId])) {
        unset($_SESSION['cart'][$medicineId]);
    }
}

function clearCart() {
    $_SESSION['cart'] = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - <?php echo SITE_NAME; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-shopping-cart"></i>
                    Shopping Cart (<?php echo count($cartItems); ?> items)
                </h2>
                <div>
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Continue Shopping
                    </a>
                    <?php if (!empty($cartItems)): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="clear_cart">
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Are you sure you want to clear your cart?')">
                                <i class="fas fa-trash"></i> Clear Cart
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (empty($cartItems)): ?>
                <div style="text-align: center; padding: 3rem;">
                    <i class="fas fa-shopping-cart" style="font-size: 4rem; color: #ccc; margin-bottom: 1rem;"></i>
                    <h3>Your cart is empty</h3>
                    <p>Add some medicines to your cart to proceed with checkout.</p>
                    <a href="dashboard.php" class="btn btn-primary">
                        <i class="fas fa-pills"></i> Browse Medicines
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Medicine</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item): ?>
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 1rem;">
                                            <div style="width: 60px; height: 60px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                <?php if ($item['image']): ?>
                                                    <img src="../uploads/medicines/<?php echo $item['image']; ?>" 
                                                         style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                                                <?php else: ?>
                                                    <i class="fas fa-pills" style="color: var(--primary-color);"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <h4 style="margin: 0;"><?php echo htmlspecialchars($item['name']); ?></h4>
                                                <p style="margin: 0; color: #666; font-size: 0.9rem;">
                                                    <?php echo htmlspecialchars($item['description']); ?>
                                                </p>
                                                <span class="stock-badge <?php echo getStockStatus($item['stock_quantity']); ?>">
                                                    Stock: <?php echo $item['stock_quantity']; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><strong><?php echo formatCurrency($item['price']); ?></strong></td>
                                    <td>
                                        <form method="POST" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                                            <input type="hidden" name="action" value="update_quantity">
                                            <input type="hidden" name="medicine_id" value="<?php echo $item['id']; ?>">
                                            <input type="number" name="quantity" 
                                                   value="<?php echo $item['cart_quantity']; ?>"
                                                   min="1" max="<?php echo $item['stock_quantity']; ?>"
                                                   class="form-control" style="width: 80px;"
                                                   onchange="this.form.submit()">
                                        </form>
                                    </td>
                                    <td><strong><?php echo formatCurrency($item['subtotal']); ?></strong></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="remove_item">
                                            <input type="hidden" name="medicine_id" value="<?php echo $item['id']; ?>">
                                            <button type="submit" class="btn btn-danger" 
                                                    onclick="return confirm('Remove this item from cart?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr style="background: #f8f9fa; font-weight: bold;">
                                <td colspan="3">Total</td>
                                <td><?php echo formatCurrency($cartTotal); ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Checkout Section -->
                <div style="text-align: right; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #eee;">
                    <h3>Cart Summary</h3>
                    <div style="display: inline-block; text-align: left; margin-bottom: 1rem;">
                        <div style="margin-bottom: 0.5rem;">
                            Subtotal: <strong><?php echo formatCurrency($cartTotal); ?></strong>
                        </div>
                        <div style="margin-bottom: 0.5rem;">
                            Shipping: <strong>Free</strong>
                        </div>
                        <div style="font-size: 1.2rem; border-top: 1px solid #eee; padding-top: 0.5rem;">
                            Total: <strong style="color: var(--primary-color);"><?php echo formatCurrency($cartTotal); ?></strong>
                        </div>
                    </div>
                    <br>
                    <a href="checkout.php" class="btn btn-primary" style="font-size: 1.1rem; padding: 15px 30px;">
                        <i class="fas fa-credit-card"></i> Proceed to Checkout
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/main.js"></script>
</body>
</html>