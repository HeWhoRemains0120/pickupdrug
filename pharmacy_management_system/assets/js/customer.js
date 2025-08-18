// Customer-specific JavaScript functions

// Initialize customer dashboard
document.addEventListener('DOMContentLoaded', function() {
    initializeCustomerFeatures();
    loadUserPreferences();
    checkCartSync();
});

function initializeCustomerFeatures() {
    // Quick actions
    const quickActionsBtn = document.getElementById('quickActionsBtn');
    if (quickActionsBtn) {
        quickActionsBtn.addEventListener('click', () => openModal('quickActionsModal'));
    }

    // Medicine search with autocomplete
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(handleMedicineSearch, 300));
    }

    // Wishlist functionality
    initializeWishlist();
    
    // Order tracking
    initializeOrderTracking();
}

// Medicine Search with Autocomplete
function handleMedicineSearch(e) {
    const query = e.target.value.trim();
    if (query.length < 2) {
        hideSearchSuggestions();
        return;
    }

    // Simulate API call for autocomplete
    fetchMedicineSuggestions(query).then(suggestions => {
        showSearchSuggestions(suggestions, e.target);
    });
}

function fetchMedicineSuggestions(query) {
    // In a real application, this would be an API call
    return new Promise(resolve => {
        setTimeout(() => {
            const mockSuggestions = [
                { id: 1, name: 'Paracetamol 500mg', price: 5.99 },
                { id: 2, name: 'Amoxicillin 250mg', price: 12.50 },
                { id: 3, name: 'Aspirin 100mg', price: 4.25 }
            ].filter(med => med.name.toLowerCase().includes(query.toLowerCase()));
            resolve(mockSuggestions);
        }, 200);
    });
}

function showSearchSuggestions(suggestions, inputElement) {
    hideSearchSuggestions();

    if (suggestions.length === 0) return;

    const suggestionsList = document.createElement('div');
    suggestionsList.className = 'search-suggestions';
    suggestionsList.style.cssText = `
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-top: none;
        border-radius: 0 0 8px 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        z-index: 1000;
        max-height: 300px;
        overflow-y: auto;
    `;

    suggestions.forEach(suggestion => {
        const item = document.createElement('div');
        item.className = 'suggestion-item';
        item.style.cssText = `
            padding: 12px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        `;
        item.innerHTML = `
            <span>${suggestion.name}</span>
            <span style="color: var(--primary-color); font-weight: bold;">
                ${formatCurrency(suggestion.price)}
            </span>
        `;
        
        item.addEventListener('click', () => {
            inputElement.value = suggestion.name;
            hideSearchSuggestions();
            // Navigate to medicine detail or add to cart
            addToCartFromSuggestion(suggestion.id);
        });

        item.addEventListener('mouseenter', () => {
            item.style.backgroundColor = '#f8f9fa';
        });

        item.addEventListener('mouseleave', () => {
            item.style.backgroundColor = 'white';
        });

        suggestionsList.appendChild(item);
    });

    const container = inputElement.closest('.form-group') || inputElement.parentElement;
    container.style.position = 'relative';
    container.appendChild(suggestionsList);
}

function hideSearchSuggestions() {
    const existingSuggestions = document.querySelector('.search-suggestions');
    if (existingSuggestions) {
        existingSuggestions.remove();
    }
}

// Click outside to hide suggestions
document.addEventListener('click', function(e) {
    if (!e.target.closest('.form-group')) {
        hideSearchSuggestions();
    }
});

// Wishlist Functionality
function initializeWishlist() {
    const wishlistButtons = document.querySelectorAll('.wishlist-btn');
    wishlistButtons.forEach(btn => {
        btn.addEventListener('click', toggleWishlist);
    });

    updateWishlistUI();
}

function toggleWishlist(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const button = e.target.closest('.wishlist-btn');
    const medicineId = button.dataset.medicineId;
    const medicineName = button.dataset.medicineName;
    
    let wishlist = loadFromStorage('wishlist', []);
    
    if (wishlist.includes(medicineId)) {
        wishlist = wishlist.filter(id => id !== medicineId);
        showNotification(`${medicineName} removed from wishlist`, 'info');
    } else {
        wishlist.push(medicineId);
        showNotification(`${medicineName} added to wishlist`, 'success');
    }
    
    saveToStorage('wishlist', wishlist);
    updateWishlistUI();
}

function updateWishlistUI() {
    const wishlist = loadFromStorage('wishlist', []);
    const wishlistButtons = document.querySelectorAll('.wishlist-btn');
    
    wishlistButtons.forEach(btn => {
        const medicineId = btn.dataset.medicineId;
        const icon = btn.querySelector('i');
        
        if (wishlist.includes(medicineId)) {
            icon.className = 'fas fa-heart';
            btn.style.color = 'var(--danger-color)';
        } else {
            icon.className = 'far fa-heart';
            btn.style.color = '#666';
        }
    });
}

