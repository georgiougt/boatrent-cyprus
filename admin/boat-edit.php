<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();

$pdo = db();
$cities = get_cities();
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$isEdit = $id > 0;

$errors = [];
$boat = [
    'name' => '', 'city_id' => $cities[0]['id'] ?? 1, 'type' => 'Luxury Yacht',
    'capacity' => 8, 'length_m' => '', 'cabins' => 0, 'year' => date('Y'),
    'crewed' => 1, 'price_hour' => '', 'price_day' => '', 'description' => '',
    'features' => '', 'image_url' => '', 'gallery' => '', 'featured' => 0, 'status' => 'active',
];

if ($isEdit) {
    $stmt = $pdo->prepare('SELECT * FROM boats WHERE id = ?');
    $stmt->execute([$id]);
    $existing = $stmt->fetch();
    if (!$existing) {
        $_SESSION['admin_flash'] = 'That boat no longer exists.';
        header('Location: /admin/boats.php');
        exit;
    }
    $boat = $existing;
    // features stored as JSON -> textarea lines
    $boat['features'] = implode("\n", json_arr($existing['features']));
    $boat['gallery']  = implode("\n", json_arr($existing['gallery']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        $errors[] = 'Session expired, please try again.';
    }

    // Collect
    $boat['name']        = trim($_POST['name'] ?? '');
    $boat['city_id']     = (int) ($_POST['city_id'] ?? 0);
    $boat['type']        = trim($_POST['type'] ?? '');
    $boat['capacity']    = (int) ($_POST['capacity'] ?? 0);
    $boat['length_m']    = $_POST['length_m'] !== '' ? (float) $_POST['length_m'] : null;
    $boat['cabins']      = (int) ($_POST['cabins'] ?? 0);
    $boat['year']        = $_POST['year'] !== '' ? (int) $_POST['year'] : null;
    $boat['crewed']      = isset($_POST['crewed']) ? 1 : 0;
    $boat['price_hour']  = $_POST['price_hour'] !== '' ? (float) $_POST['price_hour'] : null;
    $boat['price_day']   = (float) ($_POST['price_day'] ?? 0);
    $boat['description'] = trim($_POST['description'] ?? '');
    $boat['features']    = trim($_POST['features'] ?? '');
    $boat['image_url']   = trim($_POST['image_url'] ?? '');
    $boat['gallery']     = trim($_POST['gallery'] ?? '');
    $boat['featured']    = isset($_POST['featured']) ? 1 : 0;
    $boat['status']      = ($_POST['status'] ?? 'active') === 'hidden' ? 'hidden' : 'active';

    // Validate
    if ($boat['name'] === '')                 $errors[] = 'Boat name is required.';
    if (!$boat['city_id'])                     $errors[] = 'Please choose a destination.';
    if ($boat['type'] === '')                  $errors[] = 'Vessel type is required.';
    if ($boat['capacity'] < 1)                 $errors[] = 'Capacity must be at least 1.';
    if ($boat['price_day'] <= 0)               $errors[] = 'Daily price must be greater than 0.';
    if ($boat['image_url'] === '')             $errors[] = 'A main image URL is required.';

    if (!$errors) {
        // Normalise list fields to JSON
        $featuresArr = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $boat['features']))));
        $galleryArr  = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $boat['gallery']))));
        $featuresJson = json_encode($featuresArr);
        $galleryJson  = json_encode($galleryArr);

        if ($isEdit) {
            $stmt = $pdo->prepare("
                UPDATE boats SET
                    name=:name, city_id=:city_id, type=:type, capacity=:capacity, length_m=:length_m,
                    cabins=:cabins, year=:year, crewed=:crewed, price_hour=:price_hour, price_day=:price_day,
                    description=:description, features=:features, image_url=:image_url, gallery=:gallery,
                    featured=:featured, status=:status
                WHERE id=:id
            ");
            $params = [':id' => $id];
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO boats
                    (name, slug, city_id, type, capacity, length_m, cabins, year, crewed, price_hour, price_day, description, features, image_url, gallery, featured, status)
                VALUES
                    (:name, :slug, :city_id, :type, :capacity, :length_m, :cabins, :year, :crewed, :price_hour, :price_day, :description, :features, :image_url, :gallery, :featured, :status)
            ");
            // unique slug
            $base = slugify($boat['name']) ?: 'boat';
            $slug = $base; $n = 2;
            $check = $pdo->prepare('SELECT COUNT(*) FROM boats WHERE slug = ?');
            $check->execute([$slug]);
            while ((int) $check->fetchColumn() > 0) {
                $slug = $base . '-' . $n++;
                $check->execute([$slug]);
            }
            $params = [':slug' => $slug];
        }

        $stmt->execute(array_merge($params, [
            ':name' => $boat['name'], ':city_id' => $boat['city_id'], ':type' => $boat['type'],
            ':capacity' => $boat['capacity'], ':length_m' => $boat['length_m'], ':cabins' => $boat['cabins'],
            ':year' => $boat['year'], ':crewed' => $boat['crewed'], ':price_hour' => $boat['price_hour'],
            ':price_day' => $boat['price_day'], ':description' => $boat['description'],
            ':features' => $featuresJson, ':image_url' => $boat['image_url'], ':gallery' => $galleryJson,
            ':featured' => $boat['featured'], ':status' => $boat['status'],
        ]));

        $_SESSION['admin_flash'] = $isEdit ? 'Boat updated successfully.' : 'New boat added successfully.';
        header('Location: /admin/boats.php');
        exit;
    }
}

