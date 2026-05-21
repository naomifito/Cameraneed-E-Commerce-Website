# SnapRent PHP Integration Guide

This document explains how to integrate your existing JavaScript application with the new PHP backend and MySQL database.

## Setup Steps

### 1. Database Setup

1. Create a MySQL database named `snaprent`
2. Import the SQL schema:
   ```
   mysql -u root -p snaprent < snaprent_db.sql
   ```
   Alternatively, use phpMyAdmin or another MySQL client to import the file.

3. Test the database connection:
   - Open `test_connection.php` in your browser
   - Verify that all tables are created successfully

### 2. Configure Database Connection

If needed, update the database credentials in `config/db_connection.php`:

```php
$db_host = "localhost";      // Your database host 
$db_name = "snaprent";       // Your database name
$db_user = "root";           // Your MySQL username
$db_pass = "";               // Your MySQL password
```

### 3. Frontend Integration

#### Add the API Adapter to HTML Files

Add the following script tag before your app.js in each HTML file:

```html
<script src="js/api-adapter.js"></script>
<script src="app.js"></script>
```

#### Modify app.js to Use the API

Your existing `app.js` uses localStorage for data management. To use the database:

1. Replace localStorage calls with API calls
2. Add `async/await` to functions that need to wait for API responses

## API Usage Examples

### Products

```javascript
// Get all products
const products = await SnapRentAPI.products.getAll();

// Get product by ID
const product = await SnapRentAPI.products.getById('kamera-mirrorless-1');

// Get products by category
const categoryProducts = await SnapRentAPI.products.getByCategory(1);
```

### Categories

```javascript
// Get all categories
const categories = await SnapRentAPI.categories.getAll();

// Get categories with product count
const categoriesWithCount = await SnapRentAPI.categories.getAllWithProductCount();
```

### Orders

```javascript
// Create new order
const orderData = {
  fullName: 'Budi Santoso',
  whatsApp: '081234567890',
  ktpId: 'ID-12345678',
  rentalDate: '2023-12-30',
  notes: 'Catatan tambahan',
  items: [
    { productId: 'kamera-dslr-1', quantity: 1, duration: 2 }
  ]
};
const result = await SnapRentAPI.orders.create(orderData);
const orderId = result.order_id;

// Get order by ID
const order = await SnapRentAPI.orders.getById(orderId);

// Update order status
await SnapRentAPI.orders.updateStatus(orderId, 'PEMBAYARAN_DIKONFIRMASI');
```

### Cart

The API adapter includes a cart module that mimics your existing cart functionality:

```javascript
// Add item to cart
await SnapRentAPI.cart.addItem('kamera-dslr-1', 1, 2);

// Get cart items
const cartItems = SnapRentAPI.cart.getItems();

// Update quantity
await SnapRentAPI.cart.updateQuantity('kamera-dslr-1', 2);

// Update duration
SnapRentAPI.cart.updateDuration('kamera-dslr-1', 3);

// Remove item
SnapRentAPI.cart.removeItem('kamera-dslr-1');

// Get cart total
const total = await SnapRentAPI.cart.getTotal();

// Check cart availability
const availability = await SnapRentAPI.cart.checkAvailability();

// Clear cart
SnapRentAPI.cart.clear();
```

## PHP Web Server

To run your application with PHP:

1. Start the built-in PHP development server:
   ```
   php -S localhost:8000
   ```

2. Access the application at `http://localhost:8000`

## Troubleshooting

### Database Connection Issues

- Ensure MySQL is running
- Verify database credentials
- Check that the snaprent database exists
- Make sure your MySQL user has proper permissions

### API Errors

- Check browser console for error messages
- Verify that API endpoints are accessible
- Ensure proper JSON format in requests

## Migration Strategy

For a gradual migration:

1. First implement read operations (products/categories)
2. Then implement order creation
3. Finally implement payment processing

This allows for a phased approach rather than switching everything at once.
