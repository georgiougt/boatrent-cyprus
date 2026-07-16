<?php
/**
 * One-click fleet importer (admin only).
 *
 * Reloads the boats table from charter_yachts.json — the same data used to seed
 * a fresh install. Use this on an already-seeded database (e.g. production) where
 * the JSON fleet changed but the database was created before the change, so the
 * first-run seed no longer applies.
 *
 * Boats are fully replaced. Inquiries are preserved (their boat link is cleared
 * by the existing ON DELETE SET NULL rule); admin accounts are untouched.
 */
require_once __DIR__ . '/includes/auth.php';
require_admin();

$jsonPath = __DIR__ . '/../charter_yachts.json';
$jsonExists = is_file($jsonPath);
$currentBoats = (int) db()->query('SELECT COUNT(*) FROM boats')->fetchColumn();

// Count how many vessels the JSON holds, for the confirmation screen.
$jsonCount = 0;
if ($jsonExists) {
    $decoded = json_decode((string) file_get_contents($jsonPath), true);
    $jsonCount = is_array($decoded) ? count($decoded) : 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
    if (!$jsonExists) {
        $_SESSION['admin_flash_error'] = 'charter_yachts.json was not found on the server — upload it and try again.';
        header('Location: /admin/import-fleet.php');
        exit;
    }
    $city   = get_city('limassol');
    $cityId = $city['id'] ?? (int) db()->query('SELECT id FROM cities ORDER BY id LIMIT 1')->fetchColumn();
    $imported = insert_charter_yachts(db(), $jsonPath, (int) $cityId);
    $_SESSION['admin_flash'] = "Fleet imported — {$imported} boats loaded from charter_yachts.json.";
    header('Location: /admin/boats.php');
    exit;
}

$flashError = $_SESSION['admin_flash_error'] ?? null;
unset($_SESSION['admin_flash_error']);

$adminTitle = 'Import Fleet';
include __DIR__ . '/includes/admin-header.php';
?>

<div class="max-w-2xl">
  <?php if ($flashError): ?>
  <div class="mb-6 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3"><?php echo e($flashError); ?></div>
  <?php endif; ?>

  <div class="bg-white rounded-2xl border border-brand-navy/10 shadow-sm p-6 sm:p-8">
    <h2 class="font-display text-2xl font-semibold text-brand-ink mb-2">Reload the fleet from <code class="text-base bg-brand-sand px-1.5 py-0.5 rounded">charter_yachts.json</code></h2>
    <p class="text-brand-navy/70 text-sm leading-relaxed mb-6">
      Use this after updating <code>charter_yachts.json</code> on a database that was already seeded
      (the automatic first-run seed only applies to a brand-new database). It replaces the boats table
      with the vessels defined in the JSON file.
    </p>

    <dl class="grid grid-cols-2 gap-4 mb-6">
      <div class="bg-brand-sand rounded-xl p-4">
        <dt class="text-xs uppercase tracking-wide text-brand-navy/50">Boats in database now</dt>
        <dd class="font-display text-2xl font-semibold text-brand-ink"><?php echo $currentBoats; ?></dd>
      </div>
      <div class="bg-brand-sand rounded-xl p-4">
        <dt class="text-xs uppercase tracking-wide text-brand-navy/50">Vessels in JSON file</dt>
        <dd class="font-display text-2xl font-semibold text-brand-ink"><?php echo $jsonExists ? $jsonCount : '—'; ?></dd>
      </div>
    </dl>

    <div class="bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-xl px-4 py-3 mb-6">
      <p class="font-semibold mb-1">This replaces every boat.</p>
      <ul class="list-disc list-inside space-y-0.5 text-amber-700">
        <li>All current boats are removed and re-created from the JSON.</li>
        <li>Existing inquiries are kept (their boat link is cleared).</li>
        <li>Your admin login is unaffected.</li>
      </ul>
    </div>

    <?php if (!$jsonExists): ?>
    <p class="text-red-600 text-sm font-medium">charter_yachts.json was not found on the server. Upload it to the site root, then reload this page.</p>
    <?php else: ?>
    <form method="post" data-confirm="Replace all boats with the <?php echo $jsonCount; ?> vessels from charter_yachts.json? This cannot be undone.">
      <?php echo csrf_field(); ?>
      <button type="submit" class="inline-flex items-center gap-2 bg-brand-gold hover:bg-brand-goldL text-brand-ink font-semibold px-6 py-3 rounded-full transition-colors duration-200 cursor-pointer">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        Import <?php echo $jsonCount; ?> boats now
      </button>
    </form>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
