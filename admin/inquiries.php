<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();
$adminTitle = 'Inquiries';

$pdo = db();

// Handle status update / delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
    $action = $_POST['action'] ?? '';
    $iid = (int) ($_POST['id'] ?? 0);
    if ($iid && $action === 'status') {
        $new = in_array($_POST['status'] ?? '', ['new', 'contacted', 'closed'], true) ? $_POST['status'] : 'new';
        $pdo->prepare('UPDATE inquiries SET status = ? WHERE id = ?')->execute([$new, $iid]);
        $_SESSION['admin_flash'] = 'Inquiry marked as ' . $new . '.';
    } elseif ($iid && $action === 'delete') {
        $pdo->prepare('DELETE FROM inquiries WHERE id = ?')->execute([$iid]);
        $_SESSION['admin_flash'] = 'Inquiry deleted.';
    }
    header('Location: /admin/inquiries.php' . (!empty($_POST['filter']) ? '?status=' . urlencode($_POST['filter']) : ''));
    exit;
}

$flash = $_SESSION['admin_flash'] ?? null;
unset($_SESSION['admin_flash']);

$filter = $_GET['status'] ?? 'all';
$valid = ['all', 'new', 'contacted', 'closed'];
if (!in_array($filter, $valid, true)) {
    $filter = 'all';
}

if ($filter === 'all') {
    $inquiries = $pdo->query('SELECT * FROM inquiries ORDER BY created_at DESC')->fetchAll();
} else {
    $stmt = $pdo->prepare('SELECT * FROM inquiries WHERE status = ? ORDER BY created_at DESC');
    $stmt->execute([$filter]);
    $inquiries = $stmt->fetchAll();
}

$counts = [
    'all'       => (int) $pdo->query('SELECT COUNT(*) FROM inquiries')->fetchColumn(),
    'new'       => (int) $pdo->query("SELECT COUNT(*) FROM inquiries WHERE status='new'")->fetchColumn(),
    'contacted' => (int) $pdo->query("SELECT COUNT(*) FROM inquiries WHERE status='contacted'")->fetchColumn(),
    'closed'    => (int) $pdo->query("SELECT COUNT(*) FROM inquiries WHERE status='closed'")->fetchColumn(),
];

include __DIR__ . '/includes/admin-header.php';
?>

<?php if ($flash): ?>
<div class="mb-6 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3 flex items-center gap-2">
  <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
  <?php echo e($flash); ?>
</div>
<?php endif; ?>

<!-- Filter tabs -->
<div class="flex flex-wrap gap-2 mb-6">
  <?php
  $tabs = ['all' => 'All', 'new' => 'New', 'contacted' => 'Contacted', 'closed' => 'Closed'];
  foreach ($tabs as $key => $label):
    $active = $filter === $key;
  ?>
  <a href="/admin/inquiries.php?status=<?php echo $key; ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 cursor-pointer <?php echo $active ? 'bg-brand-navy text-white' : 'bg-white border border-brand-navy/10 text-brand-navy/70 hover:bg-brand-foam'; ?>">
    <?php echo $label; ?>
    <span class="text-xs font-semibold px-1.5 py-0.5 rounded-full <?php echo $active ? 'bg-white/20' : 'bg-brand-navy/10'; ?>"><?php echo $counts[$key]; ?></span>
  </a>
  <?php endforeach; ?>
</div>