// Order Tracking
function initializeOrderTracking() {
    const trackingForm = document.getElementById('trackingForm');
    if (trackingForm) {
        trackingForm.addEventListener('submit', handleOrderTracking);
    }
}

function handleOrderTracking(e) {
    e.preventDefault();
    const trackingId = document.getElementById('trackingId').value.trim();
    
    if (!trackingId) {
        showNotification('Please enter a tracking ID', 'warning');
        return;
    }

    showLoading(e.target.querySelector('button[type="submit"]'));
    
    // Simulate API call
    setTimeout(() => {
        fetchOrderStatus(trackingId);
        hideLoading(e.target.querySelector('button[type="submit"]'));
    }, 1000);
}

function fetchOrderStatus(trackingId) {
    // Mock order tracking data
    const mockOrderData = {
        'TRK202412341001': {
            status: 'shipped',
            estimatedDelivery: '2024-12-20',
            trackingSteps: [
                { status: 'Order Placed', date: '2024-12-15', completed: true },
                { status: 'Order Confirmed', date: '2024-12-16', completed: true },
                { status: 'Processing', date: '2024-12-17', completed: true },
                { status: 'Shipped', date: '2024-12-18', completed: true },
                { status: 'Out for Delivery', date: '2024-12-20', completed: false },
                { status: 'Delivered', date: '', completed: false }
            ]
        }
    };

    const orderData = mockOrderData[trackingId];
    
    if (orderData) {
        displayOrderTracking(orderData);
    } else {
        showNotification('Order not found. Please check your tracking ID.', 'error');
    }
}

function displayOrderTracking(orderData) {
    const trackingResult = document.getElementById('trackingResult');
    if (!trackingResult) return;

    const trackingHTML = `
        <div class="tracking-info">
            <h4>Order Status: <span class="status-badge ${getStatusBadgeClass(orderData.status)}">${orderData.status.toUpperCase()}</span></h4>
            <p>Estimated Delivery: <strong>${formatDate(orderData.estimatedDelivery)}</strong></p>
            
            <div class="tracking-timeline">
                ${orderData.trackingSteps.map(step => `
                    <div class="timeline-item ${step.completed ? 'completed' : ''}">
                        <div class="timeline-marker">
                            <i class="fas ${step.completed ? 'fa-check' : 'fa-clock'}"></i>
                        </div>
                        <div class="timeline-content">
                            <h5>${step.status}</h5>
                            <p>${step.date ? formatDate(step.date) : 'Pending'}</p>
                        </div>
                    </div>
                `).join('')}
            </div>
        </div>
    `;
    
    trackingResult.innerHTML = trackingHTML;
    trackingResult.style.display = 'block';
}

// Profile Management
function updateProfile(formData) {
    showLoading(document.querySelector('#profileForm button[type="submit"]'));
    
    // Simulate API call
    setTimeout(() => {
        // In a real application, this would be an API call
        showNotification('Profile updated successfully!', 'success');
        hideLoading(document.querySelector('#profileForm button[type="submit"]'));
    }, 1000);
}

// Order History
function loadOrderHistory() {
    const orderHistoryContainer = document.getElementById('orderHistory');
    if (!orderHistoryContainer) return;

    showLoading(orderHistoryContainer);
    
    // Simulate API call
    setTimeout(() => {
        const mockOrders = [
            {
                id: 'ORD20241215001',
                date: '2024-12-15',
                total: 45.99,
                status: 'delivered',
                items: [
                    { name: 'Paracetamol 500mg', quantity: 2, price: 5.99 },
                    { name: 'Vitamin D3', quantity: 1, price: 15.99 }
                ]
            },
            {
                id: 'ORD20241210002',
                date: '2024-12-10',
                total: 32.50,
                status: 'shipped',
                items: [
                    { name: 'Amoxicillin 250mg', quantity: 1, price: 12.50 },
                    { name: 'Cough Syrup', quantity: 2, price: 8.75 }
                ]
            }
        ];

        displayOrderHistory(mockOrders);
        hideLoading(orderHistoryContainer);
    }, 1000);
}

