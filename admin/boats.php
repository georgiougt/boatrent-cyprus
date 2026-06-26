<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();
$adminTitle = 'Boats';

$flash = $_SESSION['admin_flash'] ?? null;
unset($_SESSION['admin_flash']);

$boats = db()->query("
    SELECT b.*, c.name AS city_name
    FROM boats b JOIN cities c ON c.id = b.city_id
    ORDER BY b.created_at DESC
")->fetchAll();

include __DIR__ . '/includes/admin-header.php';
?>

<?php if ($flash): ?>
<div class="mb-6 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3 flex items-center gap-2">
  <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
  <?php echo e($flash); ?>
</div>
<?php endif; ?>

<div class="bg-white rounded-2xl border border-brand-navy/10 overflow-hidden">
  <div class="flex items-center justify-between px-6 py-4 border-b border-brand-navy/10">
    <h2 class="font-display text-lg font-semibold text-brand-ink"><?php echo count($boats); ?> Boats</h2>
    <a href="/admin/boat-edit.php" class="inline-flex items-center gap-2 bg-brand-gold hover:bg-brand-goldL text-brand-ink text-sm font-semibold px-4 py-2 rounded-full transition-colors duration-200 cursor-pointer">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
      Add Boat
    </a>
  </div>

  <?php if ($boats): ?>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="text-left text-brand-navy/50 border-b border-brand-navy/10">
          <th class="px-6 py-3 font-medium">Vessel</th>
          <th class="px-6 py-3 font-medium hidden sm:table-cell">Destination</th>
          <th class="px-6 py-3 font-medium hidden md:table-cell">Type</th>
          <th class="px-6 py-3 font-medium hidden md:table-cell">Guests</th>
          <th class="px-6 py-3 font-medium">Price/day</th>
          <th class="px-6 py-3 font-medium">Status</th>
          <th class="px-6 py-3 font-medium text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-brand-navy/5">
        <?php foreach ($boats as $b): ?>
        <tr class="hover:bg-brand-sand/50 transition-colors duration-150">
          <td class="px-6 py-3">
            <div class="flex items-center gap-3">
              <img src="<?php echo e($b['image_url']); ?>" alt="" class="h-11 w-14 rounded-lg object-cover shrink-0">
              <div class="min-w-0">
                <p class="font-medium text-brand-ink truncate"><?php echo e($b['name']); ?></p>
                <?php if ($b['featured']): ?><span class="text-xs text-brand-gold font-medium">★ Featured</span><?php endif; ?>
              </div>
            </div>
          </td>
          <td class="px-6 py-3 text-brand-navy/70 hidden sm:table-cell"><?php echo e($b['city_name']); ?></td>
          <td class="px-6 py-3 text-brand-navy/70 hidden md:table-cell"><?php echo e($b['type']); ?></td>
          <td class="px-6 py-3 text-brand-navy/70 hidden md:table-cell"><?php echo (int) $b['capacity']; ?></td>
          <td class="px-6 py-3 font-medium text-brand-ink"><?php echo money($b['price_day']); ?></td>
          <td class="px-6 py-3">
            <?php if ($b['status'] === 'active'): ?>
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-green-100 text-green-700"><span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>Active</span>
            <?php else: ?>
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-brand-navy/10 text-brand-navy/60"><span class="h-1.5 w-1.5 rounded-full bg-brand-navy/40"></span>Hidden</span>
            <?php endif; ?>
          </td>
          <td class="px-6 py-3">
            <div class="flex items-center justify-end gap-1">
              <a href="/boat.php?id=<?php echo (int) $b['id']; ?>" target="_blank" rel="noopener" aria-label="Preview" class="h-9 w-9 flex items-center justify-center rounded-lg text-brand-navy/60 hover:bg-brand-foam hover:text-brand-aquaD transition-colors duration-200 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.46 12C3.73 7.94 7.52 5 12 5s8.27 2.94 9.54 7c-1.27 4.06-5.06 7-9.54 7s-8.27-2.94-9.54-7z"/></svg>
              </a>
              <a href="/admin/boat-edit.php?id=<?php echo (int) $b['id']; ?>" aria-label="Edit" class="h-9 w-9 flex items-center justify-center rounded-lg text-brand-navy/60 hover:bg-brand-foam hover:text-brand-aquaD transition-colors duration-200 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.42-9.42a2 2 0 112.84 2.84L11.83 15.17 8 16l.83-3.83 9.75-9.59z"/></svg>
              </a>
              <form action="/admin/boat-delete.php" method="post" data-confirm="Delete &quot;<?php echo e($b['name']); ?>&quot;? This cannot be undone." class="inline">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id" value="<?php echo (int) $b['id']; ?>">
                <button type="submit" aria-label="Delete" class="h-9 w-9 flex items-center justify-center rounded-lg text-brand-navy/60 hover:bg-red-50 hover:text-red-600 transition-colors duration-200 cursor-pointer">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.87 12.14A2 2 0 0116.14 21H7.86a2 2 0 01-1.99-1.86L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/></svg>
                </button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <p class="px-6 py-12 text-center text-brand-navy/50">No boats yet. <a href="/admin/boat-edit.php" class="text-brand-aquaD font-medium cursor-pointer">Add your first boat</a>.</p>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
