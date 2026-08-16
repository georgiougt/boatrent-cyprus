<?php
/**
 * One-click house-reel importer (admin only).
 *
 * Guest reels live only in the gitignored database and /uploads, so a deploy
 * never carries them. The reels that ship with the site keep their media in the
 * tracked /assets/reels and their rows in data/reels.json, so this restores the
 * homepage strip after a deploy the same way Import Fleet restores the boats.
 *
 * Unlike the fleet import, nothing is replaced: seeded rows are matched on their
 * video path and refreshed, and genuine guest submissions are left untouched.
 */
require_once __DIR__ . '/includes/auth.php';
require_admin();

$jsonPath   = __DIR__ . '/../data/reels.json';
$jsonExists = is_file($jsonPath);

$currentHome = (int) db()->query("SELECT COUNT(*) FROM reels WHERE status = 'approved' AND on_home = 1")->fetchColumn();
$guestReels  = (int) db()->query("SELECT COUNT(*) FROM reels WHERE ip IS NULL OR ip <> 'seed'")->fetchColumn();

// Count the manifest entries, and flag any whose video is not on the server —
// importing those would just produce dead cards on the homepage.
$jsonCount = 0;
$missing   = [];
if ($jsonExists) {
    $decoded = json_decode((string) file_get_contents($jsonPath), true);
    if (is_array($decoded)) {
        $jsonCount = count($decoded);
        $root = realpath(__DIR__ . '/..');
        foreach ($decoded as $reel) {
            $path = (string) ($reel['video_path'] ?? '');
            if ($path !== '' && !is_file($root . $path)) {
                $missing[] = $path;
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
    if (!$jsonExists) {
        $_SESSION['admin_flash_error'] = 'data/reels.json was not found on the server — upload it and try again.';
        header('Location: /admin/import-reels.php');
        exit;
    }
    try {
        $res = import_seed_reels(db(), $jsonPath);
        $msg = "Reels imported — {$res['imported']} added, {$res['updated']} refreshed.";
        if ($res['missing']) {
            $msg .= ' Skipped ' . count($res['missing']) . ' with no video file on the server.';
        }
        $_SESSION['admin_flash'] = $msg;
        header('Location: /admin/reels.php?status=home');
        exit;
    } catch (Throwable $e) {
        $_SESSION['admin_flash_error'] = 'Import failed: ' . $e->getMessage();
        header('Location: /admin/import-reels.php');
        exit;
    }
}

$flashError = $_SESSION['admin_flash_error'] ?? null;
unset($_SESSION['admin_flash_error']);

$adminTitle = 'Import Reels';
include __DIR__ . '/includes/admin-header.php';
?>

<div class="max-w-2xl">
  <?php if ($flashError): ?>
  <div class="mb-6 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3"><?php echo e($flashError); ?></div>
  <?php endif; ?>

  <div class="bg-white rounded-2xl border border-brand-navy/10 shadow-sm p-6 sm:p-8">
    <h2 class="font-display text-2xl font-semibold text-brand-ink mb-2">Restore the homepage reels from <code class="text-base bg-brand-sand px-1.5 py-0.5 rounded">data/reels.json</code></h2>
    <p class="text-brand-navy/70 text-sm leading-relaxed mb-6">
      Guest reels live only in the database and <code>/uploads</code>, neither of which is deployed, so the
      homepage strip comes up empty on a fresh server. The reels that ship with the site keep their video
      files in <code>/assets/reels</code>, and this loads their rows back in.
    </p>

    <dl class="grid grid-cols-2 gap-4 mb-6">
      <div class="bg-brand-sand rounded-xl p-4">
        <dt class="text-xs uppercase tracking-wide text-brand-navy/50">On the homepage now</dt>
        <dd class="font-display text-2xl font-semibold text-brand-ink"><?php echo $currentHome; ?></dd>
      </div>
      <div class="bg-brand-sand rounded-xl p-4">
        <dt class="text-xs uppercase tracking-wide text-brand-navy/50">Reels in JSON file</dt>
        <dd class="font-display text-2xl font-semibold text-brand-ink"><?php echo $jsonExists ? $jsonCount : '—'; ?></dd>
      </div>
    </dl>

    <div class="bg-brand-sand border border-brand-navy/10 text-brand-navy/80 text-sm rounded-xl px-4 py-3 mb-6">
      <p class="font-semibold text-brand-ink mb-1">Safe to run as often as you like.</p>
      <ul class="list-disc list-inside space-y-0.5">
        <li>Reels already imported are refreshed in place, never duplicated.</li>
        <li><?php echo $guestReels; ?> guest submission<?php echo $guestReels === 1 ? '' : 's'; ?> in the database — these are left untouched.</li>
        <li>Nothing is deleted.</li>
      </ul>
    </div>

    <?php if ($missing): ?>
    <div class="bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-xl px-4 py-3 mb-6">
      <p class="font-semibold mb-1">
        <?php echo count($missing); ?> video file<?php echo count($missing) === 1 ? ' is' : 's are'; ?> missing from the server.
      </p>
      <p class="text-amber-700 mb-2">These will be skipped — upload the files to <code>/assets/reels</code> and run this again.</p>
      <ul class="list-disc list-inside space-y-0.5 text-amber-700 font-mono text-xs">
        <?php foreach ($missing as $m): ?>
        <li><?php echo e($m); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>

    <?php if (!$jsonExists): ?>
    <p class="text-red-600 text-sm font-medium">data/reels.json was not found on the server. Upload it, then reload this page.</p>
    <?php elseif ($jsonCount === 0): ?>
    <p class="text-red-600 text-sm font-medium">data/reels.json holds no reels.</p>
    <?php else: ?>
    <form method="post">
      <?php echo csrf_field(); ?>
      <button type="submit" class="inline-flex items-center gap-2 bg-brand-gold hover:bg-brand-goldL text-brand-ink font-semibold px-6 py-3 rounded-full transition-colors duration-200 cursor-pointer">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        Import <?php echo $jsonCount - count($missing); ?> reel<?php echo ($jsonCount - count($missing)) === 1 ? '' : 's'; ?> now
      </button>
    </form>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
