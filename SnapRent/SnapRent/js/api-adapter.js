/**
 * SnapRent API Adapter
 * Connects frontend JavaScript to backend PHP API
 */

const SnapRentAPI = (function() {
    // Base URL for API endpoints
    const API_BASE_URL = 'api/';
    
    // Helper function for making API calls
    async function apiCall(endpoint, method = 'GET', data = null) {
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        };
        
        // Add request body for POST/PUT methods
        if (data && (method === 'POST' || method === 'PUT')) {
            options.body = JSON.stringify(data);
        }
        
        try {
            const response = await fetch(`${API_BASE_URL}${endpoint}`, options);
            if (!response.ok) {
                throw new Error(`API error: ${response.status} ${response.statusText}`);
            }
            
            return await response.json();
        } catch (error) {
            console.error('API request failed:', error);
            throw error;
        }
    }
    
    // Products API
    const products = {
        getAll: async function() {
            return await apiCall('products.php');
        },
        
        getById: async function(productId) {
            return await apiCall(`products.php?action=get_by_id&id=${encodeURIComponent(productId)}`);
        },
        
        getByCategory: async function(categoryId) {
            return await apiCall(`products.php?action=get_by_category&category_id=${encodeURIComponent(categoryId)}`);
        },
        
        getAvailable: async function() {
            return await apiCall('products.php?action=get_available');
        },
        
        checkAvailability: async function(productId, quantity) {
            const product = await this.getById(productId);
            return product && product.is_available && product.stock >= quantity;
        }
    };
    
    // Categories API
    const categories = {
        getAll: async function() {
            return await apiCall('products.php?action=categories');
        },
        
        getAllWithProductCount: async function() {
            return await apiCall('products.php?action=categories_with_count');
        }
    };
    
    // Orders API
    const orders = {
        getAll: async function() {
            return await apiCall('orders.php');
        },
        
        getById: async function(orderId) {
            return await apiCall(`orders.php?action=get_by_id&id=${encodeURIComponent(orderId)}`);
        },
        
        getByStatus: async function(status) {
            return await apiCall(`orders.php?action=get_by_status&status=${encodeURIComponent(status)}`);
        },
        
        getUpcoming: async function() {
            return await apiCall('orders.php?action=upcoming');
        },
        
        create: async function(orderData) {
            return await apiCall('orders.php?action=create', 'POST', orderData);
        },
        
        updateStatus: async function(orderId, newStatus) {
            return await apiCall('orders.php?action=update_status', 'POST', {
                orderId: orderId,
                status: newStatus
            });
        }
    };
    
    // Payments API
    const payments = {
        getByOrderId: async function(orderId) {
            return await apiCall(`payments.php?action=by_order_id&order_id=${encodeURIComponent(orderId)}`);
        },
        
        recordPayment: async function(paymentData) {
            return await apiCall('payments.php?action=record', 'POST', paymentData);
        },
        
        updateStatus: async function(paymentId, status, orderId) {
            return await apiCall('payments.php?action=update_status', 'POST', {
                paymentId: paymentId,
                status: status,
                orderId: orderId
            });
        }
    };
    
    // Cart Management
    const cart = {
        _items: [],
        
        // Load cart from localStorage (for backward compatibility)
        loadFromLocalStorage: function() {
            try {
                const raw = localStorage.getItem('snaprent_cart_v1');
                if (raw) {
                    this._items = JSON.parse(raw) || [];
                }
            } catch (e) {
                console.error('Failed to load cart from localStorage', e);
                this._items = [];
            }
            
            return this._items;
        },
        
        // Save cart to localStorage (for backward compatibility)
        saveToLocalStorage: function() {
            localStorage.setItem('snaprent_cart_v1', JSON.stringify(this._items));
        },
        
        // Get all items in cart
        getItems: function() {
            return this._items;
        },
        
        // Add item to cart
        addItem: async function(productId, quantity = 1, duration = 1) {
            // Check product availability first
            const isAvailable = await products.checkAvailability(productId, quantity);
            
            if (!isAvailable) {
                console.error('Product not available or insufficient stock');
                return this._items;
            }
            
            // Find if product already exists in cart
            const existingItemIndex = this._items.findIndex(item => item.productId === productId);
            
            if (existingItemIndex >= 0) {
                // Update quantity if product already in cart
                this._items[existingItemIndex].quantity += quantity;
            } else {
                // Add new item to cart
                this._items.push({
                    productId: productId,
                    quantity: quantity,
                    duration: duration,
                    addedAt: new Date().toISOString()
                });
            }
            
            this.saveToLocalStorage();
            return this._items;
        },
        
        // Remove item from cart
        removeItem: function(productId) {
            this._items = this._items.filter(item => item.productId !== productId);
            this.saveToLocalStorage();
            return this._items;
        },
        
        // Update item quantity
        updateQuantity: async function(productId, quantity) {
            if (quantity <= 0) {
                return this.removeItem(productId);
            }
            
            // Check product availability first
            const isAvailable = await products.checkAvailability(productId, quantity);
            
            const itemIndex = this._items.findIndex(item => item.productId === productId);
            if (itemIndex >= 0) {
                if (isAvailable) {
                    this._items[itemIndex].quantity = quantity;
                } else {
                    const product = await products.getById(productId);
                    this._items[itemIndex].quantity = product.stock;
                    console.warn(`Quantity adjusted to maximum stock: ${product.stock}`);
                }
            }
            
            this.saveToLocalStorage();
            return this._items;
        },
        
        // Update item duration
        updateDuration: function(productId, duration) {
            if (duration < 1) duration = 1;
            
            const itemIndex = this._items.findIndex(item => item.productId === productId);
            if (itemIndex >= 0) {
                this._items[itemIndex].duration = duration;
            }
            
            this.saveToLocalStorage();
            return this._items;
        },
        
        // Clear cart
        clear: function() {
            this._items = [];
            this.saveToLocalStorage();
        },
        
        // Calculate cart total
        async getTotal() {
            let total = 0;
            
            for (const item of this._items) {
                const product = await products.getById(item.productId);
                if (product) {
                    const duration = item.duration || 1;
                    total += product.price_per_day * item.quantity * duration;
                }
            }
            
            return total;
        },
        
        // Check cart availability
        async checkAvailability() {
            const issues = [];
            
            for (const item of this._items) {
                const product = await products.getById(item.productId);
                
                if (!product) {
                    issues.push({ id: item.productId, message: 'Product not found' });
                } else if (!product.is_available) {
                    issues.push({ id: item.productId, message: 'Product is not available for rent' });
                } else if (item.quantity > product.stock) {
                    issues.push({ id: item.productId, message: `Insufficient stock (available: ${product.stock})` });
                }
            }
            
            return { valid: issues.length === 0, issues };
        }
    };
    
    // Format price in Indonesian Rupiah
    function formatPrice(num) {
        return 'Rp ' + num.toLocaleString('id-ID');
    }
    
    // Initialize the adapter
    function init() {
        // Load cart from localStorage for backward compatibility
        cart.loadFromLocalStorage();
        
        console.log('SnapRent API Adapter initialized');
    }
    
    // Public API
    return {
        init,
        products,
        categories,
        orders,
        payments,
        cart,
        formatPrice
    };
})();

// Initialize the API adapter
document.addEventListener('DOMContentLoaded', function() {
    SnapRentAPI.init();
});
