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
      // threshold must stay 0: a ratio threshold is unreachable once an element
      // is taller than viewport/threshold (a 1-column city grid of 28 boats is
      // ~12000px), which left every card stuck at opacity 0 but still clickable.
      // The negative bottom margin keeps the "reveal just after it enters" feel.
    }, { threshold: 0, rootMargin: '0px 0px -50px 0px' });
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

  /* ---------------- Guest reels: strip + player ---------------- */
  const reelTrack = document.querySelector('[data-reel-track]');
  const reelCards = [...document.querySelectorAll('[data-reel-open]')];
  const reelBox = document.getElementById('reel-lightbox');
  const reelPlayer = document.getElementById('reel-player');

  // Arrows nudge the strip by roughly one card.
  document.querySelectorAll('[data-reel-scroll]').forEach(btn => {
    btn.addEventListener('click', () => {
      if (!reelTrack) return;
      const card = reelTrack.querySelector('li');
      const step = (card ? card.offsetWidth + 20 : 240) * 2;
      reelTrack.scrollBy({ left: step * Number(btn.getAttribute('data-reel-scroll')), behavior: 'smooth' });
    });
  });

  if (reelBox && reelPlayer && reelCards.length) {
    const reelCaption = document.getElementById('reel-caption');
    const reelName = document.getElementById('reel-name');
    const reelMeta = document.getElementById('reel-meta');
    let reelIdx = 0;

    const setText = (el, value) => {
      if (!el) return;
      el.textContent = value || '';
      el.classList.toggle('hidden', !value);
    };

    const showReel = (i) => {
      reelIdx = (i + reelCards.length) % reelCards.length;
      const card = reelCards[reelIdx];
      reelPlayer.src = card.getAttribute('data-reel-src');
      reelPlayer.load();
      setText(reelCaption, card.getAttribute('data-reel-caption'));
      setText(reelName, card.getAttribute('data-reel-name'));
      setText(reelMeta, card.getAttribute('data-reel-meta'));
      const p = reelPlayer.play();
      if (p && p.catch) p.catch(() => {});
    };

    const closeReel = () => {
      reelBox.classList.remove('open');
      reelPlayer.pause();
      reelPlayer.removeAttribute('src');
      reelPlayer.load();
      document.body.style.overflow = '';
    };

    reelCards.forEach((card, i) => card.addEventListener('click', () => {
      showReel(i);
      reelBox.classList.add('open');
      document.body.style.overflow = 'hidden';
    }));

    document.getElementById('reel-close')?.addEventListener('click', closeReel);
    document.getElementById('reel-prev')?.addEventListener('click', () => showReel(reelIdx - 1));
    document.getElementById('reel-next')?.addEventListener('click', () => showReel(reelIdx + 1));
    reelBox.addEventListener('click', (e) => { if (e.target === reelBox) closeReel(); });
    document.addEventListener('keydown', (e) => {
      if (!reelBox.classList.contains('open')) return;
      if (e.key === 'Escape') closeReel();
      if (e.key === 'ArrowLeft') showReel(reelIdx - 1);
      if (e.key === 'ArrowRight') showReel(reelIdx + 1);
    });
  }

  /* ---------------- Guest reels: upload form ---------------- */
  const reelForm = document.querySelector('[data-reel-form]');
  if (reelForm) {
    const fileInput = reelForm.querySelector('input[type="file"]');
    const dropZone = reelForm.querySelector('[data-reel-drop]');
    const fileLabel = reelForm.querySelector('[data-reel-label]');
    const errorEl = reelForm.querySelector('[data-reel-error]');
    const previewWrap = reelForm.querySelector('[data-reel-preview]');
    const previewVideo = previewWrap && previewWrap.querySelector('video');
    const posterField = reelForm.querySelector('[data-reel-poster]');
    const submitBtn = reelForm.querySelector('[data-reel-submit]');
    const submitLabel = reelForm.querySelector('[data-reel-submit-label]');
    const maxBytes = Number(reelForm.querySelector('input[name="MAX_FILE_SIZE"]')?.value) || 0;

    const mb = (bytes) => (bytes / 1048576).toFixed(1).replace(/\.0$/, '') + ' MB';
    const showError = (msg) => {
      if (!errorEl) return;
      errorEl.textContent = msg || '';
      errorEl.classList.toggle('hidden', !msg);
    };

    // Grab a still from the chosen video so the homepage grid can show an image
    // instead of loading every reel at once. Best effort — the server treats
    // the poster as optional and falls back to the video's first frame.
    const capturePoster = (file) => {
      if (!posterField || typeof URL === 'undefined') return;
      posterField.value = '';
      const url = URL.createObjectURL(file);
      const probe = document.createElement('video');
      probe.muted = true;
      probe.playsInline = true;
      probe.preload = 'metadata';
      const cleanup = () => { URL.revokeObjectURL(url); probe.removeAttribute('src'); };

      probe.addEventListener('loadeddata', () => {
        // A frame a second in beats frame zero, which is often a black fade-in.
        probe.currentTime = Math.min(1, (probe.duration || 2) / 2);
      });
      probe.addEventListener('seeked', () => {
        try {
          const h = Math.min(probe.videoHeight || 720, 720);
          const scale = h / (probe.videoHeight || h);
          const canvas = document.createElement('canvas');
          canvas.width = Math.round((probe.videoWidth || 405) * scale);
          canvas.height = Math.round(h);
          canvas.getContext('2d').drawImage(probe, 0, 0, canvas.width, canvas.height);
          const data = canvas.toDataURL('image/jpeg', 0.72);
          if (data.indexOf('data:image/jpeg') === 0 && data.length < 500000) posterField.value = data;
        } catch (err) { /* tainted canvas or unsupported codec — skip the poster */ }
        cleanup();
      });
      probe.addEventListener('error', cleanup);
      probe.src = url;
      // iOS needs a play() nudge before it will decode a frame.
      const p = probe.play && probe.play();
      if (p && p.catch) p.catch(() => {});
    };

    const handleFile = (file) => {
      if (!file) return;
      if (maxBytes && file.size > maxBytes) {
        showError('That video is ' + mb(file.size) + '. Please trim it to ' + mb(maxBytes) + ' or less before uploading.');
        fileInput.value = '';
        if (fileLabel) fileLabel.textContent = 'Tap to choose a video';
        previewWrap?.classList.add('hidden');
        return;
      }
      showError('');
      if (fileLabel) fileLabel.textContent = file.name + ' · ' + mb(file.size);
      if (previewVideo) {
        previewVideo.src = URL.createObjectURL(file);
        previewWrap.classList.remove('hidden');
      }
      capturePoster(file);
    };

    fileInput?.addEventListener('change', () => handleFile(fileInput.files[0]));

    ['dragenter', 'dragover'].forEach(ev => dropZone?.addEventListener(ev, (e) => {
      e.preventDefault();
      dropZone.classList.add('border-brand-aqua');
    }));
    ['dragleave', 'drop'].forEach(ev => dropZone?.addEventListener(ev, (e) => {
      e.preventDefault();
      dropZone.classList.remove('border-brand-aqua');
    }));
    dropZone?.addEventListener('drop', (e) => {
      const file = e.dataTransfer?.files?.[0];
      if (!file || !fileInput) return;
      const dt = new DataTransfer();
      dt.items.add(file);
      fileInput.files = dt.files;
      handleFile(file);
    });

    // Uploads take a while — say so instead of letting the page look frozen.
    reelForm.addEventListener('submit', (e) => {
      if (!reelForm.checkValidity()) return;
      const file = fileInput?.files[0];
      if (file && maxBytes && file.size > maxBytes) {
        e.preventDefault();
        showError('That video is ' + mb(file.size) + '. Please trim it to ' + mb(maxBytes) + ' or less before uploading.');
        return;
      }
      if (submitBtn) submitBtn.disabled = true;
      if (submitLabel) submitLabel.textContent = 'Uploading…';
    });
  }

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
