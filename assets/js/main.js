document.addEventListener('DOMContentLoaded', function () {
  const baseUrl = document.body.dataset.baseUrl || '';

  // ==========================================================================
  // 1. TOAST NOTIFICATION SYSTEM
  // ==========================================================================
  const toastContainer = document.getElementById('toast-container');

  function showToast(message) {
    if (!toastContainer) return;
    const toast = document.createElement('div');
    toast.className = 'toast-notification';
    toast.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#A8E910" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <polyline points="20 6 9 17 4 12"></polyline>
      </svg>
      <span>${message}</span>
    `;
    toastContainer.appendChild(toast);

    requestAnimationFrame(() => {
      toast.classList.add('show');
    });

    setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => toast.remove(), 260);
    }, 2800);
  }

  // ==========================================================================
  // 2. MOBILE NAVIGATION & USER MENU DROPDOWN
  // ==========================================================================
  const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
  const mainNav = document.getElementById('main-nav');

  if (mobileMenuToggle && mainNav) {
    mobileMenuToggle.addEventListener('click', function (e) {
      e.stopPropagation();
      const isOpen = mainNav.classList.toggle('is-open');
      mobileMenuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
  }

  const userMenu = document.getElementById('user-menu');
  const userMenuTrigger = document.getElementById('user-menu-trigger');
  const userDropdown = document.getElementById('user-dropdown');

  if (userMenu && userMenuTrigger && userDropdown) {
    userMenuTrigger.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      const isShown = userDropdown.classList.toggle('show');
      userMenu.classList.toggle('is-open', isShown);
      userMenuTrigger.setAttribute('aria-expanded', isShown ? 'true' : 'false');
    });
  }

  document.addEventListener('click', function (e) {
    if (userDropdown && userDropdown.classList.contains('show')) {
      if (!userDropdown.contains(e.target) && !userMenuTrigger.contains(e.target)) {
        userDropdown.classList.remove('show');
        userMenu?.classList.remove('is-open');
        userMenuTrigger?.setAttribute('aria-expanded', 'false');
      }
    }
    if (mainNav && mainNav.classList.contains('is-open')) {
      if (!mainNav.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
        mainNav.classList.remove('is-open');
        mobileMenuToggle?.setAttribute('aria-expanded', 'false');
      }
    }
  });

  // ==========================================================================
  // 3. GLOBAL CART DRAWER & SERVER SESSION SYNC (cart_manager.php)
  // ==========================================================================
  const cartTriggers = document.querySelectorAll('.cart-link, #header-cart-trigger');
  const sidebar = document.getElementById('global-cart-sidebar');
  const overlay = document.getElementById('sidebar-overlay');
  const closeCartBtn = document.getElementById('cart-close-btn');
  const cartItemsContainer = document.getElementById('global-cart-items');
  const cartEmptyEl = document.getElementById('global-cart-empty');
  const cartTotalEl = document.getElementById('global-cart-total');
  const cartBadgeEl = document.getElementById('header-cart-badge');
  const cartDrawerCountEl = document.getElementById('cart-drawer-count');
  const checkoutBtn = document.getElementById('global-checkout-btn');

  let currentCart = [];

  function openCart(e) {
    if (e) e.preventDefault();
    sidebar?.classList.add('is-open');
    overlay?.classList.add('is-open');
  }

  function closeCart() {
    sidebar?.classList.remove('is-open');
    overlay?.classList.remove('is-open');
  }

  cartTriggers.forEach(btn => btn.addEventListener('click', openCart));
  closeCartBtn?.addEventListener('click', closeCart);
  overlay?.addEventListener('click', closeCart);

  function renderCart(items) {
    currentCart = Array.isArray(items) ? items : [];
    const count = currentCart.length;

    if (cartBadgeEl) {
      cartBadgeEl.textContent = count;
      cartBadgeEl.classList.toggle('is-empty', count === 0);
    }
    if (cartDrawerCountEl) {
      cartDrawerCountEl.textContent = count;
    }

    if (!cartItemsContainer || !cartEmptyEl || !cartTotalEl) return;

    cartItemsContainer.innerHTML = '';
    let total = 0;

    if (count === 0) {
      cartEmptyEl.classList.add('is-visible');
      if (checkoutBtn) checkoutBtn.disabled = true;
    } else {
      cartEmptyEl.classList.remove('is-visible');
      if (checkoutBtn) checkoutBtn.disabled = false;

      currentCart.forEach(slot => {
        const priceNum = parseInt(slot.price, 10) || 0;
        total += priceNum;
        const itemEl = document.createElement('div');
        itemEl.className = 'cart-item';
        itemEl.dataset.id = slot.id;
        itemEl.innerHTML = `
          <div class="cart-item-info">
            ${slot.venue ? `<span class="cart-item-venue">${slot.venue}</span>` : ''}
            <div class="cart-item-title">${slot.court || 'Lapangan'}</div>
            <div class="cart-item-meta">${slot.date || ''}</div>
            <div><span class="cart-item-time">${slot.time || ''}</span></div>
            <div class="cart-item-price">Rp${priceNum.toLocaleString('id-ID')}</div>
          </div>
          <button type="button" class="cart-item-remove-btn" data-id="${slot.id}" aria-label="Hapus jadwal ${slot.court} ${slot.time}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
            </svg>
          </button>
        `;
        cartItemsContainer.appendChild(itemEl);
      });
    }

    cartTotalEl.textContent = `Rp${total.toLocaleString('id-ID')}`;
    document.dispatchEvent(new CustomEvent('sportiva:cart-updated', { detail: { items: currentCart } }));
  }

  async function fetchCart() {
    try {
      const res = await fetch(`${baseUrl}actions/cart_manager.php?action=get`);
      if (res.ok) {
        const data = await res.json();
        renderCart(data);
      }
    } catch (err) {
      // Fallback gracefully if offline
    }
  }

  async function addCartItem(slotData, shouldOpenDrawer = true) {
    try {
      const res = await fetch(`${baseUrl}actions/cart_manager.php?action=add`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(slotData)
      });
      if (res.ok) {
        const data = await res.json();
        renderCart(data);
        showToast('Jadwal berhasil ditambahkan ke keranjang');
        if (shouldOpenDrawer) openCart();
      }
    } catch (err) {
      showToast('Gagal menambahkan jadwal');
    }
  }

  async function removeCartItem(slotId) {
    try {
      const res = await fetch(`${baseUrl}actions/cart_manager.php?action=remove&id=${encodeURIComponent(slotId)}`);
      if (res.ok) {
        const data = await res.json();
        renderCart(data);
        showToast('Jadwal dihapus dari keranjang');
      }
    } catch (err) {
      showToast('Gagal menghapus jadwal');
    }
  }

  cartItemsContainer?.addEventListener('click', function (e) {
    const removeBtn = e.target.closest('.cart-item-remove-btn');
    if (!removeBtn) return;
    const id = removeBtn.dataset.id;
    if (id) removeCartItem(id);
  });

  checkoutBtn?.addEventListener('click', function () {
    if (currentCart.length === 0) return;
    showToast(`Reservasi ${currentCart.length} sesi jadwal sedang diproses`);
  });

  fetchCart();

  // Expose helper API for page scripts
  window.SportivaUI = {
    showToast,
    openCart,
    closeCart,
    addCartItem,
    removeCartItem,
    getCart: () => currentCart
  };

  // ==========================================================================
  // 4. AUTH MODAL (LOGIN / REGISTER) + URL ERROR FEEDBACK
  // ==========================================================================
  const modalContainer = document.getElementById('auth-modal-container');
  const loginBtnHeader = document.getElementById('login-btn-header');
  const registerBtnHeader = document.getElementById('register-btn-header');
  const closeModalBtn = document.getElementById('close-modal-btn');
  const registerForm = document.getElementById('register-form');
  const loginForm = document.getElementById('login-form');
  const switchToLoginLink = document.getElementById('switch-to-login');
  const switchToRegisterLink = document.getElementById('switch-to-register');

  function openAuthModal() {
    modalContainer?.classList.add('is-visible');
  }

  function closeAuthModal() {
    modalContainer?.classList.remove('is-visible');
  }

  function showLoginForm() {
    if (!loginForm || !registerForm) return;
    loginForm.classList.remove('hidden', 'from-right');
    registerForm.classList.add('hidden', 'from-right');
  }

  function showRegisterForm() {
    if (!loginForm || !registerForm) return;
    registerForm.classList.remove('hidden', 'from-right');
    loginForm.classList.add('hidden');
  }

  if (modalContainer) {
    loginBtnHeader?.addEventListener('click', (e) => {
      e.preventDefault();
      showLoginForm();
      openAuthModal();
    });
    registerBtnHeader?.addEventListener('click', (e) => {
      e.preventDefault();
      showRegisterForm();
      openAuthModal();
    });
    closeModalBtn?.addEventListener('click', closeAuthModal);
    modalContainer.addEventListener('click', (e) => {
      if (e.target === modalContainer) closeAuthModal();
    });
    switchToLoginLink?.addEventListener('click', showLoginForm);
    switchToRegisterLink?.addEventListener('click', showRegisterForm);
  }

  // Handle Escape key for all global overlays
  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      closeCart();
      closeAuthModal();
      if (userDropdown?.classList.contains('show')) {
        userDropdown.classList.remove('show');
        userMenu?.classList.remove('is-open');
      }
    }
  });

  // Check URL parameters for auth error feedback
  const urlParams = new URLSearchParams(window.location.search);
  const errorParam = urlParams.get('error');
  if (errorParam) {
    const errorMessages = {
      login_failed: 'Email atau password tidak sesuai. Silakan coba lagi.',
      email_taken: 'Email sudah terdaftar. Silakan masuk menggunakan akun Anda.',
      invalid_register_data: 'Mohon lengkapi seluruh kolom pendaftaran dengan benar.',
      empty_login_fields: 'Email dan password wajib diisi.'
    };
    showToast(errorMessages[errorParam] || 'Terjadi kesalahan autentikasi.');
    if (modalContainer) {
      if (errorParam === 'invalid_register_data') {
        showRegisterForm();
      } else {
        showLoginForm();
      }
      openAuthModal();
    }
  }

  // ==========================================================================
  // 5. HERO SLIDER (HOME PAGE)
  // ==========================================================================
  const heroSection = document.querySelector('.hero-section');
  if (heroSection) {
    const slides = heroSection.querySelectorAll('.hero-slide');
    const dots = heroSection.querySelectorAll('.slider-pagination .dot');
    const infoTitle = document.getElementById('hero-sport-title');
    const infoTag = document.getElementById('hero-sport-tag');
    const infoDesc = document.getElementById('hero-sport-desc');
    const infoCard = document.querySelector('.info-box');
    const progressBar = document.getElementById('hero-progress-bar');

    const slidesData = [
      {
        tag: 'Sepak Bola & Futsal',
        title: 'Football',
        description: 'Booking lapangan rumput sintetis maupun vinyl indoor dengan jadwal real-time untuk latihan tim dan turnamen.'
      },
      {
        tag: 'Rakets & Indoor Hall',
        title: 'Badminton',
        description: 'Temukan partner sparring, gabung sesi mabar mingguan, dan pesan court karpet standar BWF favoritmu.'
      },
      {
        tag: 'Full Court & 3x3 Arena',
        title: 'Basketball',
        description: 'Arena basket kayu dan outdoor berkualitas tinggi untuk game komunitas maupun latihan rutin.'
      },
      {
        tag: 'Ping Pong & Reflex',
        title: 'Table Tennis',
        description: 'Meja standar kompetisi dengan pencahayaan optimal untuk pertandingan cepat bersama rekan kerja.'
      },
      {
        tag: 'Indoor & Sand Court',
        title: 'Volleyball',
        description: 'Nikmati keseruan spike bersama tim di lapangan voli indoor berstandar turnamen nasional.'
      }
    ];

    let currentSlideIndex = 0;
    let slideInterval = null;
    const SLIDE_DURATION = 5500;

    function restartProgress() {
      if (!progressBar) return;
      progressBar.style.transition = 'none';
      progressBar.style.transform = 'scaleX(0)';
      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          progressBar.style.transition = `transform ${SLIDE_DURATION}ms linear`;
          progressBar.style.transform = 'scaleX(1)';
        });
      });
    }

    function updateSlide(index) {
      if (!slides.length) return;
      slides.forEach((slide, i) => {
        slide.classList.toggle('is-active', i === index);
      });
      dots.forEach((dot, i) => {
        dot.classList.toggle('active', i === index);
        dot.setAttribute('aria-selected', i === index ? 'true' : 'false');
      });

      if (infoCard && infoTitle && infoDesc) {
        infoCard.classList.add('is-switching');
        setTimeout(() => {
          if (infoTag) infoTag.textContent = slidesData[index].tag;
          infoTitle.textContent = slidesData[index].title;
          infoDesc.textContent = slidesData[index].description;
          infoCard.classList.remove('is-switching');
        }, 160);
      }

      currentSlideIndex = index;
      restartProgress();
    }

    function nextSlide() {
      updateSlide((currentSlideIndex + 1) % slidesData.length);
    }

    function prevSlide() {
      updateSlide((currentSlideIndex - 1 + slidesData.length) % slidesData.length);
    }

    function resetInterval() {
      clearInterval(slideInterval);
      slideInterval = setInterval(nextSlide, SLIDE_DURATION);
    }

    document.querySelector('.next-arrow')?.addEventListener('click', () => {
      nextSlide();
      resetInterval();
    });

    document.querySelector('.prev-arrow')?.addEventListener('click', () => {
      prevSlide();
      resetInterval();
    });

    dots.forEach((dot, idx) => {
      dot.addEventListener('click', () => {
        if (idx === currentSlideIndex) return;
        updateSlide(idx);
        resetInterval();
      });
    });

    restartProgress();
    slideInterval = setInterval(nextSlide, SLIDE_DURATION);
  }
});
