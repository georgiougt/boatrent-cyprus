    </main>
    <footer class="px-5 sm:px-8 py-4 text-center text-xs text-brand-navy/40 border-t border-brand-navy/10">
      BoatRent Cyprus Admin &middot; <?php echo date('Y'); ?>
    </footer>
  </div>
</div>

<script>
  const btn = document.getElementById('admin-menu-btn');
  const sidebar = document.getElementById('admin-sidebar');
  const overlay = document.getElementById('admin-overlay');
  if (btn && sidebar) {
    const open = () => { sidebar.classList.remove('-translate-x-full'); overlay.classList.remove('hidden'); };
    const close = () => { sidebar.classList.add('-translate-x-full'); overlay.classList.add('hidden'); };
    btn.addEventListener('click', () => sidebar.classList.contains('-translate-x-full') ? open() : close());
    overlay.addEventListener('click', close);
  }

  // Confirm before delete
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('submit', (e) => {
      if (!confirm(el.getAttribute('data-confirm'))) e.preventDefault();
    });
  });
</script>
</body>
</html>