function displayOrderHistory(orders) {
    const container = document.getElementById('orderHistory');
    if (!container) return;

    if (orders.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 3rem;">
                <i class="fas fa-shopping-bag" style="font-size: 4rem; color: #ccc; margin-bottom: 1rem;"></i>
                <h3>No orders yet</h3>
                <p>Start shopping to see your order history here.</p>
                <a href="dashboard.php" class="btn btn-primary">
                    <i class="fas fa-shopping-cart"></i> Start Shopping
                </a>
            </div>
        `;
        return;
    }

    const ordersHTML = orders.map(order => `
        <div class="order-card">
            <div class="order-header">
                <div>
                    <h4>Order #${order.id}</h4>
                    <p>Placed on ${formatDate(order.date)}</p>
                </div>
                <div>
                    <span class="status-badge ${getStatusBadgeClass(order.status)}">
                        ${order.status.toUpperCase()}
                    </span>
                    <p><strong>${formatCurrency(order.total)}</strong></p>
                </div>
            </div>
            <div class="order-items">
                ${order.items.map(item => `
                    <div class="order-item">
                        <span>${item.name} x${item.quantity}</span>
                        <span>${formatCurrency(item.price * item.quantity)}</span>
                    </div>
                `).join('')}
            </div>
            <div class="order-actions">
                <button class="btn btn-secondary" onclick="trackOrder('${order.id}')">
                    <i class="fas fa-truck"></i> Track Order
                </button>
                <button class="btn btn-secondary" onclick="reorderItems('${order.id}')">
                    <i class="fas fa-redo"></i> Reorder
                </button>
                <button class="btn btn-secondary" onclick="downloadInvoice('${order.id}')">
                    <i class="fas fa-download"></i> Invoice
                </button>
            </div>
        </div>
    `).join('');

    container.innerHTML = ordersHTML;
}

// Reorder functionality
function reorderItems(orderId) {
    showNotification('Adding items to cart...', 'info');
    
    // Simulate adding items to cart
    setTimeout(() => {
        // In a real application, this would fetch the order details and add items to cart
        showNotification('Items added to cart successfully!', 'success');
        updateCartCount();
    }, 1000);
}

// Download invoice
function downloadInvoice(orderId) {
    showNotification('Preparing invoice...', 'info');
    
    // Simulate invoice download
    setTimeout(() => {
        // In a real application, this would generate and download the invoice
        const link = document.createElement('a');
        link.href = `../api/download_invoice.php?order_id=${orderId}`;
        link.download = `invoice_${orderId}.pdf`;
        link.click();
        
        showNotification('Invoice downloaded!', 'success');
    }, 1000);
}

// User Preferences
function loadUserPreferences() {
    const preferences = loadFromStorage('userPreferences', {
        theme: 'light',
        notifications: true,
        autoSave: true,
        language: 'en'
    });

    applyUserPreferences(preferences);
}

function applyUserPreferences(preferences) {
    // Apply theme
    if (preferences.theme === 'dark') {
        document.body.classList.add('dark-theme');
    }

    // Apply other preferences
    if (preferences.autoSave) {
        enableAutoSave('profileForm');
    }
}

// Cart Synchronization
function checkCartSync() {
    // In a real application, this would sync cart with server
    const serverCart = {}; // This would come from an API call
    const localCart = loadFromStorage('cart', {});
    
    // Merge carts if needed
    const mergedCart = { ...serverCart, ...localCart };
    saveToStorage('cart', mergedCart);
    cart = mergedCart;
    updateCartCount();
}

// Medicine comparison
function addToComparison(medicineId) {
    let comparison = loadFromStorage('comparison', []);
    
    if (comparison.includes(medicineId)) {
        showNotification('Medicine already in comparison', 'warning');
        return;
    }
    
    if (comparison.length >= 3) {
        showNotification('You can compare maximum 3 medicines', 'warning');
        return;
    }
    
    comparison.push(medicineId);
    saveToStorage('comparison', comparison);
    showNotification('Medicine added to comparison', 'success');
    updateComparisonUI();
}

function updateComparisonUI() {
    const comparison = loadFromStorage('comparison', []);
    const comparisonButtons = document.querySelectorAll('.compare-btn');
    
    comparisonButtons.forEach(btn => {
        const medicineId = btn.dataset.medicineId;
        if (comparison.includes(medicineId)) {
            btn.classList.add('active');
            btn.innerHTML = '<i class="fas fa-check"></i> In Comparison';
        } else {
            btn.classList.remove('active');
            btn.innerHTML = '<i class="fas fa-balance-scale"></i> Compare';
        }
    });
    
    // Update comparison counter
    const comparisonCounter = document.getElementById('comparisonCounter');
    if (comparisonCounter) {
        comparisonCounter.textContent = comparison.length;
        comparisonCounter.style.display = comparison.length > 0 ? 'inline' : 'none';
    }
}

// Initialize comparison on page load
document.addEventListener('DOMContentLoaded', function() {
    updateComparisonUI();
});