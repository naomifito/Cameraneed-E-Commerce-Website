/**
 * SnapRent Database Adapter
 * Compatibility layer to bridge existing app.js with the new PHP/MySQL backend
 */

window.DbAdapter = (function() {
    // Check if API adapter is available
    const apiAvailable = typeof SnapRentAPI !== 'undefined';
    
    // Log status
    console.log('DbAdapter initialized, API available:', apiAvailable);
    
    // Local catalog cache
    let catalogCache = null;
    
    // Get catalog from API or fall back to app.js hardcoded catalog
    async function getCatalog() {
        if (apiAvailable) {
            try {
                // Try to fetch from API
                if (!catalogCache) {
                    catalogCache = await SnapRentAPI.products.getAll();
                }
                
                // Format the data to match app.js expected format
                return catalogCache.map(p => ({
                    id: p.product_id,
                    category: p.category_name,
                    name: p.name,
                    pricePerDay: parseFloat(p.price_per_day),
                    image: p.image_url,
                    specs: p.specs,
                    stock: parseInt(p.stock),
                    isAvailable: p.is_available === '1' || p.is_available === true
                }));
            } catch (err) {
                console.error('Failed to load catalog from API, falling back to hardcoded data:', err);
                // Fall back to original App catalog
                return window.App.getCatalog();
            }
        }
        
        // If API not available, use the hardcoded catalog
        return window.App.getCatalog();
    }
    
    // Get orders from API or fall back to localStorage
    async function getOrders() {
        if (apiAvailable) {
            try {
                return await SnapRentAPI.orders.getAll();
            } catch (err) {
                console.error('Failed to load orders from API, falling back to localStorage:', err);
                return window.App.loadOrders();
            }
        }
        
        return window.App.loadOrders();
    }
    
    // Add order to backend or fall back to localStorage
    async function addOrder(order) {
        if (apiAvailable) {
            try {
                // Map order data to the API format
                const apiOrderData = {
                    fullName: order.fullName,
                    whatsApp: order.whatsApp,
                    ktpId: order.ktpFileName || order.ktpId,
                    rentalDate: order.rentalDate,
                    notes: order.notes || '',
                    items: Array.isArray(order.items) ? order.items : [{
                        productId: order.productId,
                        quantity: 1,
                        duration: order.duration || 1
                    }]
                };
                
                // Send to API
                const result = await SnapRentAPI.orders.create(apiOrderData);
                return result.success ? result.order_id : null;
            } catch (err) {
                console.error('Failed to create order via API, falling back to localStorage:', err);
                // Fall back to App method
                window.App.addOrder(order);
                return order.id;
            }
        }
        
        // If API not available, use the original method
        window.App.addOrder(order);
        return order.id;
    }
    
    // Check if cart exists in SnapRentAPI
    function isApiCartAvailable() {
        return apiAvailable && SnapRentAPI.cart && typeof SnapRentAPI.cart.getItems === 'function';
    }
    
    // Get cart from API or fall back to localStorage
    function getCart() {
        if (isApiCartAvailable()) {
            return SnapRentAPI.cart.getItems();
        }
        
        return window.App.loadCart();
    }
    
    // Add item to cart via API or localStorage
    async function addToCart(productId, duration = 1) {
        if (isApiCartAvailable()) {
            try {
                await SnapRentAPI.cart.addItem(productId, 1, duration);
                return SnapRentAPI.cart.getItems();
            } catch (err) {
                console.error('Failed to add to cart via API, falling back to localStorage:', err);
                return window.App.addToCart(productId, duration);
            }
        }
        
        return window.App.addToCart(productId, duration);
    }
    
    // Update cart quantity via API or localStorage
    async function updateCartQuantity(productId, quantity) {
        if (isApiCartAvailable()) {
            try {
                await SnapRentAPI.cart.updateQuantity(productId, quantity);
                return SnapRentAPI.cart.getItems();
            } catch (err) {
                console.error('Failed to update cart via API, falling back to localStorage:', err);
                return window.App.updateCartQuantity(productId, quantity);
            }
        }
        
        return window.App.updateCartQuantity(productId, quantity);
    }
    
    // Calculate cart total
    async function getCartTotal() {
        if (isApiCartAvailable()) {
            try {
                return await SnapRentAPI.cart.getTotal();
            } catch (err) {
                console.error('Failed to get cart total via API, falling back to localStorage:', err);
                return window.App.getCartTotal();
            }
        }
        
        return window.App.getCartTotal();
    }
    
    // Return public methods
    return {
        getCatalog,
        getOrders,
        addOrder,
        getCart,
        addToCart,
        updateCartQuantity,
        getCartTotal,
        isApiAvailable: () => apiAvailable
    };
})();
