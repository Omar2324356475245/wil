// ============================
// DATA STORE
// ============================
const products = [
  { id: 1, name: "Classic White Sneakers", price: 89.99, oldPrice: 120, category: "shoes", emoji: "👟", badge: "Best Seller" },
  { id: 2, name: "Leather Crossbody Bag", price: 149.99, oldPrice: null, category: "bags", emoji: "👜", badge: "New" },
  { id: 3, name: "Aviator Sunglasses", price: 59.99, oldPrice: 80, category: "accessories", emoji: "🕶️", badge: "Sale" },
  { id: 4, name: "Cozy Knit Sweater", price: 79.99, oldPrice: null, category: "clothing", emoji: "🧶", badge: "Trending" },
  { id: 5, name: "Sports Running Shoes", price: 109.99, oldPrice: 140, category: "shoes", emoji: "🏃", badge: "Sale" },
  { id: 6, name: "Canvas Tote Bag", price: 39.99, oldPrice: null, category: "bags", emoji: "🛍️", badge: "Popular" },
  { id: 7, name: "Gold Chain Necklace", price: 69.99, oldPrice: 90, category: "accessories", emoji: "📿", badge: "New" },
  { id: 8, name: "Slim Fit Denim Jacket", price: 119.99, oldPrice: null, category: "clothing", emoji: "🧥", badge: "Featured" },
];

// ============================
// CART STATE (localStorage)
// ============================
function getCart() {
  return JSON.parse(localStorage.getItem('shopCart') || '[]');
}

function saveCart(cart) {
  localStorage.setItem('shopCart', JSON.stringify(cart));
  updateCartUI();
}

function addToCart(productId, qty = 1) {
  const cart = getCart();
  const product = products.find(p => p.id === productId);
  if (!product) return;

  const existing = cart.find(i => i.id === productId);
  if (existing) {
    existing.qty += qty;
  } else {
    cart.push({ ...product, qty });
  }
  saveCart(cart);
  showToast(`✓ ${product.name} added to cart!`);
}

function removeFromCart(productId) {
  let cart = getCart().filter(i => i.id !== productId);
  saveCart(cart);
}

function updateQty(productId, delta) {
  const cart = getCart();
  const item = cart.find(i => i.id === productId);
  if (!item) return;
  item.qty = Math.max(1, item.qty + delta);
  saveCart(cart);
}

function getCartTotal() {
  return getCart().reduce((sum, i) => sum + i.price * i.qty, 0);
}

function getCartCount() {
  return getCart().reduce((sum, i) => sum + i.qty, 0);
}

// ============================
// UI UPDATES
// ============================
function updateCartUI() {
  const count = getCartCount();
  document.querySelectorAll('#cart-count').forEach(el => {
    el.textContent = count;
  });
  renderCartItems();
}

function renderCartItems() {
  const container = document.getElementById('cart-items');
  if (!container) return;

  const cart = getCart();

  if (cart.length === 0) {
    container.innerHTML = `
      <div class="empty-cart">
        <div style="font-size:4rem;margin-bottom:1rem">🛒</div>
        <p>Your cart is empty</p>
        <a href="products.html" class="btn-primary" style="display:inline-block;margin-top:1rem">Shop Now</a>
      </div>`;
    document.getElementById('cart-total-price').textContent = '$0.00';
    return;
  }

  container.innerHTML = cart.map(item => `
    <div class="cart-item">
      <div class="cart-item-img">${item.emoji}</div>
      <div class="cart-item-info">
        <div class="cart-item-name">${item.name}</div>
        <div class="cart-item-price">$${(item.price * item.qty).toFixed(2)}</div>
        <div class="qty-control">
          <button class="qty-btn" onclick="updateQty(${item.id}, -1)">−</button>
          <span>${item.qty}</span>
          <button class="qty-btn" onclick="updateQty(${item.id}, 1)">+</button>
        </div>
      </div>
      <button class="remove-item" onclick="removeFromCart(${item.id})" title="Remove">✕</button>
    </div>
  `).join('');

  const total = document.getElementById('cart-total-price');
  if (total) total.textContent = `$${getCartTotal().toFixed(2)}`;
}

