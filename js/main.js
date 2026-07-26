document.addEventListener('DOMContentLoaded', () => {

  /* ---------------- Mobile menu ---------------- */
  const menuToggle = document.getElementById('menu-toggle');
  const mobileMenu = document.getElementById('mobile-menu');
  const iconBurger = document.getElementById('icon-burger');
  const iconClose = document.getElementById('icon-close');

  if (menuToggle && mobileMenu) {
    menuToggle.addEventListener('click', () => {
      const willOpen = mobileMenu.classList.contains('hidden');
      mobileMenu.classList.toggle('hidden');
      iconBurger.classList.toggle('hidden');
      iconClose.classList.toggle('hidden');
      menuToggle.setAttribute('aria-expanded', String(willOpen));
      menuToggle.setAttribute('aria-label', willOpen ? 'Close menu' : 'Open menu');
    });
  }

  /* ---------------- Header + back to top ---------------- */
  const header = document.getElementById('site-header');
  const backToTop = document.getElementById('back-to-top');
  const onScroll = () => {
    const y = window.scrollY;
    if (header) header.classList.toggle('scrolled', y > 30);
    if (backToTop) {
      if (y > 500) backToTop.classList.remove('opacity-0', 'pointer-events-none');
      else backToTop.classList.add('opacity-0', 'pointer-events-none');
    }
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
  if (backToTop) backToTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

  /* ---------------- Scroll reveal ---------------- */
  const targets = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-stagger');
  if ('IntersectionObserver' in window && targets.length) {
    const obs = new IntersectionObserver((entries) => {
      entries.forEach(en => {
        if (en.isIntersecting) { en.target.classList.add('is-visible'); obs.unobserve(en.target); }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -50px 0px' });
    targets.forEach(t => obs.observe(t));
  } else {
    targets.forEach(t => t.classList.add('is-visible'));
  }

  /* ---------------- Animated counters ---------------- */
  const counters = document.querySelectorAll('.counter');
  if ('IntersectionObserver' in window && counters.length) {
    const cObs = new IntersectionObserver((entries) => {
      entries.forEach(en => {
        if (!en.isIntersecting) return;
        const el = en.target;
        const target = parseFloat(el.dataset.target) || 0;
        const dur = 1400, start = performance.now();
        const tick = (now) => {
          const p = Math.min((now - start) / dur, 1);
          const eased = 1 - Math.pow(1 - p, 3);
          el.textContent = Math.floor(eased * target).toLocaleString();
          if (p < 1) requestAnimationFrame(tick);
          else el.textContent = target.toLocaleString();
        };
        requestAnimationFrame(tick);
        cObs.unobserve(el);
      });
    }, { threshold: 0.4 });
    counters.forEach(el => cObs.observe(el));
  }

  /* ---------------- Gallery + lightbox (boat detail) ---------------- */
  const mainImg = document.getElementById('boat-main-img');
  const thumbBtns = [...document.querySelectorAll('[data-thumb]')];
  // The full gallery is every thumbnail (falls back to any [data-gallery-item]).
  const galleryUrls = thumbBtns.length
    ? thumbBtns.map(t => t.getAttribute('data-thumb'))
    : [...document.querySelectorAll('[data-gallery-item]')].map(el => el.getAttribute('data-full') || (el.querySelector('img') && el.querySelector('img').src));
  const lightbox = document.getElementById('lightbox');
  const lightboxImg = document.getElementById('lightbox-img');
  const lightboxClose = document.getElementById('lightbox-close');
  const lightboxPrev = document.getElementById('lightbox-prev');
  const lightboxNext = document.getElementById('lightbox-next');
  const lightboxCount = document.getElementById('lightbox-count');
  let idx = 0;

  // Show gallery image i everywhere (main image + thumb highlight, and the
  // lightbox too when it's open).
  const showImage = (i, inLightbox) => {
    if (!galleryUrls.length) return;
    idx = (i + galleryUrls.length) % galleryUrls.length;
    const url = galleryUrls[idx];
    if (mainImg) mainImg.src = url;
    thumbBtns.forEach((t, j) => {
      const on = j === idx;
      t.classList.toggle('ring-2', on);
      t.classList.toggle('ring-brand-gold', on);
    });
    if (inLightbox && lightboxImg) {
      lightboxImg.src = url;
      lightboxImg.alt = (mainImg && mainImg.alt) || '';
      if (lightboxCount) lightboxCount.textContent = (idx + 1) + ' / ' + galleryUrls.length;
    }
  };

  const openLB = (i) => {
    if (!lightbox || !galleryUrls.length) return;
    showImage(i, true);
    lightbox.classList.add('open');
    document.body.style.overflow = 'hidden';
  };
  const closeLB = () => { if (lightbox) { lightbox.classList.remove('open'); document.body.style.overflow = ''; } };

  // Click a thumbnail -> swap the hero. Click the hero -> open the lightbox.
  thumbBtns.forEach((t, i) => t.addEventListener('click', () => showImage(i, false)));
  const mainWrap = document.querySelector('[data-gallery-item]');
  if (mainWrap) mainWrap.addEventListener('click', () => openLB(idx));

  if (lightboxClose) lightboxClose.addEventListener('click', closeLB);
  if (lightbox) lightbox.addEventListener('click', (e) => { if (e.target === lightbox) closeLB(); });
  if (lightboxPrev) lightboxPrev.addEventListener('click', () => showImage(idx - 1, true));
  if (lightboxNext) lightboxNext.addEventListener('click', () => showImage(idx + 1, true));
  document.addEventListener('keydown', (e) => {
    if (!lightbox || !lightbox.classList.contains('open')) return;
    if (e.key === 'Escape') closeLB();
    if (e.key === 'ArrowLeft') showImage(idx - 1, true);
    if (e.key === 'ArrowRight') showImage(idx + 1, true);
  });

  /* ---------------- Client-side form validation ---------------- */
  document.querySelectorAll('form[data-validate]').forEach(form => {
    form.addEventListener('submit', (e) => {
      let ok = true;
      form.querySelectorAll('[required]').forEach(field => {
        const err = form.querySelector(`[data-error-for="${field.id}"]`);
        const empty = !String(field.value).trim();
        const badEmail = field.type === 'email' && field.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value);
        if (empty || badEmail) {
          ok = false;
          field.classList.add('border-red-500', 'ring-1', 'ring-red-500');
          if (err) err.classList.remove('hidden');
        } else {
          field.classList.remove('border-red-500', 'ring-1', 'ring-red-500');
          if (err) err.classList.add('hidden');
        }
      });
      if (!ok) { e.preventDefault(); form.querySelector('.border-red-500')?.focus(); }
    });
    form.querySelectorAll('[required]').forEach(field => {
      field.addEventListener('input', () => {
        field.classList.remove('border-red-500', 'ring-1', 'ring-red-500');
        const err = form.querySelector(`[data-error-for="${field.id}"]`);
        if (err) err.classList.add('hidden');
      });
    });
  });

  /* ---------------- Date inputs: min today ---------------- */
  const today = new Date().toISOString().split('T')[0];
  document.querySelectorAll('input[type="date"]').forEach(d => {
    if (!d.getAttribute('min')) d.setAttribute('min', today);
  });

  /* ---------------- Custom cursor: rotating ship's wheel (fine pointers, motion-ok) ---------------- */
  (function () {
    const fine = window.matchMedia && matchMedia('(pointer: fine)').matches;
    const reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!fine || reduce) return; // touch devices & reduced-motion keep the native cursor

    const WHEEL =
      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" aria-hidden="true">' +
      '<circle cx="12" cy="12" r="7"/><circle cx="12" cy="12" r="2.4"/>' +
      '<line x1="12" y1="2" x2="12" y2="22"/><line x1="2" y1="12" x2="22" y2="12"/>' +
      '<line x1="4.9" y1="4.9" x2="19.1" y2="19.1"/><line x1="19.1" y1="4.9" x2="4.9" y2="19.1"/>' +
      '</svg>';

    const dot = document.createElement('div'); dot.id = 'cursor-dot';
    const wheel = document.createElement('div'); wheel.id = 'cursor-wheel'; wheel.innerHTML = WHEEL;
    document.body.appendChild(dot);
    document.body.appendChild(wheel);
    document.documentElement.classList.add('has-custom-cursor');

    let mx = innerWidth / 2, my = innerHeight / 2, wx = mx, wy = my, shown = false;
    const interactiveSel = 'a, button, input, textarea, select, label, summary, [role="button"], .cursor-pointer';

    addEventListener('mousemove', (e) => {
      mx = e.clientX; my = e.clientY;
      dot.style.transform = `translate(${mx}px, ${my}px) translate(-50%, -50%)`;
      if (!shown) { shown = true; dot.style.opacity = '1'; wheel.style.opacity = '1'; }
      const hit = e.target.closest && e.target.closest(interactiveSel);
      wheel.classList.toggle('is-hover', !!hit);
    }, { passive: true });

    (function loop() {
      wx += (mx - wx) * 0.2; wy += (my - wy) * 0.2;
      wheel.style.transform = `translate(${wx}px, ${wy}px) translate(-50%, -50%)`;
      requestAnimationFrame(loop);
    })();

    document.addEventListener('mouseleave', () => { dot.style.opacity = '0'; wheel.style.opacity = '0'; shown = false; });
    document.addEventListener('mouseenter', () => { dot.style.opacity = '1'; wheel.style.opacity = '1'; shown = true; });
    addEventListener('mousedown', () => wheel.classList.add('is-down'));
    addEventListener('mouseup', () => wheel.classList.remove('is-down'));
  })();

});
