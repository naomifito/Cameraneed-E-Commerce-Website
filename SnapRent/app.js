window.App = (function () {
  const PRODUCT_CACHE = new Map();

  const CATALOG = [
    {
      id: 'kamera-mirrorless-1',
      category: 'Kamera Mirrorless',
      name: 'Sony A6400 Kit 16-50mm',
      pricePerDay: 350000,
      image: 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Sony+A6400',
      specs: '24.2MP APS-C, 4K Video, flip screen, cocok untuk konten kreator.',
      stock: 3,
      isAvailable: true,
    },
    {
      id: 'kamera-mirrorless-2',
      category: 'Kamera Mirrorless',
      name: 'Fujifilm X-T30 + 18-55mm',
      pricePerDay: 380000,
      image: 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Fuji+X-T30',
      specs: '26.1MP, film simulation, cocok untuk foto dan video sinematik.',
      stock: 2,
      isAvailable: true,
    },
    {
      id: 'kamera-dslr-1',
      category: 'Kamera DSLR',
      name: 'Canon EOS 80D Kit',
      pricePerDay: 320000,
      image: 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Canon+EOS+80D',
      specs: '24.2MP, Dual Pixel AF, cocok untuk event dan produksi sederhana.',
      stock: 2,
      isAvailable: true,
    },
    {
      id: 'kamera-dslr-2',
      category: 'Kamera DSLR',
      name: 'Nikon D750 Body',
      pricePerDay: 400000,
      image: 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Nikon+D750',
      specs: 'Full frame 24.3MP, low light bagus, cocok untuk foto wedding.',
      stock: 1,
      isAvailable: true,
    },
    {
      id: 'handycam-1',
      category: 'Handycam',
      name: 'Sony Handycam HD',
      pricePerDay: 250000,
      image: 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Sony+Handycam',
      specs: 'Full HD recording, stabilizer, cocok untuk dokumentasi acara.',
      stock: 3,
      isAvailable: true,
    },
    {
      id: 'handycam-2',
      category: 'Handycam',
      name: 'Panasonic 4K Handycam',
      pricePerDay: 300000,
      image: 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Panasonic+4K',
      specs: '4K video, zoom panjang, cocok untuk seminar dan live event.',
      stock: 2,
      isAvailable: true,
    },
    {
      id: 'lighting-softbox-1',
      category: 'Lighting Softbox',
      name: 'Softbox 2 Head 85W',
      pricePerDay: 150000,
      image: 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Softbox+2Head',
      specs: 'Paket 2 lampu dengan softbox, cocok untuk studio kecil.',
      stock: 5,
      isAvailable: true,
    },
    {
      id: 'lighting-softbox-2',
      category: 'Lighting Softbox',
      name: 'Softbox 3 Head 135W',
      pricePerDay: 200000,
      image: 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Softbox+3Head',
      specs: 'Output lebih terang, cocok untuk video produk.',
      stock: 3,
      isAvailable: true,
    },
    {
      id: 'lighting-rgb-1',
      category: 'Lighting RGB',
      name: 'RGB Tube Light 60cm',
      pricePerDay: 180000,
      image: 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=RGB+Tube',
      specs: 'RGB penuh, efek lighting kreatif untuk konten.',
      stock: 4,
      isAvailable: true,
    },
    {
      id: 'lighting-rgb-2',
      category: 'Lighting RGB',
      name: 'RGB Panel Light',
      pricePerDay: 200000,
      image: 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=RGB+Panel',
      specs: 'Panel RGB dengan dimmer, cocok untuk studio.',
      stock: 2,
      isAvailable: true,
    },
    {
      id: 'tripod-1',
      category: 'Tripod',
      name: 'Tripod Aluminium 1.6m',
      pricePerDay: 50000,
      image: 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Tripod+Aluminium',
      specs: 'Ringan dan kokoh, cocok untuk kamera mirrorless/DSLR.',
      stock: 10,
      isAvailable: true,
    },
    {
      id: 'tripod-2',
      category: 'Tripod',
      name: 'Tripod Video Fluid Head',
      pricePerDay: 90000,
      image: 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Tripod+Video',
      specs: 'Kepala fluid, gerakan kamera halus untuk video.',
      stock: 5,
      isAvailable: true,
    },
    {
      id: 'mic-wireless-1',
      category: 'Microphone Wireless',
      name: 'Wireless Mic 2 Transmitter',
      pricePerDay: 170000,
      image: 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Wireless+Mic+2TX',
      specs: 'Mic clip-on, cocok untuk interview dan vlog.',
      stock: 3,
      isAvailable: true,
    },
    {
      id: 'mic-wireless-2',
      category: 'Microphone Wireless',
      name: 'Wireless Mic Compact',
      pricePerDay: 150000,
      image: 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Mic+Compact',
      specs: 'Unit kecil, mudah dipasang pada kamera/smartphone.',
      stock: 4,
      isAvailable: true,
    },
    {
      id: 'mic-shotgun-1',
      category: 'Microphone Shotgun',
      name: 'Shotgun Mic On-Camera',
      pricePerDay: 90000,
      image: 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Shotgun+Mic',
      specs: 'Fokus suara ke depan, cocok untuk run-and-gun.',
      stock: 5,
      isAvailable: true,
    },
    {
      id: 'mic-shotgun-2',
      category: 'Microphone Shotgun',
      name: 'Shotgun Mic Boom',
      pricePerDay: 130000,
      image: 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Shotgun+Boom',
      specs: 'Dengan boom pole, cocok untuk produksi film.',
      stock: 3,
      isAvailable: true,
    },
    {
      id: 'audio-recorder-1',
      category: 'Audio Recorder',
      name: 'Zoom H4n Pro',
      pricePerDay: 160000,
      image: 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Zoom+H4n',
      specs: 'Perekam audio portable, 4 track, cocok untuk interview.',
      stock: 2,
      isAvailable: true,
    },
    {
      id: 'audio-recorder-2',
      category: 'Audio Recorder',
      name: 'Tascam DR-40X',
      pricePerDay: 150000,
      image: 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Tascam+DR40X',
      specs: 'Perekam dengan XLR, cocok untuk produksi film.',
      stock: 2,
      isAvailable: true,
    },
    {
      id: 'projector-1',
      category: 'Projector',
      name: 'Projector HD 3000 lumens',
      pricePerDay: 250000,
      image: 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Projector+HD',
      specs: 'Cocok untuk presentasi kantor dan kelas.',
      stock: 3,
      isAvailable: true,
    },
    {
      id: 'projector-2',
      category: 'Projector',
      name: 'Projector Full HD 4000 lumens',
      pricePerDay: 320000,
      image: 'https://via.placeholder.com/400x260/3B82F6/FFFFFF?text=Projector+FHD',
      specs: 'Lebih terang, cocok untuk event dan pemutaran film.',
      stock: 2,
      isAvailable: true,
    },
  ];

  function formatPrice(num) {
    return 'Rp ' + num.toLocaleString('id-ID');
  }

  async function updateOrderItems(orderId, payload) {
    try {
      const response = await fetch('api/orders.php?action=update_items', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          orderId,
          ...payload,
        }),
      });

      const result = await response.json();
      if (result.success) {
        return true;
      }
      throw new Error(result.error || 'Gagal update items');
    } catch (e) {
      console.error('Gagal update items via API', e);
      throw e;
    }
  }

  function normalizeProduct(p) {
    if (!p) return null;
    const id = p.product_id || p.id;
    if (!id) return null;

    const normalized = {
      ...p,
      id,
      product_id: p.product_id || id,
      pricePerDay: p.pricePerDay ?? p.price_per_day,
      price_per_day: p.price_per_day ?? p.pricePerDay,
      isAvailable:
        p.isAvailable ??
        (p.is_available === 1 || p.is_available === '1' || p.is_available === true),
      is_available:
        p.is_available ??
        (p.isAvailable === true ? 1 : p.isAvailable === false ? 0 : p.is_available),
      stock: typeof p.stock === 'string' ? parseInt(p.stock, 10) : p.stock,
      image: p.image ?? p.image_url,
      image_url: p.image_url ?? p.image,
    };

    PRODUCT_CACHE.set(id, normalized);
    return normalized;
  }

  function findProductByIdSync(id) {
    if (!id) return null;
    if (PRODUCT_CACHE.has(id)) return PRODUCT_CACHE.get(id);
    return CATALOG.find((p) => p.id === id) || null;
  }

  async function getCatalog() {
    try {
      const response = await fetch('api/products.php');
      const products = await response.json();
      if (Array.isArray(products)) {
        return products.map((p) => normalizeProduct(p)).filter(Boolean);
      }
      return [];
    } catch (e) {
      console.error('Gagal mengambil produk dari API', e);
      // Fallback ke data hardcoded jika API gagal
      return CATALOG;
    }
  }

  async function findProductById(id) {
    try {
      const cached = findProductByIdSync(id);
      if (cached) return cached;
      const response = await fetch(`api/products.php?id=${id}`);
      const product = await response.json();
      return normalizeProduct(product) || null;
    } catch (e) {
      console.error('Gagal mengambil produk dari API', e);
      // Fallback ke data hardcoded jika API gagal
      return findProductByIdSync(id);
    }
  }

  async function getCategories() {
    try {
      const response = await fetch('api/products.php?action=categories');
      const categories = await response.json();
      
      // Group products by category
      const map = new Map();
      
      // Get products for each category
      for (const cat of categories) {
        if (!map.has(cat.name)) {
          map.set(cat.name, []);
        }
        try {
          const productsResponse = await fetch(`api/products.php?category_id=${cat.category_id}`);
          const products = await productsResponse.json();
          if (Array.isArray(products)) {
            map.get(cat.name).push(...products.map((p) => normalizeProduct(p)).filter(Boolean));
          }
        } catch (e) {
          console.error('Gagal mengambil produk untuk kategori', cat.name, e);
        }
      }
      
      return Array.from(map.entries()).map(([name, items]) => ({ name, items }));
    } catch (e) {
      console.error('Gagal mengambil kategori dari API', e);
      // Fallback ke hardcoded data
      const map = new Map();
      CATALOG.forEach((item) => {
        if (!map.has(item.category)) {
          map.set(item.category, []);
        }
        map.get(item.category).push(item);
      });
      return Array.from(map.entries()).map(([name, items]) => ({ name, items }));
    }
  }

  async function renderFeaturedCategories() {
    const container = document.getElementById('featured-categories');
    if (!container) return;
    
    try {
      const categories = await getCategories();
      const featuredCategories = categories.slice(0, 6);
      container.innerHTML = featuredCategories
        .map((cat) => {
          const minPrice = Math.min(...cat.items.map((i) => i.price_per_day || i.pricePerDay || 0));
          
          return `
            <article class="bg-white rounded-lg border flex flex-col justify-between p-4">
              <div class="bg-blue-50 rounded-md p-3 mb-3">
                <h3 class="font-semibold text-blue-900 text-base">${cat.name}</h3>
                <p class="text-xs text-blue-700 mt-1">${cat.items.length} unit tersedia</p>
              </div>
              <div>
                <div class="flex items-center justify-between text-xs border-t border-slate-100 pt-3">
                  <p class="text-slate-600">Mulai dari</p>
                  <p class="font-semibold text-blue-700">${formatPrice(minPrice)}/hari</p>
                </div>
                <div class="mt-3">
                  <a href="catalog.html#${encodeURIComponent(cat.name)}" class="w-full inline-flex justify-center items-center py-2 px-3 rounded-md bg-blue-100 text-blue-800 text-xs font-semibold hover:bg-blue-200">Lihat ${cat.items.length} produk ${cat.name} →</a>
                </div>
              </div>
            </article>
          `;
        })
        .join('');
    } catch (e) {
      console.error('Gagal render kategori', e);
      // Fallback ke hardcoded data
      const categories = getCategories().slice(0, 6);
      container.innerHTML = categories
        .map((cat) => {
          const minPrice = Math.min(...cat.items.map((i) => i.pricePerDay));
          
          return `
            <article class="bg-white rounded-lg border flex flex-col justify-between p-4">
              <div class="bg-blue-50 rounded-md p-3 mb-3">
                <h3 class="font-semibold text-blue-900 text-base">${cat.name}</h3>
                <p class="text-xs text-blue-700 mt-1">${cat.items.length} unit tersedia</p>
              </div>
              <div>
                <div class="flex items-center justify-between text-xs border-t border-slate-100 pt-3">
                  <p class="text-slate-600">Mulai dari</p>
                  <p class="font-semibold text-blue-700">${formatPrice(minPrice)}/hari</p>
                </div>
                <div class="mt-3">
                  <a href="catalog.html#${encodeURIComponent(cat.name)}" class="w-full inline-flex justify-center items-center py-2 px-3 rounded-md bg-blue-100 text-blue-800 text-xs font-semibold hover:bg-blue-200">Lihat ${cat.items.length} produk ${cat.name} →</a>
                </div>
              </div>
            </article>
          `;
        })
        .join('');
    }
  }

  const STORAGE_KEY = 'snaprent_orders_v1';
  const CART_KEY = 'snaprent_cart_v1';

  async function loadOrders() {
    try {
      const response = await fetch('api/orders.php');
      const orders = await response.json();
      return orders || [];
    } catch (e) {
      console.error('Gagal mengambil orders dari API', e);
      // Fallback ke localStorage jika API gagal
      try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) return [];
        const parsed = JSON.parse(raw);
        if (!Array.isArray(parsed)) return [];
        return parsed;
      } catch (fallbackError) {
        console.error('Gagal membaca order dari localStorage', fallbackError);
        return [];
      }
    }
  }

  function saveOrders(orders) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(orders));
  }

  async function addOrder(order) {
    console.log('addOrder called with:', order);
    try {
      const response = await fetch('api/orders.php?action=create', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(order)
      });
      
      console.log('API response status:', response.status);
      const result = await response.json();
      console.log('API response data:', result);
      
      if (result.success) {
        return result.order_id;
      } else {
        throw new Error(result.error || 'Gagal membuat order');
      }
    } catch (e) {
      console.error('Gagal membuat order via API', e);
      // Fallback ke localStorage jika API gagal
      const orders = await loadOrders();
      orders.push(order);
      saveOrders(orders);
      return order.id;
    }
  }

  async function deleteOrder(orderId) {
    try {
      const response = await fetch('api/orders.php?action=delete_order', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ orderId }),
      });

      const result = await response.json();
      if (result && result.success) {
        return true;
      }
      throw new Error(result?.error || 'Gagal menghapus order');
    } catch (e) {
      console.error('Gagal menghapus order via API', e);
      throw e;
    }
  }

  async function updateOrder(orderIdOrObject, updateFn) {
    try {
      // Handling direct object update (with id property)
      if (typeof orderIdOrObject === 'object' && orderIdOrObject.id) {
        const orderId = orderIdOrObject.id;
        const updated = {
          ...orderIdOrObject,
          updatedAt: orderIdOrObject.updatedAt || new Date().toISOString(),
        };
        
        const response = await fetch('api/orders.php?action=update_status', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            orderId: orderId,
            status: updated.status
          })
        });
        
        const result = await response.json();
        if (result.success) {
          return updated;
        } else {
          throw new Error(result.error || 'Gagal update order');
        }
      }
      
      // Handling orderId + callback function
      const orderId = orderIdOrObject;
      const orders = await loadOrders();
      const index = orders.findIndex((o) => o.id === orderId);
      if (index === -1) return null;
      
      const original = orders[index];
      const updated = {
        ...original,
        ...updateFn(original),
        updatedAt: new Date().toISOString(),
      };
      
      // Update via API
      const response = await fetch('api/orders.php?action=update_status', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          orderId: orderId,
          status: updated.status
        })
      });
      
      const result = await response.json();
      if (result.success) {
        return updated;
      } else {
        throw new Error(result.error || 'Gagal update order');
      }
    } catch (e) {
      console.error('Gagal update order via API', e);
      // Fallback ke localStorage jika API gagal
      const orders = await loadOrders();
      const index = orders.findIndex((o) => o.id === orderIdOrObject.id || o.id === orderIdOrObject);
      if (index === -1) return null;
      
      const original = orders[index];
      const updated = {
        ...original,
        ...(typeof orderIdOrObject === 'object' ? orderIdOrObject : updateFn(original)),
        updatedAt: new Date().toISOString(),
      };
      
      orders[index] = updated;
      saveOrders(orders);
      return updated;
    }
  }
      async function getOrderById(orderId) {
    try {
      const response = await fetch(`api/orders.php?action=get_by_id&id=${orderId}`);
      const order = await response.json();
      return order || null;
    } catch (e) {
      console.error('Gagal mengambil order dari API', e);
      // Fallback ke localStorage jika API gagal
      const orders = await loadOrders();
      return orders.find((o) => o.id === orderId) || null;
    }
  }

  function generateOrderId() {
    const ts = Date.now().toString(36);
    const rand = Math.random().toString(36).substring(2, 8);
    return `ORD-${ts}-${rand}`.toUpperCase();
  }

  function parseDateInput(value) {
    const [y, m, d] = value.split('-').map((v) => parseInt(v, 10));
    if (!y || !m || !d) return null;
    return new Date(y, m - 1, d);
  }

  function diffDays(a, b) {
    const one = new Date(a.getFullYear(), a.getMonth(), a.getDate()).getTime();
    const two = new Date(b.getFullYear(), b.getMonth(), b.getDate()).getTime();
    const diffMs = one - two;
    return Math.round(diffMs / (1000 * 60 * 60 * 24));
  }

  function canEditOrder(order) {
    if (!order) return false;

    const status = order.status;
    const now = new Date();
    const createdAt = order.createdAt || order.created_at;
    const updatedAt = order.updatedAt || order.updated_at;

    const paidStatuses = new Set(['BERHASIL', 'PEMBAYARAN_DIKONFIRMASI']);
    const reference = paidStatuses.has(status) ? (updatedAt || createdAt) : createdAt;
    if (!reference) return false;

    const refDate = new Date(reference);
    const diffMs = now.getTime() - refDate.getTime();
    const hours = diffMs / (1000 * 60 * 60);

    // Editable up to 8 hours after:
    // - created_at for MENUNGGU_PEMBAYARAN
    // - updated_at for paid/confirmed statuses
    return hours <= 8 && (status === 'MENUNGGU_PEMBAYARAN' || paidStatuses.has(status));
  }

  function canCancelOrder(order) {
    return true;
  }

  function isOneDayBefore(date) {
    // Jika parameter adalah order object, ambil rentalDate-nya
    const rentalDate = typeof date === 'object' && date.rentalDate 
      ? new Date(date.rentalDate) 
      : new Date(date);
    
    const now = new Date();
    const daysBefore = diffDays(rentalDate, now);
    return daysBefore === 1;
  }

  function loadCart() {
    try {
      const raw = localStorage.getItem(CART_KEY);
      if (!raw) return [];
      const parsed = JSON.parse(raw);
      if (!Array.isArray(parsed)) return [];
      return parsed;
    } catch (e) {
      console.error('Gagal membaca cart dari localStorage', e);
      return [];
    }
  }

  function saveCart(items) {
    localStorage.setItem(CART_KEY, JSON.stringify(items));
  }

  function addToCart(productId, duration = 1) {
    const cart = loadCart();
    const product = findProductByIdSync(productId);
    
    // Cek jika produk ada dan tersedia
    const isAvailable = product && (product.isAvailable ?? (product.is_available === 1 || product.is_available === '1'));
    if (!product || !isAvailable || product.stock <= 0) {
      console.error('Produk tidak tersedia atau stok habis');
      return cart;
    }
    
    const existing = cart.find((item) => item.productId === productId);
    
    // Cek jumlah di keranjang tidak melebihi stok
    if (existing) {
      if (existing.quantity < product.stock) {
        existing.quantity += 1;
      } else {
        console.error('Stok tidak mencukupi');
        return cart;
      }
    } else {
      cart.push({ 
        productId, 
        quantity: 1, 
        duration: duration, // Durasi per item
        addedAt: new Date().toISOString() 
      });
    }
    
    saveCart(cart);
    return cart;
  }

  function removeFromCart(productId) {
    const cart = loadCart().filter((item) => item.productId !== productId);
    saveCart(cart);
    return cart;
  }

  function updateCartQuantity(productId, quantity) {
    const cart = loadCart();
    const item = cart.find((i) => i.productId === productId);
    const product = findProductByIdSync(productId);
    
    if (item) {
      if (quantity <= 0) {
        return removeFromCart(productId);
      }
      
      // Pastikan jumlah tidak melebihi stok
      if (product && quantity <= product.stock) {
        item.quantity = quantity;
      } else if (product) {
        item.quantity = product.stock;
        console.warn(`Jumlah disesuaikan ke stok maksimum: ${product.stock}`);
      }
      
      saveCart(cart);
    }
    return cart;
  }
  
  function updateCartItemDuration(productId, duration) {
    if (duration < 1) duration = 1;
    
    const cart = loadCart();
    const item = cart.find((i) => i.productId === productId);
    
    if (item) {
      item.duration = duration;
      saveCart(cart);
    }
    
    return cart;
  }

  function clearCart() {
    saveCart([]);
  }

  function getCartTotal() {
    const cart = loadCart();
    return cart.reduce((sum, item) => {
      const product = findProductByIdSync(item.productId);
      const duration = item.duration || 1; // Menggunakan durasi per item atau default 1
      const pricePerDay = product ? (product.pricePerDay ?? product.price_per_day) : 0;
      return sum + (pricePerDay ? pricePerDay * item.quantity * duration : 0);
    }, 0);
  }

  function getProductAvailability(productId) {
    const product = findProductByIdSync(productId);
    if (!product) return { available: false, stock: 0 };
    const isAvailable =
      (product.isAvailable ?? (product.is_available === 1 || product.is_available === '1')) &&
      product.stock > 0;
    return { available: isAvailable, stock: product.stock };
  }
  
  function checkCartAvailability() {
    const cart = loadCart();
    const issues = [];
    
    cart.forEach(item => {
      const product = findProductByIdSync(item.productId);
      if (!product) {
        issues.push({ id: item.productId, message: 'Produk tidak ditemukan' });
      } else if (!(product.isAvailable ?? (product.is_available === 1 || product.is_available === '1'))) {
        issues.push({ id: item.productId, message: 'Produk tidak tersedia untuk disewa' });
      } else if (item.quantity > product.stock) {
        issues.push({ id: item.productId, message: `Stok tidak cukup (tersedia: ${product.stock})` });
      }
    });
    
    return { valid: issues.length === 0, issues };
  }
  
  return {
    getCatalog,
    getCategories,
    findProductById,
    findProductByIdSync,
    formatPrice,
    loadOrders,
    saveOrders,
    addOrder,
    deleteOrder,
    updateOrder,
    updateOrderItems,
    getOrderById,
    generateOrderId,
    parseDateInput,
    diffDays,
    canEditOrder,
    canCancelOrder,
    isOneDayBefore,
    renderFeaturedCategories,
    loadCart,
    saveCart,
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
