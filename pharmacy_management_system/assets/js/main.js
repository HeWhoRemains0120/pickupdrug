// Main JavaScript for Pharmacy Management System

// Global variables
let cart = JSON.parse(localStorage.getItem('cart') || '{}');
let notifications = [];

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
    initializeEventListeners();
    loadNotifications();
});

// Event Listeners
function initializeEventListeners() {
    // Mobile menu toggle
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', toggleMobileMenu);
    }

    // Search form
    const searchForm = document.querySelector('form[method="GET"]');
    if (searchForm) {
        searchForm.addEventListener('submit', handleSearch);
    }

    // Quantity inputs
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', validateQuantity);
    });

    // Close modals when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            closeModal(e.target.id);
        }
    });

    // Form validation
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', validateForm);
    });
}

// Cart Functions
function addToCart(medicineId, button) {
    const medicineCard = button.closest('.medicine-card');
    const quantityInput = medicineCard.querySelector('.quantity-input');
    const quantity = parseInt(quantityInput.value) || 1;
    const medicineName = medicineCard.querySelector('.medicine-title').textContent;
    
    // Disable button temporarily
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';

    // Simulate API call
    setTimeout(() => {
        // Add to cart logic (in a real app, this would be an API call)
        if (cart[medicineId]) {
            cart[medicineId] += quantity;
        } else {
            cart[medicineId] = quantity;
        }

        // Save to localStorage
        localStorage.setItem('cart', JSON.stringify(cart));
        
        // Update UI
        updateCartCount();
        showNotification(`${medicineName} added to cart!`, 'success');
        
        // Reset button
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-cart-plus"></i> Add to Cart';
        
        // Reset quantity to 1
        quantityInput.value = 1;
    }, 500);
}

function removeFromCart(medicineId) {
    if (cart[medicineId]) {
        delete cart[medicineId];
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
        showNotification('Item removed from cart', 'info');
    }
}

function updateCartQuantity(medicineId, quantity) {
    if (quantity <= 0) {
        removeFromCart(medicineId);
    } else {
        cart[medicineId] = quantity;
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
    }
}

function clearCart() {
    cart = {};
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    showNotification('Cart cleared', 'info');
}

function updateCartCount() {
    const totalItems = Object.values(cart).reduce((sum, quantity) => sum + quantity, 0);
    const cartCountElements = document.querySelectorAll('#cartCount, .cart-count');
    
    cartCountElements.forEach(element => {
        element.textContent = totalItems;
        if (totalItems > 0) {
            element.style.display = 'inline';
        } else {
            element.style.display = 'none';
        }
    });
}

// Modal Functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
    }
}

