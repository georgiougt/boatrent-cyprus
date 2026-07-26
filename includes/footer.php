</main>

<footer class="relative bg-brand-ink text-white pt-20 pb-8 overflow-hidden">
  <div class="absolute -top-24 right-0 w-80 h-80 bg-brand-aqua/10 rounded-full blur-3xl"></div>
  <div class="absolute -bottom-32 -left-20 w-96 h-96 bg-brand-navy2/30 rounded-full blur-3xl"></div>

  <div class="relative max-w-7xl mx-auto px-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-12 pb-12">
      <div class="md:col-span-1">
        <a href="/" class="flex items-center gap-2.5 mb-4 cursor-pointer">
          <span class="text-brand-gold">
            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 17c1.5 1 3 1 4.5 0s3-1 4.5 0 3 1 4.5 0 3-1 4.5 0"/>
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 14l7-9 7 9M12 5v9"/>
            </svg>
          </span>
          <span class="font-display text-lg font-semibold text-white">BoatRent<span class="text-brand-gold">.</span>Cyprus</span>
        </a>
        <p class="text-white/55 text-sm leading-relaxed">Cyprus' marketplace for yacht, catamaran and speedboat charters. Browse, inquire, and set sail.</p>
        <div class="flex gap-3 mt-5">
          <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="h-10 w-10 flex items-center justify-center rounded-full bg-white/5 hover:bg-brand-gold hover:text-brand-ink transition-colors duration-200 cursor-pointer">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.2c3.2 0 3.6 0 4.85.07 3.25.15 4.77 1.69 4.92 4.92.06 1.25.07 1.62.07 4.81s-.01 3.56-.07 4.81c-.15 3.23-1.66 4.77-4.92 4.92-1.25.06-1.62.07-4.85.07-3.2 0-3.6 0-4.85-.07-3.26-.15-4.77-1.7-4.92-4.92C2.21 15.56 2.2 15.19 2.2 12s.01-3.56.07-4.81c.15-3.23 1.67-4.77 4.92-4.92C8.4 2.2 8.8 2.2 12 2.2zm0 5.45a4.35 4.35 0 1 0 0 8.7 4.35 4.35 0 0 0 0-8.7zm0 1.8a2.55 2.55 0 1 1 0 5.1 2.55 2.55 0 0 1 0-5.1zm5.54-3.4a1.04 1.04 0 1 0 0 2.08 1.04 1.04 0 0 0 0-2.08z"/></svg>
          </a>
          <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="h-10 w-10 flex items-center justify-center rounded-full bg-white/5 hover:bg-brand-gold hover:text-brand-ink transition-colors duration-200 cursor-pointer">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22 12.06C22 6.5 17.52 2 12 2S2 6.5 2 12.06c0 5.02 3.66 9.18 8.44 9.94v-7.03H7.9v-2.9h2.54V9.7c0-2.5 1.49-3.89 3.78-3.89 1.1 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56v1.87h2.78l-.44 2.9h-2.34V22c4.78-.76 8.44-4.92 8.44-9.94z"/></svg>
          </a>
          <a href="https://wa.me/35799000000" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp" class="h-10 w-10 flex items-center justify-center rounded-full bg-white/5 hover:bg-brand-gold hover:text-brand-ink transition-colors duration-200 cursor-pointer">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12c0 1.85.5 3.58 1.36 5.06L2 22l5.1-1.33A9.94 9.94 0 0 0 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2zm4.4 12.03c-.24-.12-1.43-.7-1.65-.78-.22-.08-.38-.12-.54.12-.16.24-.62.78-.76.94-.14.16-.28.18-.52.06-.24-.12-1.02-.38-1.94-1.2-.72-.64-1.2-1.43-1.34-1.67-.14-.24-.02-.37.1-.49.12-.12.24-.28.36-.42.12-.14.16-.24.24-.4.08-.16.04-.3-.02-.42-.06-.12-.54-1.3-.74-1.78-.2-.47-.4-.4-.54-.41h-.46c-.16 0-.42.06-.64.3-.22.24-.84.82-.84 2s.86 2.32.98 2.48c.12.16 1.7 2.6 4.12 3.64.58.25 1.03.4 1.38.51.58.18 1.1.16 1.52.1.46-.07 1.43-.58 1.63-1.15.2-.57.2-1.06.14-1.16-.06-.1-.22-.16-.46-.28z"/></svg>
          </a>
        </div>
      </div>

      <div>
        <h3 class="font-display text-lg font-semibold mb-4">Destinations</h3>
        <ul class="space-y-2 text-sm text-white/55">
          <?php foreach (get_cities() as $c): ?>
          <li><a href="/<?php echo e($c['slug']); ?>" class="hover:text-brand-gold transition-colors duration-200 cursor-pointer"><?php echo e($c['name']); ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div>
        <h3 class="font-display text-lg font-semibold mb-4">Company</h3>
        <ul class="space-y-2 text-sm text-white/55">
          <li><a href="/about" class="hover:text-brand-gold transition-colors duration-200 cursor-pointer">About Us</a></li>
          <li><a href="/boats" class="hover:text-brand-gold transition-colors duration-200 cursor-pointer">Browse Fleet</a></li>
          <li><a href="/blog" class="hover:text-brand-gold transition-colors duration-200 cursor-pointer">Blog</a></li>
          <li><a href="/faq" class="hover:text-brand-gold transition-colors duration-200 cursor-pointer">FAQ</a></li>
          <li><a href="/contact" class="hover:text-brand-gold transition-colors duration-200 cursor-pointer">Contact</a></li>
          <li><a href="/admin/login.php" class="hover:text-brand-gold transition-colors duration-200 cursor-pointer">Owner Login</a></li>
        </ul>
      </div>

      <div>
        <h3 class="font-display text-lg font-semibold mb-4">Get in touch</h3>
        <ul class="space-y-2 text-sm text-white/55">
          <li><a href="mailto:hello@boatrentcyprus.com" class="hover:text-brand-gold transition-colors duration-200 cursor-pointer">hello@boatrentcyprus.com</a></li>
          <li><a href="tel:+35725000000" class="hover:text-brand-gold transition-colors duration-200 cursor-pointer">+357 25 000 000</a></li>
          <li class="text-white/40 pt-2">Limassol Marina, Cyprus</li>
        </ul>
      </div>
    </div>

    <div class="border-t border-white/10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-white/40">
      <p>&copy; <?php echo date('Y'); ?> BoatRent Cyprus. All rights reserved.</p>
      <p>Charters operated by licensed local partners across Cyprus.</p>
    </div>
  </div>
</footer>

<button id="back-to-top" type="button" aria-label="Back to top" class="fixed bottom-6 right-6 z-40 h-12 w-12 rounded-full bg-brand-gold text-brand-ink shadow-lg flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300 cursor-pointer hover:bg-brand-goldL">
  <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
</button>

<script src="<?php echo e(asset('/js/main.js')); ?>"></script>
</body>
</html>
