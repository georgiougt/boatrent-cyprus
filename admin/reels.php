<?php
/**
 * Guest reel moderation. Nothing a customer uploads reaches the site until it
 * is approved here, and only reels ticked "On homepage" appear in the strip
 * below the hero.
 */
require_once __DIR__ . '/includes/auth.php';
require_admin();
$adminTitle = 'Reels';

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
    $action = $_POST['action'] ?? '';
    $rid    = (int) ($_POST['id'] ?? 0);

    $stmt = $pdo->prepare('SELECT * FROM reels WHERE id = ?');
    $stmt->execute([$rid]);
    $reel = $stmt->fetch();

    if ($reel) {
        switch ($action) {
            case 'approve':
                $pdo->prepare("UPDATE reels SET status = 'approved' WHERE id = ?")->execute([$rid]);
                $_SESSION['admin_flash'] = 'Reel approved. Tick "On homepage" to show it below the hero.';
                break;

            case 'reject':
                // Keep the file so the decision can be undone; just pull it off the site.
                $pdo->prepare("UPDATE reels SET status = 'rejected', on_home = 0 WHERE id = ?")->execute([$rid]);
                $_SESSION['admin_flash'] = 'Reel rejected and removed from the homepage.';
                break;

            case 'home':
                if (empty($reel['on_home'])) {
                    // Featuring a reel implies approving it — one click instead of two.
                    $pdo->prepare("UPDATE reels SET on_home = 1, status = 'approved' WHERE id = ?")->execute([$rid]);
                    $_SESSION['admin_flash'] = 'Reel approved and added to the homepage.';
                } else {
                    $pdo->prepare('UPDATE reels SET on_home = 0 WHERE id = ?')->execute([$rid]);
                    $_SESSION['admin_flash'] = 'Reel removed from the homepage.';
                }
                break;

            case 'edit':
                // Retitle a reel — guests write these, so they often need a tidy-up.
                $name = trim($_POST['guest_name'] ?? '');
                if ($name === '') {
                    $_SESSION['admin_flash_error'] = 'A reel needs a name to credit — the card shows it.';
                    break;
                }
                $pdo->prepare('UPDATE reels SET guest_name = ?, location = ?, boat_name = ?, caption = ? WHERE id = ?')
                    ->execute([
                        $name,
                        trim($_POST['location'] ?? '') ?: null,
                        trim($_POST['boat_name'] ?? '') ?: null,
                        trim($_POST['caption'] ?? '') ?: null,
                        $rid,
                    ]);
                $_SESSION['admin_flash'] = 'Reel details updated.';
                break;

            case 'order':
                $pdo->prepare('UPDATE reels SET sort_order = ? WHERE id = ?')
                    ->execute([(int) ($_POST['sort_order'] ?? 0), $rid]);
                $_SESSION['admin_flash'] = 'Homepage order updated.';
                break;

            case 'delete':
                delete_reel_files($reel);
                $pdo->prepare('DELETE FROM reels WHERE id = ?')->execute([$rid]);
                $_SESSION['admin_flash'] = 'Reel deleted.';
                break;
        }
    }

    header('Location: /admin/reels.php' . (!empty($_POST['filter']) ? '?status=' . urlencode($_POST['filter']) : ''));
    exit;
}

$flash      = $_SESSION['admin_flash'] ?? null;
$flashError = $_SESSION['admin_flash_error'] ?? null;
unset($_SESSION['admin_flash'], $_SESSION['admin_flash_error']);

$filter = $_GET['status'] ?? 'pending';
$valid  = ['all', 'pending', 'approved', 'rejected', 'home'];
if (!in_array($filter, $valid, true)) {
    $filter = 'pending';
}

$order = 'ORDER BY on_home DESC, sort_order ASC, created_at DESC';
if ($filter === 'all') {
    $reels = $pdo->query("SELECT * FROM reels {$order}")->fetchAll();
} elseif ($filter === 'home') {
    $reels = $pdo->query("SELECT * FROM reels WHERE on_home = 1 AND status = 'approved' {$order}")->fetchAll();
} else {
    $stmt = $pdo->prepare("SELECT * FROM reels WHERE status = ? {$order}");
    $stmt->execute([$filter]);
    $reels = $stmt->fetchAll();
}