// Notification System
function showNotification(message, type = 'info', duration = 4000) {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());

    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" 
                    style="background: none; border: none; color: inherit; font-size: 1.2rem; cursor: pointer;">
                &times;
            </button>
        </div>
    `;

    // Add to page
    document.body.appendChild(notification);

    // Show notification
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);

    // Auto remove
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 300);
    }, duration);
}

// Form Validation
function validateForm(e) {
    const form = e.target;
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            showFieldError(field, 'This field is required');
            isValid = false;
        } else {
            clearFieldError(field);
        }
    });

    // Email validation
    const emailFields = form.querySelectorAll('input[type="email"]');
    emailFields.forEach(field => {
        if (field.value && !isValidEmail(field.value)) {
            showFieldError(field, 'Please enter a valid email address');
            isValid = false;
        }
    });

    // Password confirmation
    const password = form.querySelector('input[name="password"]');
    const confirmPassword = form.querySelector('input[name="confirm_password"]');
    if (password && confirmPassword && password.value !== confirmPassword.value) {
        showFieldError(confirmPassword, 'Passwords do not match');
        isValid = false;
    }

    if (!isValid) {
        e.preventDefault();
        showNotification('Please fix the errors in the form', 'error');
    }
}

function showFieldError(field, message) {
    clearFieldError(field);
    field.style.borderColor = 'var(--danger-color)';
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.style.color = 'var(--danger-color)';
    errorDiv.style.fontSize = '0.8rem';
    errorDiv.style.marginTop = '0.25rem';
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
}

function clearFieldError(field) {
    field.style.borderColor = '';
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Utility Functions
function validateQuantity(e) {
    const input = e.target;
    const min = parseInt(input.getAttribute('min')) || 1;
    const max = parseInt(input.getAttribute('max')) || 999;
    let value = parseInt(input.value);

    if (isNaN(value) || value < min) {
        input.value = min;
    } else if (value > max) {
        input.value = max;
        showNotification(`Maximum quantity available: ${max}`, 'warning');
    }
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Search Functions
function handleSearch(e) {
    const form = e.target;
    const searchInput = form.querySelector('input[name="search"]');
    
    if (searchInput && searchInput.value.trim().length > 0 && searchInput.value.trim().length < 3) {
        e.preventDefault();
        showNotification('Search term must be at least 3 characters long', 'warning');
        return false;
    }
}

// Auto-search with debouncing
const debouncedSearch = debounce(function(searchTerm) {
    if (searchTerm.length >= 3) {
        // Perform search
        window.location.href = `dashboard.php?search=${encodeURIComponent(searchTerm)}`;
    }
}, 500);

// Order Tracking
function trackOrder() {
    const trackingId = prompt('Enter your tracking ID:');
    if (trackingId) {
        // In a real application, this would make an API call
        showNotification('Order tracking feature coming soon!', 'info');
    }
}

// Loading States
function showLoading(button) {
    if (button) {
        button.disabled = true;
        button.dataset.originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
    }
}

function hideLoading(button) {
    if (button && button.dataset.originalText) {
        button.disabled = false;
        button.innerHTML = button.dataset.originalText;
        delete button.dataset.originalText;
    }
}

// Mobile Menu
function toggleMobileMenu() {
    const navMenu = document.querySelector('.nav-menu');
    if (navMenu) {
        navMenu.classList.toggle('mobile-open');
    }
}

// Local Storage Management
function saveToStorage(key, data) {
    try {
        localStorage.setItem(key, JSON.stringify(data));
    } catch (e) {
        console.error('Failed to save to localStorage:', e);
    }
}

function loadFromStorage(key, defaultValue = null) {
    try {
        const data = localStorage.getItem(key);
        return data ? JSON.parse(data) : defaultValue;
    } catch (e) {
        console.error('Failed to load from localStorage:', e);
        return defaultValue;
    }
}

// Print Functions
function printInvoice(orderId) {
    const printWindow = window.open(`../api/print_invoice.php?order_id=${orderId}`, '_blank');
    printWindow.onload = function() {
        printWindow.print();
    };
}

// Export Functions
function exportData(data, filename, type = 'csv') {
    let content = '';
    let mimeType = '';

    if (type === 'csv') {
        const headers = Object.keys(data[0]).join(',');
        const rows = data.map(row => Object.values(row).join(',')).join('\n');
        content = headers + '\n' + rows;
        mimeType = 'text/csv';
    } else if (type === 'json') {
        content = JSON.stringify(data, null, 2);
        mimeType = 'application/json';
    }

    const blob = new Blob([content], { type: mimeType });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `${filename}.${type}`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}

// Image Preview
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById(previewId);
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Confirmation Dialogs
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// Date Formatting
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Status Badge Colors
function getStatusBadgeClass(status) {
    const statusClasses = {
        'pending': 'badge-warning',
        'confirmed': 'badge-info',
        'processing': 'badge-primary',
        'shipped': 'badge-success',
        'delivered': 'badge-success',
        'cancelled': 'badge-danger',
        'paid': 'badge-success',
        'failed': 'badge-danger'
    };
    return statusClasses[status] || 'badge-secondary';
}

// Initialize tooltips (if using Bootstrap or similar)
function initializeTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

function showTooltip(e) {
    const element = e.target;
    const tooltipText = element.getAttribute('data-tooltip');
    
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = tooltipText;
    tooltip.style.position = 'absolute';
    tooltip.style.background = 'rgba(0,0,0,0.8)';
    tooltip.style.color = 'white';
    tooltip.style.padding = '5px 10px';
    tooltip.style.borderRadius = '4px';
    tooltip.style.fontSize = '12px';
    tooltip.style.zIndex = '1000';
    tooltip.style.pointerEvents = 'none';
    
    document.body.appendChild(tooltip);
    
    const rect = element.getBoundingClientRect();
    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
    tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
    
    element._tooltip = tooltip;
}

function hideTooltip(e) {
    const element = e.target;
    if (element._tooltip) {
        element._tooltip.remove();
        delete element._tooltip;
    }
}

// Keyboard Shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl+S to save (prevent default browser save)
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        const saveButton = document.querySelector('.btn-primary[type="submit"]');
        if (saveButton) {
            saveButton.click();
        }
    }
    
    // Escape to close modals
    if (e.key === 'Escape') {
        const openModal = document.querySelector('.modal.show');
        if (openModal) {
            closeModal(openModal.id);
        }
    }
});

// Auto-save for forms (drafts)
function enableAutoSave(formId) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    const inputs = form.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.addEventListener('input', debounce(() => {
            saveFormDraft(formId);
        }, 1000));
    });
    
    // Load saved draft on page load
    loadFormDraft(formId);
}

function saveFormDraft(formId) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    const formData = new FormData(form);
    const draftData = {};
    
    for (let [key, value] of formData.entries()) {
        draftData[key] = value;
    }
    
    saveToStorage(`draft_${formId}`, draftData);
}

function loadFormDraft(formId) {
    const draftData = loadFromStorage(`draft_${formId}`);
    if (!draftData) return;
    
    const form = document.getElementById(formId);
    if (!form) return;
    
    Object.keys(draftData).forEach(key => {
        const input = form.querySelector(`[name="${key}"]`);
        if (input && input.type !== 'password') {
            input.value = draftData[key];
        }
    });
}

function clearFormDraft(formId) {
    localStorage.removeItem(`draft_${formId}`);
}

// Performance monitoring
const performanceMonitor = {
    startTime: performance.now(),
    
    mark: function(name) {
        performance.mark(name);
    },
    
    measure: function(name, startMark, endMark) {
        performance.measure(name, startMark, endMark);
    },
    
    getMetrics: function() {
        return {
            loadTime: performance.now() - this.startTime,
            navigationTiming: performance.getEntriesByType('navigation')[0],
            resourceTiming: performance.getEntriesByType('resource')
        };
    }
};

// Initialize performance monitoring
performanceMonitor.mark('script-start');

// Page visibility API for pausing operations when tab is not visible
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'hidden') {
        // Pause non-essential operations
        console.log('Page is now hidden');
    } else {
        // Resume operations
        console.log('Page is now visible');
    }
});

// Service Worker registration (for PWA capabilities)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js')
            .then(function(registration) {
                console.log('ServiceWorker registration successful');
            })
            .catch(function(err) {
                console.log('ServiceWorker registration failed');
            });
    });
}

// Error handling
window.addEventListener('error', function(e) {
    console.error('Global error:', e.error);
    // In production, you might want to send this to an error tracking service
});

window.addEventListener('unhandledrejection', function(e) {
    console.error('Unhandled promise rejection:', e.reason);
    // Handle promise rejections
});