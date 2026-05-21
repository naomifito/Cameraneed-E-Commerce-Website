// ================================================
//  APP.JS — versi API (async/await)
//  Tidak menggunakan localStorage untuk produk/order
// ================================================
window.App = (function () {

  // ======================
  //   PRODUCT FUNCTIONS
  // ======================

  async function getCatalog() {
    return await SnapRentAPI.products.getAll();
  }

  async function findProductById(id) {
    return await SnapRentAPI.products.getById(id);
  }

  async function getCategories() {
    return await SnapRentAPI.categories.getAllWithProductCount();
  }

  async function renderFeaturedCategories() {
    const container = document.getElementById('featured-categories');
    if (!container) return;

    const categories = await SnapRentAPI.categories.getAllWithProductCount();

    const limited = categories.slice(0, 6);

    container.innerHTML = limited
      .map((cat) => `
        <article class="bg-white rounded-lg border flex flex-col justify-between p-4">
          <div class="bg-blue-50 rounded-md p-3 mb-3">
            <h3 class="font-semibold text-blue-900 text-base">${cat.name}</h3>
            <p class="text-xs text-blue-700 mt-1">${cat.productCount} unit tersedia</p>
          </div>
          <div>
            <div class="flex items-center justify-between text-xs border-t border-slate-100 pt-3">
              <p class="text-slate-600">Mulai dari</p>
              <p class="font-semibold text-blue-700">${formatPrice(cat.minPrice)}/hari</p>
            </div>
            <div class="mt-3">
              <a href="catalog.html#${encodeURIComponent(cat.name)}"
                class="w-full inline-flex justify-center items-center py-2 px-3 rounded-md bg-blue-100 text-blue-800 text-xs font-semibold hover:bg-blue-200">
                Lihat ${cat.productCount} produk ${cat.name} →
              </a>
            </div>
          </div>
        </article>
      `).join('');
  }

  function formatPrice(num) {
    return 'Rp ' + Number(num).toLocaleString('id-ID');
  }


  // ======================
  //       ORDER API
  // ======================

  async function loadOrders() {
    return await SnapRentAPI.orders.getAll();
  }

  async function addOrder(orderData) {
    return await SnapRentAPI.orders.create(orderData);
  }

  async function updateOrder(orderId, updateData) {
    return await SnapRentAPI.orders.update(orderId, updateData);
  }

  async function updateOrderStatus(orderId, status) {
    return await SnapRentAPI.orders.updateStatus(orderId, status);
  }

  async function deleteOrder(orderId) {
    return await fetch('api/orders.php?action=delete_order', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ orderId }),
    }).then(async (res) => {
      const data = await res.json();
      if (data && data.success) return true;
      throw new Error(data?.error || 'Gagal menghapus order');
    });
  }

  async function getOrderById(orderId) {
    return await SnapRentAPI.orders.getById(orderId);
  }

  // (generate id tetap lokal)
  function generateOrderId() {
    const ts = Date.now().toString(36);
    const rand = Math.random().toString(36).substring(2, 8);
    return `ORD-${ts}-${rand}`.toUpperCase();
  }


  // ======================
  //   DATE UTILITIES
  // ======================
  function parseDateInput(value) {
    const [y, m, d] = value.split('-').map(Number);
    if (!y || !m || !d) return null;
    return new Date(y, m - 1, d);
  }

  function diffDays(a, b) {
    const one = new Date(a.getFullYear(), a.getMonth(), a.getDate()).getTime();
    const two = new Date(b.getFullYear(), b.getMonth(), b.getDate()).getTime();
    return Math.round((one - two) / 86400000);
  }

  function canEditOrder(order) {
    const created = new Date(order.createdAt);
    const now = new Date();
    const hours = (now - created) / (1000 * 60 * 60);
    return hours <= 8 && order.status === 'MENUNGGU_PEMBAYARAN';
  }

  function canCancelOrder(order) {
    if (order.status !== 'MENUNGGU_PEMBAYARAN') return false;
    const now = new Date();
    const rentalDate = new Date(order.rentalDate);
    return diffDays(rentalDate, now) >= 2;
  }


  // ======================
  //       CART API
  // ======================

  async function loadCart() {
    return SnapRentAPI.cart.getItems();
  }

  async function addToCart(productId, quantity = 1, duration = 1) {
    return await SnapRentAPI.cart.addItem(productId, quantity, duration);
  }

  async function removeFromCart(productId) {
    return await SnapRentAPI.cart.removeItem(productId);
  }

  async function updateCartQuantity(productId, quantity) {
    return await SnapRentAPI.cart.updateQuantity(productId, quantity);
  }

  async function updateCartItemDuration(productId, duration) {
    return await SnapRentAPI.cart.updateDuration(productId, duration);
  }

  async function clearCart() {
    return await SnapRentAPI.cart.clear();
  }

  async function getCartTotal() {
    return await SnapRentAPI.cart.getTotal();
  }

  async function checkCartAvailability() {
    return await SnapRentAPI.cart.checkAvailability();
  }

  async function getProductAvailability(productId) {
    return await SnapRentAPI.products.getAvailability(productId);
  }


  // ===========================================
  //  RETURN PUBLIC API
  // ===========================================
  return {
    // Products
    getCatalog,
    findProductById,
    getCategories,
    renderFeaturedCategories,
    formatPrice,

    // Orders
    loadOrders,
    addOrder,
    updateOrder,
    updateOrderStatus,
    deleteOrder,
    getOrderById,
    generateOrderId,
    parseDateInput,
    diffDays,
    canEditOrder,
    canCancelOrder,

    // Cart
    loadCart,
    addToCart,
    removeFromCart,
    updateCartQuantity,
    updateCartItemDuration,
    clearCart,
    getCartTotal,
    getProductAvailability,
    checkCartAvailability,
  };
})();

