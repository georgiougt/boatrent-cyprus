<?php
$pageTitle = 'Charter a Yacht or Boat in Cyprus';
$pageDescription = 'Charter a yacht or rent a boat anywhere in Cyprus. Browse luxury yachts, catamarans, sailing boats and speedboats for hire in Limassol, Paphos, Larnaca, Ayia Napa, Protaras and Latsi.';
$pageKeywords = 'charter yacht cyprus, yacht charter cyprus, rent boat cyprus, rent yacht cyprus, catamaran charter cyprus, speedboat rental cyprus, boat hire cyprus, yacht rental cyprus';
require_once __DIR__ . '/includes/functions.php';

$cities = get_cities();
$types  = get_boat_types();

// Resolve city slug -> id
$cityId = null;
$citySlug = $_GET['city'] ?? '';
if ($citySlug) {
    $city = get_city($citySlug);
    if ($city) {
        $cityId = (int) $city['id'];
    }
}

$filters = [
    'city_id'   => $cityId,
    'type'      => $_GET['type'] ?? '',
    'guests'    => $_GET['guests'] ?? '',
    'max_price' => $_GET['max_price'] ?? '',
    'sort'      => $_GET['sort'] ?? '',
    'q'         => trim($_GET['q'] ?? ''),
];
$boats = get_boats($filters);

$canonical = base_url() . '/boats';
include __DIR__ . '/includes/header.php';
?>

<section class="bg-brand-ink pt-32 pb-12 px-6">
  <div class="max-w-7xl mx-auto">
    <p class="reveal text-brand-goldL font-semibold uppercase tracking-[0.3em] text-xs mb-3">The fleet</p>
    <h1 class="reveal font-display text-4xl sm:text-5xl font-bold text-white">Charter a Yacht or Rent a Boat in Cyprus</h1>
    <p class="reveal text-white/65 mt-3 max-w-2xl">Filter across every town in Cyprus to find the right yacht, catamaran or speedboat to charter for your group, your dates and your budget.</p>
  </div>
</section>

<section class="bg-brand-sand py-10 px-6">
  <div class="max-w-7xl mx-auto">
    <!-- Filter bar -->
    <form method="get" action="/boats" class="bg-white rounded-2xl border border-brand-navy/10 shadow-sm p-4 sm:p-5 grid grid-cols-2 lg:grid-cols-6 gap-3 mb-8">
      <div class="col-span-2 lg:col-span-1">
        <label for="f-q" class="block text-xs font-medium text-brand-navy/60 mb-1">Search</label>
        <input id="f-q" name="q" type="text" value="<?php echo e($filters['q']); ?>" placeholder="Name or type" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-3 py-2.5 text-sm text-brand-ink focus:outline-none focus:ring-2 focus:ring-brand-aqua">
      </div>
      <div>
        <label for="f-city" class="block text-xs font-medium text-brand-navy/60 mb-1">Destination</label>
        <select id="f-city" name="city" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-3 py-2.5 text-sm text-brand-ink focus:outline-none focus:ring-2 focus:ring-brand-aqua cursor-pointer">
          <option value="">All towns</option>
          <?php foreach ($cities as $c): ?>
          <option value="<?php echo e($c['slug']); ?>" <?php echo $citySlug === $c['slug'] ? 'selected' : ''; ?>><?php echo e($c['name']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label for="f-type" class="block text-xs font-medium text-brand-navy/60 mb-1">Vessel type</label>
        <select id="f-type" name="type" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-3 py-2.5 text-sm text-brand-ink focus:outline-none focus:ring-2 focus:ring-brand-aqua cursor-pointer">
          <option value="">Any type</option>
          <?php foreach ($types as $t): ?>
          <option value="<?php echo e($t); ?>" <?php echo $filters['type'] === $t ? 'selected' : ''; ?>><?php echo e($t); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label for="f-guests" class="block text-xs font-medium text-brand-navy/60 mb-1">Min. guests</label>
        <select id="f-guests" name="guests" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-3 py-2.5 text-sm text-brand-ink focus:outline-none focus:ring-2 focus:ring-brand-aqua cursor-pointer">
          <option value="">Any size</option>
          <?php foreach ([2,4,6,8,10,12,16] as $g): ?>
          <option value="<?php echo $g; ?>" <?php echo (string) $filters['guests'] === (string) $g ? 'selected' : ''; ?>><?php echo $g; ?>+ guests</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label for="f-sort" class="block text-xs font-medium text-brand-navy/60 mb-1">Sort by</label>
        <select id="f-sort" name="sort" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-3 py-2.5 text-sm text-brand-ink focus:outline-none focus:ring-2 focus:ring-brand-aqua cursor-pointer">
          <option value="">Recommended</option>
          <option value="price_asc"  <?php echo $filters['sort'] === 'price_asc' ? 'selected' : ''; ?>>Price: low to high</option>
          <option value="price_desc" <?php echo $filters['sort'] === 'price_desc' ? 'selected' : ''; ?>>Price: high to low</option>
          <option value="capacity"   <?php echo $filters['sort'] === 'capacity' ? 'selected' : ''; ?>>Largest capacity</option>
        </select>
      </div>
      <div class="col-span-2 lg:col-span-6 flex gap-3">
        <button type="submit" class="inline-flex items-center justify-center gap-2 bg-brand-navy hover:bg-brand-ink text-white font-semibold text-sm px-6 py-2.5 rounded-full transition-colors duration-200 cursor-pointer">
          Apply filters
        </button>
        <a href="/boats" class="inline-flex items-center justify-center gap-2 text-brand-navy/70 hover:text-brand-ink font-medium text-sm px-4 py-2.5 rounded-full transition-colors duration-200 cursor-pointer">Reset</a>
      </div>
    </form>

    <div class="flex items-center justify-between mb-6">
      <p class="text-brand-navy/70 text-sm"><span class="font-semibold text-brand-ink"><?php echo count($boats); ?></span> vessel<?php echo count($boats) === 1 ? '' : 's'; ?> found</p>
    </div>

    <?php if ($boats): ?>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-7">
      <?php foreach ($boats as $boat) { include __DIR__ . '/includes/boat-card.php'; } ?>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-2xl border border-brand-navy/10 p-14 text-center">
      <div class="mx-auto h-14 w-14 rounded-full bg-brand-foam flex items-center justify-center text-brand-aqua mb-4">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
      </div>
      <h3 class="font-display text-xl font-semibold text-brand-ink mb-2">No vessels match those filters</h3>
      <p class="text-brand-navy/60 mb-6">Try widening your search or resetting the filters.</p>
      <a href="/boats" class="inline-flex items-center gap-2 bg-brand-gold hover:bg-brand-goldL text-brand-ink font-semibold px-6 py-3 rounded-full transition-colors duration-200 cursor-pointer">Reset filters</a>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
