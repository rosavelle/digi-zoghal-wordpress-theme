(() => {
  const body = document.body;
  const cartToggle = document.querySelector('[data-cart-toggle]');
  const cartClose = document.querySelectorAll('[data-cart-close]');
  const menuToggle = document.querySelector('[data-menu-toggle]');
  const cartDrawer = document.querySelector('.drawer');
  const slideshow = document.querySelector('[data-hero-slideshow]');

  if (slideshow) {
    const slides = [...slideshow.querySelectorAll('[data-hero-slide]')];
    const dots = [...slideshow.querySelectorAll('[data-hero-dot]')];
    const controls = slideshow.querySelector('[data-hero-controls]');
    let active = 0;
    let rotationTimer;

    const selectSlide = (index) => {
      active = (index + slides.length) % slides.length;
      slides.forEach((slide, i) => {
        slide.hidden = i !== active;
        dots[i].setAttribute('aria-pressed', String(i === active));
      });
    };
    const restartRotation = () => {
      window.clearInterval(rotationTimer);
      rotationTimer = window.setInterval(() => {
        if (!document.hidden && !slides[active].contains(document.activeElement)) {
          selectSlide(active + 1);
        }
      }, 15000);
    };

    dots.forEach((dot, i) => dot.addEventListener('click', () => {
      selectSlide(i);
      restartRotation();
    }));
    controls.hidden = false;
    restartRotation();
  }

  const normalizeQuantity = (input, requestedValue) => {
    const min = Number(input.min) || 1;
    const max = input.max ? Number(input.max) : Number.POSITIVE_INFINITY;
    const step = Number(input.step) || 1;
    const numericValue = Number.isFinite(requestedValue) ? requestedValue : min;
    return Math.min(max, Math.max(min, Math.round(numericValue / step) * step));
  };

  const syncQuantity = (input, requestedValue, updateInput = true) => {
    const value = normalizeQuantity(input, requestedValue);
    const control = input.closest('[data-dz-buy-control]');
    const addButton = control?.querySelector('.add_to_cart_button');

    if (updateInput) input.value = String(value);
    if (!addButton) return;

    addButton.dataset.quantity = String(value);
    const url = new URL(addButton.href, window.location.href);
    url.searchParams.set('quantity', String(value));
    addButton.href = url.toString();
  };

  document.querySelectorAll('[data-dz-qty-input]').forEach((input) => {
    syncQuantity(input, Number(input.value));
  });

  document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-dz-qty]');
    if (!button) return;

    const input = button.closest('[data-dz-buy-control]')?.querySelector('[data-dz-qty-input]');
    if (!input) return;

    const direction = button.dataset.dzQty === 'increase' ? 1 : -1;
    const step = Number(input.step) || 1;
    syncQuantity(input, (Number(input.value) || 1) + direction * step);
  });

  document.addEventListener('input', (event) => {
    if (event.target.matches('[data-dz-qty-input]')) {
      syncQuantity(event.target, Number(event.target.value), false);
    }
  });

  // WooCommerce delegates its AJAX click handler to body.
  document.addEventListener('click', (event) => {
    const addButton = event.target.closest('[data-dz-buy-control] .add_to_cart_button');
    const input = addButton?.closest('[data-dz-buy-control]')?.querySelector('[data-dz-qty-input]');
    if (input) syncQuantity(input, Number(input.value));
  }, true);

  document.addEventListener('change', (event) => {
    if (event.target.matches('[data-dz-qty-input]')) {
      syncQuantity(event.target, Number(event.target.value));
    }
  });

  const closeCart = () => {
    body.classList.remove('dz-cart-open');
    cartToggle?.setAttribute('aria-expanded', 'false');
    cartDrawer?.setAttribute('aria-hidden', 'true');
  };

  cartToggle?.addEventListener('click', () => {
    const isOpen = body.classList.toggle('dz-cart-open');
    cartToggle.setAttribute('aria-expanded', String(isOpen));
    cartDrawer?.setAttribute('aria-hidden', String(!isOpen));
    if (isOpen && body.matches('.woocommerce-cart, .woocommerce-checkout') && window.jQuery) {
      window.jQuery(body).trigger('wc_fragment_refresh');
    }
  });

  cartClose.forEach((button) => button.addEventListener('click', closeCart));

  const submenus = [];
  document.querySelectorAll('.nav .primary-menu > .menu-item-has-children').forEach((item, index) => {
    const link = item.querySelector(':scope > a');
    const submenu = item.querySelector(':scope > .sub-menu');
    if (!link || !submenu) return;

    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'dz-submenu-toggle';
    button.textContent = link.textContent;
    button.setAttribute('aria-expanded', 'false');
    submenu.id = submenu.id || 'dz-primary-submenu-' + index;
    button.setAttribute('aria-controls', submenu.id);

    const overview = document.createElement('li');
    overview.className = 'dz-submenu-overview';
    const overviewLink = document.createElement('a');
    overviewLink.href = link.href;
    overviewLink.textContent = 'مشاهدهٔ صفحهٔ ' + link.textContent.trim();
    overview.append(overviewLink);
    submenu.prepend(overview);
    link.after(button);
    item.classList.add('dz-submenu-ready');

    const setExpanded = (expanded) => {
      button.setAttribute('aria-expanded', String(expanded));
      item.classList.toggle('dz-submenu-open', expanded);
    };
    button.addEventListener('click', () => setExpanded(button.getAttribute('aria-expanded') !== 'true'));
    submenus.push(setExpanded);
  });

  const closeMenu = () => {
    body.classList.remove('dz-menu-open');
    menuToggle?.setAttribute('aria-expanded', 'false');
    submenus.forEach((setExpanded) => setExpanded(false));
  };

  menuToggle?.addEventListener('click', () => {
    const isOpen = body.classList.toggle('dz-menu-open');
    menuToggle.setAttribute('aria-expanded', String(isOpen));
    if (!isOpen) closeMenu();
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      closeCart();
      closeMenu();
    }
  });

  if (window.jQuery) {
    window.jQuery(document.body).on('added_to_cart', () => {
      body.classList.add('dz-cart-open');
      cartToggle?.setAttribute('aria-expanded', 'true');
      cartDrawer?.setAttribute('aria-hidden', 'false');
    });
  }
})();

