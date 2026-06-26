<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();
$adminTitle = 'Dashboard';

$pdo = db();
$recent = $pdo->query("SELECT * FROM inquiries ORDER BY created_at DESC LIMIT 6")->fetchAll();
$topCities = $pdo->query("
    SELECT c.name, COUNT(b.id) AS cnt
    FROM cities c LEFT JOIN boats b ON b.city_id = c.id AND b.status='active'
    GROUP BY c.id ORDER BY cnt DESC
")->fetchAll();
$maxCnt = max(1, ...array_map(fn($r) => (int) $r['cnt'], $topCities ?: [['cnt' => 1]]));

include __DIR__ . '/includes/admin-header.php';
?>

<!-- Stat cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
  <?php
  $cards = [
    ['Total Boats', $stats['boats'], 'M3 17c1.5 1 3 1 4.5 0s3-1 4.5 0 3 1 4.5 0 3-1 4.5 0M5 14l7-9 7 9', 'text-brand-aqua bg-brand-aqua/10', '/admin/boats.php'],
    ['Active Listings', $stats['active'], 'M5 13l4 4L19 7', 'text-green-600 bg-green-100', '/admin/boats.php'],
    ['Total Inquiries', $stats['inquiries'], 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'text-brand-navy bg-brand-navy/10', '/admin/inquiries.php'],
    ['New Messages', $stats['new'], 'M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 00-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0a3 3 0 11-6 0', 'text-brand-gold bg-brand-gold/15', '/admin/inquiries.php?status=new'],
  ];
  foreach ($cards as $c): ?>
  <a href="<?php echo $c[4]; ?>" class="bg-white rounded-2xl border border-brand-navy/10 p-5 sm:p-6 hover:shadow-md transition-shadow duration-200 cursor-pointer">
    <div class="flex items-center justify-between mb-3">
      <span class="h-10 w-10 rounded-xl flex items-center justify-center <?php echo $c[3]; ?>">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo $c[2]; ?>"/></svg>
      </span>
    </div>
    <p class="font-display text-3xl font-bold text-brand-ink"><?php echo (int) $c[1]; ?></p>
    <p class="text-brand-navy/55 text-sm mt-0.5"><?php echo e($c[0]); ?></p>
  </a>
  <?php endforeach; ?>
</div>

<div class="grid lg:grid-cols-3 gap-6">
  <!-- Recent inquiries -->
  <div class="lg:col-span-2 bg-white rounded-2xl border border-brand-navy/10 overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-brand-navy/10">
      <h2 class="font-display text-lg font-semibold text-brand-ink">Recent Inquiries</h2>
      <a href="/admin/inquiries.php" class="text-sm text-brand-aquaD hover:text-brand-aqua font-medium cursor-pointer">View all</a>
    </div>
    <?php if ($recent): ?>
    <div class="divide-y divide-brand-navy/5">
      <?php foreach ($recent as $r): ?>
      <a href="/admin/inquiries.php#inq-<?php echo (int) $r['id']; ?>" class="flex items-center gap-4 px-6 py-4 hover:bg-brand-sand/60 transition-colors duration-200 cursor-pointer">
        <div class="h-10 w-10 rounded-full bg-brand-foam text-brand-aquaD flex items-center justify-center font-semibold uppercase shrink-0"><?php echo e(substr($r['name'], 0, 1)); ?></div>
        <div class="min-w-0 flex-1">
          <p class="font-medium text-brand-ink truncate"><?php echo e($r['name']); ?> <?php if ($r['boat_name']): ?><span class="text-brand-navy/50 font-normal">· <?php echo e($r['boat_name']); ?></span><?php endif; ?></p>
          <p class="text-sm text-brand-navy/55 truncate"><?php echo e($r['message'] ?: 'No message'); ?></p>
        </div>
        <?php
        $badge = ['new' => 'bg-brand-gold/20 text-brand-gold', 'contacted' => 'bg-blue-100 text-blue-700', 'closed' => 'bg-brand-navy/10 text-brand-navy/60'][$r['status']] ?? 'bg-brand-navy/10';
        ?>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-full shrink-0 <?php echo $badge; ?>"><?php echo e(ucfirst($r['status'])); ?></span>
      </a>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p class="px-6 py-10 text-center text-brand-navy/50 text-sm">No inquiries yet.</p>
    <?php endif; ?>
  </div>

  <!-- Boats per city -->
  <div class="bg-white rounded-2xl border border-brand-navy/10 p-6">
    <h2 class="font-display text-lg font-semibold text-brand-ink mb-5">Fleet by Destination</h2>
    <ul class="space-y-4">
      <?php foreach ($topCities as $row): $pct = round(((int) $row['cnt'] / $maxCnt) * 100); ?>
      <li>
        <div class="flex items-center justify-between text-sm mb-1.5">
          <span class="text-brand-navy/70"><?php echo e($row['name']); ?></span>
          <span class="font-semibold text-brand-ink"><?php echo (int) $row['cnt']; ?></span>
        </div>
        <div class="h-2 rounded-full bg-brand-sand overflow-hidden">
          <div class="h-full rounded-full bg-brand-aqua" style="width: <?php echo max(6, $pct); ?>%"></div>
        </div>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