<?php if ($inquiries): ?>
<div class="space-y-4">
  <?php foreach ($inquiries as $inq):
    $badge = ['new' => 'bg-brand-gold/20 text-brand-gold', 'contacted' => 'bg-blue-100 text-blue-700', 'closed' => 'bg-brand-navy/10 text-brand-navy/60'][$inq['status']] ?? '';
  ?>
  <div id="inq-<?php echo (int) $inq['id']; ?>" class="bg-white rounded-2xl border border-brand-navy/10 p-5 sm:p-6">
    <div class="flex flex-col sm:flex-row sm:items-start gap-4">
      <div class="h-11 w-11 rounded-full bg-brand-foam text-brand-aquaD flex items-center justify-center font-semibold uppercase shrink-0"><?php echo e(substr($inq['name'], 0, 1)); ?></div>

      <div class="flex-1 min-w-0">
        <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
          <h3 class="font-display text-lg font-semibold text-brand-ink"><?php echo e($inq['name']); ?></h3>
          <span class="text-xs font-semibold px-2.5 py-1 rounded-full <?php echo $badge; ?>"><?php echo e(ucfirst($inq['status'])); ?></span>
          <?php if ($inq['boat_name']): ?>
          <span class="text-xs text-brand-navy/60 inline-flex items-center gap-1">
            <svg class="w-3.5 h-3.5 text-brand-aqua" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17c1.5 1 3 1 4.5 0s3-1 4.5 0 3 1 4.5 0 3-1 4.5 0M5 14l7-9 7 9"/></svg>
            <?php echo e($inq['boat_name']); ?>
          </span>
          <?php endif; ?>
        </div>

        <div class="flex flex-wrap gap-x-5 gap-y-1 mt-2 text-sm text-brand-navy/65">
          <a href="mailto:<?php echo e($inq['email']); ?>" class="inline-flex items-center gap-1.5 hover:text-brand-aquaD cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <?php echo e($inq['email']); ?>
          </a>
          <?php if ($inq['phone']): ?>
          <a href="tel:<?php echo e($inq['phone']); ?>" class="inline-flex items-center gap-1.5 hover:text-brand-aquaD cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.95.68l1.5 4.5a1 1 0 01-.5 1.2l-2.26 1.13a11 11 0 005.52 5.52l1.13-2.26a1 1 0 011.2-.5l4.5 1.5a1 1 0 01.68.95V19a2 2 0 01-2 2h-1C9.72 21 3 14.28 3 6V5z"/></svg>
            <?php echo e($inq['phone']); ?>
          </a>
          <?php endif; ?>
          <?php if ($inq['city']): ?><span class="inline-flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg><?php echo e($inq['city']); ?></span><?php endif; ?>
        </div>

        <?php if ($inq['date_from'] || $inq['guests']): ?>
        <div class="flex flex-wrap gap-x-5 gap-y-1 mt-1.5 text-sm text-brand-navy/55">
          <?php if ($inq['date_from']): ?><span>Dates: <strong class="text-brand-navy/80"><?php echo e($inq['date_from']); ?><?php echo $inq['date_to'] && $inq['date_to'] !== $inq['date_from'] ? ' → ' . e($inq['date_to']) : ''; ?></strong></span><?php endif; ?>
          <?php if ($inq['guests']): ?><span>Guests: <strong class="text-brand-navy/80"><?php echo (int) $inq['guests']; ?></strong></span><?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ($inq['message']): ?>
        <p class="mt-3 text-brand-navy/75 bg-brand-sand rounded-xl px-4 py-3 text-sm leading-relaxed">&ldquo;<?php echo e($inq['message']); ?>&rdquo;</p>
        <?php endif; ?>

        <p class="mt-2 text-xs text-brand-navy/40">Received <?php echo e(date('j M Y, H:i', strtotime($inq['created_at']))); ?></p>
      </div>

      <!-- Actions -->
      <div class="flex sm:flex-col gap-2 shrink-0">
        <form method="post" class="flex items-center gap-2">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="action" value="status">
          <input type="hidden" name="id" value="<?php echo (int) $inq['id']; ?>">
          <input type="hidden" name="filter" value="<?php echo e($filter); ?>">
          <label for="st-<?php echo (int) $inq['id']; ?>" class="sr-only">Set status</label>
          <select id="st-<?php echo (int) $inq['id']; ?>" name="status" onchange="this.form.submit()" class="bg-brand-sand border border-brand-navy/10 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua cursor-pointer">
            <option value="new" <?php echo $inq['status'] === 'new' ? 'selected' : ''; ?>>New</option>
            <option value="contacted" <?php echo $inq['status'] === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
            <option value="closed" <?php echo $inq['status'] === 'closed' ? 'selected' : ''; ?>>Closed</option>
          </select>
        </form>
        <form method="post" data-confirm="Delete this inquiry permanently?">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?php echo (int) $inq['id']; ?>">
          <input type="hidden" name="filter" value="<?php echo e($filter); ?>">
          <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 text-sm text-brand-navy/55 hover:text-red-600 border border-brand-navy/10 hover:border-red-200 hover:bg-red-50 rounded-lg px-3 py-2 transition-colors duration-200 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.87 12.14A2 2 0 0116.14 21H7.86a2 2 0 01-1.99-1.86L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/></svg>
            Delete
          </button>
        </form>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php else: ?>
<div class="bg-white rounded-2xl border border-brand-navy/10 p-14 text-center">
  <div class="mx-auto h-14 w-14 rounded-full bg-brand-foam flex items-center justify-center text-brand-aqua mb-4">
    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
  </div>
  <h3 class="font-display text-xl font-semibold text-brand-ink mb-2">No <?php echo $filter === 'all' ? '' : e($filter) . ' '; ?>inquiries</h3>
  <p class="text-brand-navy/55">When customers send inquiries, they'll appear here.</p>
</div>
<?php endif; ?>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
