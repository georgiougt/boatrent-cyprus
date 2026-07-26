<?php
/** Admin chrome. Expects $adminTitle and that auth.php is already included + require_admin() called. */
$adminPage = basename($_SERVER['PHP_SELF']);
$stats = admin_stats();

function adminNav(string $page, string $current, string $href, string $label, string $icon, int $badge = 0): string
{
    $active = $page === $current;
    $base = 'flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-colors duration-200 cursor-pointer';
    $cls  = $active ? 'bg-brand-gold text-brand-ink' : 'text-white/70 hover:bg-white/10 hover:text-white';
    $badgeHtml = $badge > 0
        ? '<span class="ml-auto text-xs font-semibold px-2 py-0.5 rounded-full ' . ($active ? 'bg-brand-ink/20 text-brand-ink' : 'bg-brand-gold text-brand-ink') . '">' . $badge . '</span>'
        : '';
    return "<a href=\"{$href}\" class=\"{$base} {$cls}\"><svg class=\"w-5 h-5 shrink-0\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"1.8\" viewBox=\"0 0 24 24\" aria-hidden=\"true\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"{$icon}\"/></svg><span>{$label}</span>{$badgeHtml}</a>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($adminTitle) ? e($adminTitle) . ' · Admin' : 'Admin'; ?> | BoatRent Cyprus</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = { theme: { extend: {
    colors: { brand: { ink:'#0D1A33', navy:'#16395A', navy2:'#1F567C', gold:'#12A4C9', goldL:'#46C2E0', aqua:'#1ECEB6', aquaD:'#0C7A6E', sand:'#E7F7FA', foam:'#D2E7EF' } },
    fontFamily: { display:['"Playfair Display"','serif'], body:['"Plus Jakarta Sans"','sans-serif'] }
  } } }
</script>
<style>
  body { font-family: 'Plus Jakarta Sans', sans-serif; }
  ::-webkit-scrollbar { width: 9px; }
  ::-webkit-scrollbar-track { background: #0D1A33; }
  ::-webkit-scrollbar-thumb { background: #16395A; border-radius: 9px; }
</style>
</head>
<body class="bg-brand-sand text-brand-ink min-h-screen">
<div class="flex min-h-screen">

  <!-- Sidebar -->
  <aside id="admin-sidebar" class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-brand-ink flex flex-col -translate-x-full lg:translate-x-0 transition-transform duration-300">
    <div class="px-5 py-5 border-b border-white/10">
      <a href="/admin/index.php" class="flex items-center gap-2.5 cursor-pointer">
        <span class="text-brand-gold">
          <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17c1.5 1 3 1 4.5 0s3-1 4.5 0 3 1 4.5 0 3-1 4.5 0M5 14l7-9 7 9M12 5v9"/></svg>
        </span>
        <span class="font-display text-base font-semibold text-white leading-tight">BoatRent<br><span class="text-white/50 text-xs font-body">Admin Panel</span></span>
      </a>
    </div>

    <nav class="flex-1 px-3 py-5 space-y-1">
      <?php
      echo adminNav('index.php', $adminPage, '/admin/index.php', 'Dashboard', 'M3 12l9-9 9 9M5 10v10a1 1 0 001 1h12a1 1 0 001-1V10');
      echo adminNav('boats.php', $adminPage, '/admin/boats.php', 'Boats', 'M3 17c1.5 1 3 1 4.5 0s3-1 4.5 0 3 1 4.5 0 3-1 4.5 0M5 14l7-9 7 9');
      echo adminNav('inquiries.php', $adminPage, '/admin/inquiries.php', 'Inquiries', 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', $stats['new']);
      echo adminNav('import-fleet.php', $adminPage, '/admin/import-fleet.php', 'Import Fleet', 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15');
      ?>
      <div class="pt-3 mt-3 border-t border-white/10">
        <?php echo adminNav('', $adminPage, '/index.php', 'View Website', 'M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14'); ?>
      </div>
    </nav>

    <div class="px-3 py-4 border-t border-white/10">
      <div class="flex items-center gap-3 px-2 py-2">
        <div class="h-9 w-9 rounded-full bg-brand-gold text-brand-ink flex items-center justify-center font-semibold uppercase"><?php echo e(substr(admin_username(), 0, 1)); ?></div>
        <div class="flex-1 min-w-0">
          <p class="text-white text-sm font-medium truncate"><?php echo e(admin_username()); ?></p>
          <p class="text-white/40 text-xs">Administrator</p>
        </div>
        <a href="/admin/logout.php" aria-label="Log out" class="text-white/50 hover:text-brand-gold transition-colors duration-200 cursor-pointer">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
        </a>
      </div>
    </div>
  </aside>

  <div id="admin-overlay" class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden"></div>

  <!-- Main -->
  <div class="flex-1 flex flex-col min-w-0">
    <header class="sticky top-0 z-20 bg-white border-b border-brand-navy/10 px-5 sm:px-8 py-4 flex items-center gap-4">
      <button id="admin-menu-btn" type="button" aria-label="Toggle menu" class="lg:hidden h-10 w-10 flex items-center justify-center rounded-lg hover:bg-brand-sand transition-colors cursor-pointer">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>
      <div>
        <h1 class="font-display text-xl font-semibold text-brand-ink"><?php echo e($adminTitle ?? 'Dashboard'); ?></h1>
      </div>
      <a href="/admin/boat-edit.php" class="ml-auto inline-flex items-center gap-2 bg-brand-navy hover:bg-brand-ink text-white text-sm font-semibold px-4 py-2.5 rounded-full transition-colors duration-200 cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        <span class="hidden sm:inline">Add Boat</span>
      </a>
    </header>

    <main class="flex-1 p-5 sm:p-8">