$counts = [
    'all'      => (int) $pdo->query('SELECT COUNT(*) FROM reels')->fetchColumn(),
    'pending'  => (int) $pdo->query("SELECT COUNT(*) FROM reels WHERE status='pending'")->fetchColumn(),
    'approved' => (int) $pdo->query("SELECT COUNT(*) FROM reels WHERE status='approved'")->fetchColumn(),
    'rejected' => (int) $pdo->query("SELECT COUNT(*) FROM reels WHERE status='rejected'")->fetchColumn(),
    'home'     => (int) $pdo->query("SELECT COUNT(*) FROM reels WHERE on_home=1 AND status='approved'")->fetchColumn(),
];

include __DIR__ . '/includes/admin-header.php';
?>

<?php if ($flash): ?>
<div class="mb-6 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3 flex items-center gap-2">
  <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
  <?php echo e($flash); ?>
</div>
<?php endif; ?>

<?php if ($flashError): ?>
<div class="mb-6 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3"><?php echo e($flashError); ?></div>
<?php endif; ?>

<div class="mb-6 bg-white border border-brand-navy/10 rounded-2xl px-5 py-4 text-sm text-brand-navy/70 flex items-start gap-3">
  <svg class="w-5 h-5 shrink-0 text-brand-aqua mt-0.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
  <p>Guests upload reels at <a href="/share-your-reel" target="_blank" rel="noopener" class="text-brand-aquaD font-medium hover:underline cursor-pointer">/share-your-reel</a> (max <?php echo e(human_bytes(reel_max_bytes())); ?> per video). Nothing appears on the site until you approve it — and only reels marked <strong class="text-brand-ink">On homepage</strong> show in the strip under the hero.</p>
</div>

