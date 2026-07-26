<?php
require_once __DIR__ . '/includes/auth.php';

if (admin_logged_in()) {
    header('Location: /admin/index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        $error = 'Session expired, please try again.';
    } else {
        $u = trim($_POST['username'] ?? '');
        $p = $_POST['password'] ?? '';
        if (attempt_login($u, $p)) {
            header('Location: /admin/index.php');
            exit;
        }
        $error = 'Incorrect username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login | BoatRent Cyprus</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = { theme: { extend: {
    colors: { brand: { ink:'#0D1A33', navy:'#16395A', gold:'#12A4C9', goldL:'#46C2E0', aqua:'#1ECEB6', sand:'#E7F7FA' } },
    fontFamily: { display:['"Playfair Display"','serif'], body:['"Plus Jakarta Sans"','sans-serif'] }
  } } }
</script>
<style>body{font-family:'Plus Jakarta Sans',sans-serif}</style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 relative overflow-hidden bg-brand-ink">
  <img src="/assets/scenery/coast-gold.webp" alt="" aria-hidden="true" class="absolute inset-0 w-full h-full object-cover opacity-20">
  <div class="absolute inset-0 bg-gradient-to-br from-brand-ink/90 to-brand-navy/80"></div>

  <div class="relative w-full max-w-md">
    <div class="text-center mb-6">
      <a href="/index.php" class="inline-flex items-center gap-2.5 cursor-pointer">
        <span class="text-brand-gold">
          <svg class="w-9 h-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17c1.5 1 3 1 4.5 0s3-1 4.5 0 3 1 4.5 0 3-1 4.5 0M5 14l7-9 7 9M12 5v9"/></svg>
        </span>
        <span class="font-display text-2xl font-semibold text-white">BoatRent<span class="text-brand-gold">.</span>Cyprus</span>
      </a>
    </div>

    <div class="bg-white rounded-2xl shadow-2xl p-8">
      <h1 class="font-display text-2xl font-semibold text-brand-ink mb-1">Admin Login</h1>
      <p class="text-brand-navy/55 text-sm mb-6">Sign in to manage boats and inquiries.</p>

      <?php if ($error): ?>
      <div class="mb-5 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3"><?php echo e($error); ?></div>
      <?php endif; ?>

      <form method="post" class="space-y-4">
        <?php echo csrf_field(); ?>
        <div>
          <label for="username" class="block text-sm font-medium text-brand-navy/70 mb-1.5">Username</label>
          <input id="username" name="username" type="text" required autofocus value="<?php echo e($_POST['username'] ?? ''); ?>" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua">
        </div>
        <div>
          <label for="password" class="block text-sm font-medium text-brand-navy/70 mb-1.5">Password</label>
          <input id="password" name="password" type="password" required class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua">
        </div>
        <button type="submit" class="w-full bg-brand-gold hover:bg-brand-goldL text-brand-ink font-semibold px-6 py-3.5 rounded-full transition-colors duration-200 cursor-pointer">Sign In</button>
      </form>

      <div class="mt-6 pt-5 border-t border-brand-navy/10 text-center">
        <p class="text-xs text-brand-navy/50">Demo credentials — <span class="font-medium text-brand-navy">admin</span> / <span class="font-medium text-brand-navy">admin123</span></p>
      </div>
    </div>

    <p class="text-center mt-5"><a href="/index.php" class="text-white/60 hover:text-brand-gold text-sm transition-colors duration-200 cursor-pointer">&larr; Back to website</a></p>
  </div>
</body>
</html>