$adminTitle = $isEdit ? 'Edit Boat' : 'Add Boat';
$typeOptions = ['Luxury Yacht', 'Motor Yacht', 'Catamaran', 'Sailing Yacht', 'Speedboat', 'Fishing Boat'];
include __DIR__ . '/includes/admin-header.php';
?>

<div class="max-w-4xl">
  <a href="/admin/boats.php" class="inline-flex items-center gap-2 text-brand-navy/60 hover:text-brand-ink text-sm font-medium mb-5 cursor-pointer">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
    Back to boats
  </a>

  <?php if ($errors): ?>
  <div class="mb-6 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">
    <ul class="list-disc list-inside space-y-0.5">
      <?php foreach ($errors as $err): ?><li><?php echo e($err); ?></li><?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>

  <form method="post" class="space-y-6">
    <?php echo csrf_field(); ?>

    <div class="bg-white rounded-2xl border border-brand-navy/10 p-6 sm:p-8 space-y-5">
      <h2 class="font-display text-lg font-semibold text-brand-ink">Basics</h2>
      <div class="grid sm:grid-cols-2 gap-5">
        <div class="sm:col-span-2">
          <label for="name" class="block text-sm font-medium text-brand-navy/70 mb-1.5">Boat name *</label>
          <input id="name" name="name" type="text" required value="<?php echo e($boat['name']); ?>" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua">
        </div>
        <div>
          <label for="city_id" class="block text-sm font-medium text-brand-navy/70 mb-1.5">Destination *</label>
          <select id="city_id" name="city_id" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua cursor-pointer">
            <?php foreach ($cities as $c): ?>
            <option value="<?php echo (int) $c['id']; ?>" <?php echo (int) $boat['city_id'] === (int) $c['id'] ? 'selected' : ''; ?>><?php echo e($c['name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label for="type" class="block text-sm font-medium text-brand-navy/70 mb-1.5">Vessel type *</label>
          <input id="type" name="type" list="typelist" value="<?php echo e($boat['type']); ?>" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua">
          <datalist id="typelist"><?php foreach ($typeOptions as $t): ?><option value="<?php echo e($t); ?>"></option><?php endforeach; ?></datalist>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-brand-navy/10 p-6 sm:p-8 space-y-5">
      <h2 class="font-display text-lg font-semibold text-brand-ink">Specifications</h2>
      <div class="grid grid-cols-2 sm:grid-cols-3 gap-5">
        <div>
          <label for="capacity" class="block text-sm font-medium text-brand-navy/70 mb-1.5">Guests *</label>
          <input id="capacity" name="capacity" type="number" min="1" required value="<?php echo e((string) $boat['capacity']); ?>" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua">
        </div>
        <div>
          <label for="length_m" class="block text-sm font-medium text-brand-navy/70 mb-1.5">Length (m)</label>
          <input id="length_m" name="length_m" type="number" step="0.1" min="0" value="<?php echo e((string) $boat['length_m']); ?>" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua">
        </div>
        <div>
          <label for="cabins" class="block text-sm font-medium text-brand-navy/70 mb-1.5">Cabins</label>
          <input id="cabins" name="cabins" type="number" min="0" value="<?php echo e((string) $boat['cabins']); ?>" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua">
        </div>
        <div>
          <label for="year" class="block text-sm font-medium text-brand-navy/70 mb-1.5">Year</label>
          <input id="year" name="year" type="number" min="1950" max="<?php echo date('Y') + 1; ?>" value="<?php echo e((string) $boat['year']); ?>" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua">
        </div>
        <div class="col-span-2 sm:col-span-2 flex items-end">
          <label class="flex items-center gap-3 cursor-pointer">
            <input type="checkbox" name="crewed" value="1" <?php echo $boat['crewed'] ? 'checked' : ''; ?> class="h-5 w-5 rounded border-brand-navy/20 text-brand-aqua focus:ring-brand-aqua cursor-pointer">
            <span class="text-sm text-brand-navy/70">Crew / skipper included</span>
          </label>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-brand-navy/10 p-6 sm:p-8 space-y-5">
      <h2 class="font-display text-lg font-semibold text-brand-ink">Pricing</h2>
      <div class="grid sm:grid-cols-2 gap-5">
        <div>
          <label for="price_day" class="block text-sm font-medium text-brand-navy/70 mb-1.5">Price per day (€) *</label>
          <input id="price_day" name="price_day" type="number" step="1" min="0" required value="<?php echo e((string) $boat['price_day']); ?>" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua">
        </div>
        <div>
          <label for="price_hour" class="block text-sm font-medium text-brand-navy/70 mb-1.5">Price per hour (€)</label>
          <input id="price_hour" name="price_hour" type="number" step="1" min="0" value="<?php echo e((string) $boat['price_hour']); ?>" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua">
        </div>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-brand-navy/10 p-6 sm:p-8 space-y-5">
      <h2 class="font-display text-lg font-semibold text-brand-ink">Content &amp; media</h2>
      <div>
        <label for="description" class="block text-sm font-medium text-brand-navy/70 mb-1.5">Description</label>
        <textarea id="description" name="description" rows="4" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua resize-none"><?php echo e($boat['description']); ?></textarea>
      </div>
      <div>
        <label for="features" class="block text-sm font-medium text-brand-navy/70 mb-1.5">Features <span class="text-brand-navy/40">(one per line)</span></label>
        <textarea id="features" name="features" rows="4" placeholder="Captain &amp; crew&#10;Snorkelling gear&#10;Bluetooth audio" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua resize-none"><?php echo e($boat['features']); ?></textarea>
      </div>
      <div>
        <label for="image_url" class="block text-sm font-medium text-brand-navy/70 mb-1.5">Main image URL *</label>
        <input id="image_url" name="image_url" type="url" required value="<?php echo e($boat['image_url']); ?>" placeholder="https://…" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua">
      </div>
      <div>
        <label for="gallery" class="block text-sm font-medium text-brand-navy/70 mb-1.5">Gallery image URLs <span class="text-brand-navy/40">(one per line)</span></label>
        <textarea id="gallery" name="gallery" rows="3" placeholder="https://…&#10;https://…" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua resize-none"><?php echo e($boat['gallery']); ?></textarea>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-brand-navy/10 p-6 sm:p-8 space-y-5">
      <h2 class="font-display text-lg font-semibold text-brand-ink">Visibility</h2>
      <div class="flex flex-wrap gap-6">
        <label class="flex items-center gap-3 cursor-pointer">
          <input type="checkbox" name="featured" value="1" <?php echo $boat['featured'] ? 'checked' : ''; ?> class="h-5 w-5 rounded border-brand-navy/20 text-brand-gold focus:ring-brand-gold cursor-pointer">
          <span class="text-sm text-brand-navy/70">Show in <strong>Featured</strong> on homepage</span>
        </label>
        <div>
          <span class="block text-sm font-medium text-brand-navy/70 mb-1.5">Status</span>
          <div class="flex gap-2">
            <label class="cursor-pointer"><input type="radio" name="status" value="active" class="peer sr-only" <?php echo $boat['status'] === 'active' ? 'checked' : ''; ?>><span class="inline-block text-sm px-4 py-2 rounded-full border border-brand-navy/15 peer-checked:bg-brand-aqua peer-checked:text-white peer-checked:border-brand-aqua transition-colors">Active</span></label>
            <label class="cursor-pointer"><input type="radio" name="status" value="hidden" class="peer sr-only" <?php echo $boat['status'] === 'hidden' ? 'checked' : ''; ?>><span class="inline-block text-sm px-4 py-2 rounded-full border border-brand-navy/15 peer-checked:bg-brand-navy peer-checked:text-white peer-checked:border-brand-navy transition-colors">Hidden</span></label>
          </div>
        </div>
      </div>
    </div>

    <div class="flex items-center gap-3">
      <button type="submit" class="inline-flex items-center gap-2 bg-brand-gold hover:bg-brand-goldL text-brand-ink font-semibold px-7 py-3 rounded-full transition-colors duration-200 cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        <?php echo $isEdit ? 'Save Changes' : 'Create Boat'; ?>
      </button>
      <a href="/admin/boats.php" class="px-5 py-3 text-brand-navy/60 hover:text-brand-ink font-medium transition-colors duration-200 cursor-pointer">Cancel</a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