<!-- Filter tabs -->
<div class="flex flex-wrap gap-2 mb-6">
  <?php
  $tabs = ['pending' => 'Pending', 'home' => 'On homepage', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All'];
  foreach ($tabs as $key => $label):
    $active = $filter === $key;
  ?>
  <a href="/admin/reels.php?status=<?php echo $key; ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 cursor-pointer <?php echo $active ? 'bg-brand-navy text-white' : 'bg-white border border-brand-navy/10 text-brand-navy/70 hover:bg-brand-foam'; ?>">
    <?php echo $label; ?>
    <span class="text-xs font-semibold px-1.5 py-0.5 rounded-full <?php echo $active ? 'bg-white/20' : 'bg-brand-navy/10'; ?>"><?php echo $counts[$key]; ?></span>
  </a>
  <?php endforeach; ?>
</div>

<?php if ($reels): ?>
<div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">
  <?php foreach ($reels as $reel):
    $badge = [
      'pending'  => 'bg-brand-gold/20 text-brand-gold',
      'approved' => 'bg-green-100 text-green-700',
      'rejected' => 'bg-brand-navy/10 text-brand-navy/60',
    ][$reel['status']] ?? '';
  ?>
  <div class="bg-white rounded-2xl border border-brand-navy/10 overflow-hidden flex flex-col">
    <div class="bg-brand-ink flex items-center justify-center">
      <video class="w-full max-h-80 object-contain" controls playsinline preload="metadata"
             <?php echo $reel['poster_path'] ? 'poster="' . e($reel['poster_path']) . '"' : ''; ?>>
        <source src="<?php echo e($reel['video_path']); ?>" type="<?php echo e($reel['mime'] ?: 'video/mp4'); ?>">
      </video>
    </div>

    <div class="p-5 flex-1 flex flex-col">
      <div class="flex flex-wrap items-center gap-2 mb-2">
        <span class="text-xs font-semibold px-2.5 py-1 rounded-full <?php echo $badge; ?>"><?php echo e(ucfirst($reel['status'])); ?></span>
        <?php if ($reel['on_home']): ?>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-brand-aqua/15 text-brand-aquaD">On homepage</span>
        <?php endif; ?>
        <span class="text-xs text-brand-navy/40 ml-auto"><?php echo e(human_bytes((int) $reel['filesize'])); ?></span>
      </div>

      <h3 class="font-display text-lg font-semibold text-brand-ink"><?php echo e($reel['guest_name']); ?></h3>
      <?php $meta = trim(implode(' · ', array_filter([$reel['location'], $reel['boat_name']]))); ?>
      <?php if ($meta): ?><p class="text-sm text-brand-navy/55"><?php echo e($meta); ?></p><?php endif; ?>
      <?php if ($reel['caption']): ?>
      <p class="mt-3 text-sm text-brand-navy/75 bg-brand-sand rounded-xl px-4 py-3 leading-relaxed">&ldquo;<?php echo e($reel['caption']); ?>&rdquo;</p>
      <?php endif; ?>

      <details class="mt-3 group">
        <summary class="inline-flex items-center gap-1.5 text-xs font-medium text-brand-navy/55 hover:text-brand-aquaD cursor-pointer list-none">
          <svg class="w-3.5 h-3.5 transition-transform duration-200 group-open:rotate-90" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          Edit credit &amp; caption
        </summary>
        <form method="post" class="mt-3 space-y-2.5 bg-brand-sand rounded-xl p-4">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="action" value="edit">
          <input type="hidden" name="id" value="<?php echo (int) $reel['id']; ?>">
          <input type="hidden" name="filter" value="<?php echo e($filter); ?>">
          <div>
            <label for="gn-<?php echo (int) $reel['id']; ?>" class="block text-xs font-medium text-brand-navy/60 mb-1">Name on the card</label>
            <input id="gn-<?php echo (int) $reel['id']; ?>" name="guest_name" type="text" required maxlength="60" value="<?php echo e($reel['guest_name']); ?>" class="w-full bg-white border border-brand-navy/10 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua">
          </div>
          <div class="grid grid-cols-2 gap-2.5">
            <div>
              <label for="lo-<?php echo (int) $reel['id']; ?>" class="block text-xs font-medium text-brand-navy/60 mb-1">Where from</label>
              <input id="lo-<?php echo (int) $reel['id']; ?>" name="location" type="text" maxlength="60" value="<?php echo e((string) $reel['location']); ?>" placeholder="e.g. Leeds, UK" class="w-full bg-white border border-brand-navy/10 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua">
            </div>
            <div>
              <label for="bn-<?php echo (int) $reel['id']; ?>" class="block text-xs font-medium text-brand-navy/60 mb-1">Boat</label>
              <input id="bn-<?php echo (int) $reel['id']; ?>" name="boat_name" type="text" maxlength="80" value="<?php echo e((string) $reel['boat_name']); ?>" placeholder="e.g. Princess 30M" class="w-full bg-white border border-brand-navy/10 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua">
            </div>
          </div>
          <div>
            <label for="cp-<?php echo (int) $reel['id']; ?>" class="block text-xs font-medium text-brand-navy/60 mb-1">Caption <span class="text-brand-navy/35">(shown above the name)</span></label>
            <textarea id="cp-<?php echo (int) $reel['id']; ?>" name="caption" rows="2" maxlength="280" placeholder="One line about the day" class="w-full bg-white border border-brand-navy/10 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua resize-none"><?php echo e((string) $reel['caption']); ?></textarea>
          </div>
          <button type="submit" class="inline-flex items-center gap-1.5 bg-brand-navy hover:bg-brand-ink text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors duration-200 cursor-pointer">Save details</button>
        </form>
      </details>

      <p class="mt-3 text-xs text-brand-navy/40">
        Received <?php echo e(date('j M Y, H:i', strtotime($reel['created_at']))); ?>
        <?php if ($reel['guest_email']): ?>
        &middot; <a href="mailto:<?php echo e($reel['guest_email']); ?>" class="hover:text-brand-aquaD cursor-pointer"><?php echo e($reel['guest_email']); ?></a>
        <?php endif; ?>
      </p>

      <div class="mt-4 pt-4 border-t border-brand-navy/10 flex flex-wrap items-center gap-2">
        <form method="post">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="action" value="home">
          <input type="hidden" name="id" value="<?php echo (int) $reel['id']; ?>">
          <input type="hidden" name="filter" value="<?php echo e($filter); ?>">
          <button type="submit" class="inline-flex items-center gap-1.5 text-sm font-semibold px-3.5 py-2 rounded-lg transition-colors duration-200 cursor-pointer <?php echo $reel['on_home'] ? 'bg-brand-aqua/15 text-brand-aquaD hover:bg-brand-aqua/25' : 'bg-brand-navy text-white hover:bg-brand-ink'; ?>">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo $reel['on_home'] ? 'M6 18L18 6M6 6l12 12' : 'M5 13l4 4L19 7'; ?>"/></svg>
            <?php echo $reel['on_home'] ? 'Remove from home' : 'Show on homepage'; ?>
          </button>
        </form>

        <?php if ($reel['status'] !== 'approved'): ?>
        <form method="post">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="action" value="approve">
          <input type="hidden" name="id" value="<?php echo (int) $reel['id']; ?>">
          <input type="hidden" name="filter" value="<?php echo e($filter); ?>">
          <button type="submit" class="text-sm text-brand-navy/70 hover:text-brand-aquaD border border-brand-navy/10 hover:border-brand-aqua/40 rounded-lg px-3.5 py-2 transition-colors duration-200 cursor-pointer">Approve</button>
        </form>
        <?php endif; ?>

        <?php if ($reel['status'] !== 'rejected'): ?>
        <form method="post">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="action" value="reject">
          <input type="hidden" name="id" value="<?php echo (int) $reel['id']; ?>">
          <input type="hidden" name="filter" value="<?php echo e($filter); ?>">
          <button type="submit" class="text-sm text-brand-navy/70 hover:text-brand-ink border border-brand-navy/10 rounded-lg px-3.5 py-2 transition-colors duration-200 cursor-pointer">Reject</button>
        </form>
        <?php endif; ?>

        <form method="post" class="flex items-center gap-1.5" title="Lower numbers appear first on the homepage">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="action" value="order">
          <input type="hidden" name="id" value="<?php echo (int) $reel['id']; ?>">
          <input type="hidden" name="filter" value="<?php echo e($filter); ?>">
          <label for="ord-<?php echo (int) $reel['id']; ?>" class="text-xs text-brand-navy/45">Order</label>
          <input id="ord-<?php echo (int) $reel['id']; ?>" name="sort_order" type="number" value="<?php echo (int) $reel['sort_order']; ?>" class="w-16 bg-brand-sand border border-brand-navy/10 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua">
          <button type="submit" class="text-xs text-brand-navy/60 hover:text-brand-aquaD underline cursor-pointer">Save</button>
        </form>

        <form method="post" class="ml-auto" data-confirm="Delete this reel and its video file permanently?">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?php echo (int) $reel['id']; ?>">
          <input type="hidden" name="filter" value="<?php echo e($filter); ?>">
          <button type="submit" aria-label="Delete reel" class="inline-flex items-center justify-center text-brand-navy/45 hover:text-red-600 border border-brand-navy/10 hover:border-red-200 hover:bg-red-50 rounded-lg px-3 py-2 transition-colors duration-200 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.87 12.14A2 2 0 0116.14 21H7.86a2 2 0 01-1.99-1.86L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/></svg>
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
    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.55-2.27A1 1 0 0121 8.62v6.76a1 1 0 01-1.45.89L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
  </div>
  <h3 class="font-display text-xl font-semibold text-brand-ink mb-2">No <?php echo $filter === 'all' ? '' : e($filter === 'home' ? 'homepage' : $filter) . ' '; ?>reels</h3>
  <p class="text-brand-navy/55">Guest reels sent from /share-your-reel land here for review.</p>
</div>
<?php endif; ?>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
