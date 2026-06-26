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

  /* ---------------- Gallery lightbox (boat detail) ---------------- */
  const galleryItems = document.querySelectorAll('[data-gallery-item]');
  const lightbox = document.getElementById('lightbox');
  const lightboxImg = document.getElementById('lightbox-img');
  const lightboxClose = document.getElementById('lightbox-close');
  const lightboxPrev = document.getElementById('lightbox-prev');
  const lightboxNext = document.getElementById('lightbox-next');
  let idx = 0;

  const openLB = (i) => {
    if (!lightbox || !galleryItems.length) return;
    idx = (i + galleryItems.length) % galleryItems.length;
    const item = galleryItems[idx];
    lightboxImg.src = item.getAttribute('data-full') || item.querySelector('img').src;
    lightboxImg.alt = item.querySelector('img') ? item.querySelector('img').alt : '';
    lightbox.classList.add('open');
    document.body.style.overflow = 'hidden';
  };
  const closeLB = () => { if (lightbox) { lightbox.classList.remove('open'); document.body.style.overflow = ''; } };

  galleryItems.forEach((it, i) => it.addEventListener('click', () => openLB(i)));
  if (lightboxClose) lightboxClose.addEventListener('click', closeLB);
  if (lightbox) lightbox.addEventListener('click', (e) => { if (e.target === lightbox) closeLB(); });
  if (lightboxPrev) lightboxPrev.addEventListener('click', () => openLB(idx - 1));
  if (lightboxNext) lightboxNext.addEventListener('click', () => openLB(idx + 1));
  document.addEventListener('keydown', (e) => {
    if (!lightbox || !lightbox.classList.contains('open')) return;
    if (e.key === 'Escape') closeLB();
    if (e.key === 'ArrowLeft' && lightboxPrev) lightboxPrev.click();
    if (e.key === 'ArrowRight' && lightboxNext) lightboxNext.click();
  });

  /* ---------------- Boat detail: main image swap ---------------- */
  document.querySelectorAll('[data-thumb]').forEach(thumb => {
    thumb.addEventListener('click', () => {
      const main = document.getElementById('boat-main-img');
      if (main) main.src = thumb.getAttribute('data-thumb');
      document.querySelectorAll('[data-thumb]').forEach(t => t.classList.remove('ring-2', 'ring-brand-gold'));
      thumb.classList.add('ring-2', 'ring-brand-gold');
    });
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

});