// ============================
// PRODUCT RENDERING
// ============================
function renderProducts(list, container) {
  if (!container) return;
  if (list.length === 0) {
    container.innerHTML = '<p style="text-align:center;color:var(--gray);grid-column:1/-1;padding:3rem">No products found.</p>';
    return;
  }
  container.innerHTML = list.map(p => `
    <div class="product-card" onclick="location.href='product-detail.html?id=${p.id}'">
      <div class="product-img" style="cursor:pointer">${p.emoji}</div>
      <div class="product-info">
        <span class="product-badge">${p.badge}</span>
        <div class="product-name">${p.name}</div>
        <div class="product-price">
          $${p.price.toFixed(2)}
          ${p.oldPrice ? `<span class="old-price">$${p.oldPrice.toFixed(2)}</span>` : ''}
        </div>
        <button class="add-to-cart" onclick="event.stopPropagation(); addToCart(${p.id})">
          🛒 Add to Cart
        </button>
      </div>
    </div>
  `).join('');
}

// ============================
// CART SIDEBAR TOGGLE
// ============================
function openCart() {
  document.getElementById('cart-overlay')?.classList.add('active');
  document.getElementById('cart-sidebar')?.classList.add('active');
}

function closeCart() {
  document.getElementById('cart-overlay')?.classList.remove('active');
  document.getElementById('cart-sidebar')?.classList.remove('active');
}

// ============================
// TOAST NOTIFICATION
// ============================
function showToast(msg) {
  const toast = document.getElementById('toast');
  if (!toast) return;
  toast.innerHTML = `<span class="check">✓</span>${msg}`;
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 3000);
}

// ============================
// MOBILE MENU
// ============================
function toggleMenu() {
  document.getElementById('nav-links')?.classList.toggle('open');
}

// ============================
// INIT ON PAGE LOAD
// ============================
document.addEventListener('DOMContentLoaded', () => {
  updateCartUI();

  // Overlay click closes cart
  document.getElementById('cart-overlay')?.addEventListener('click', closeCart);

  // Products page filter
  const filterBtns = document.querySelectorAll('.filter-btn');
  const grid = document.getElementById('products-grid');

  if (grid) {
    let active = 'all';
    renderProducts(products, grid);

    filterBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        filterBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        active = btn.dataset.cat;
        const filtered = active === 'all' ? products : products.filter(p => p.category === active);
        renderProducts(filtered, grid);
      });
    });

    // Search
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
      searchInput.addEventListener('input', () => {
        const q = searchInput.value.toLowerCase();
        const base = active === 'all' ? products : products.filter(p => p.category === active);
        const filtered = base.filter(p => p.name.toLowerCase().includes(q));
        renderProducts(filtered, grid);
      });
    }
  }

  // Home page featured products
  const featuredGrid = document.getElementById('featured-grid');
  if (featuredGrid) {
    renderProducts(products.slice(0, 4), featuredGrid);
  }

  // Product detail page
  const detailContainer = document.getElementById('product-detail');
  if (detailContainer) {
    const params = new URLSearchParams(location.search);
    const id = parseInt(params.get('id'));
    const product = products.find(p => p.id === id) || products[0];

    document.getElementById('detail-emoji').textContent = product.emoji;
    document.getElementById('detail-name').textContent = product.name;
    document.getElementById('detail-price').textContent = `$${product.price.toFixed(2)}`;
    document.getElementById('detail-badge').textContent = product.badge;
    document.getElementById('detail-add-btn').onclick = () => {
      const qty = parseInt(document.getElementById('detail-qty').value) || 1;
      addToCart(product.id, qty);
    };
  }

  // Checkout page
  const checkoutSummary = document.getElementById('checkout-summary');
  if (checkoutSummary) {
    const cart = getCart();
    if (cart.length === 0) {
      checkoutSummary.innerHTML = '<p style="color:var(--gray)">Your cart is empty. <a href="products.html">Shop now</a></p>';
    } else {
      checkoutSummary.innerHTML = cart.map(i =>
        `<div class="summary-item"><span>${i.name} × ${i.qty}</span><span>$${(i.price*i.qty).toFixed(2)}</span></div>`
      ).join('') +
        `<div class="summary-total"><span>Total</span><span>$${getCartTotal().toFixed(2)}</span></div>`;
    }

    // Checkout form submit
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
      checkoutForm.addEventListener('submit', e => {
        e.preventDefault();
        saveCart([]);
        document.getElementById('checkout-layout').innerHTML = `
          <div style="text-align:center;padding:4rem;grid-column:1/-1">
            <div style="font-size:5rem;margin-bottom:1.5rem">🎉</div>
            <h2 style="font-family:'Playfair Display',serif;font-size:2.5rem;margin-bottom:1rem">Order Placed!</h2>
            <p style="color:var(--gray);font-size:1.1rem;margin-bottom:2rem">Thank you for your order. We'll send a confirmation shortly.</p>
            <a href="index.html" class="btn-primary">Back to Home</a>
          </div>`;
      });
    }
  }
});
