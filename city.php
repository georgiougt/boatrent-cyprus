<?php
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['slug'] ?? '';
$city = get_city($slug);
if (!$city) {
    http_response_code(404);
    $pageTitle = 'Destination not found';
    include __DIR__ . '/includes/header.php';
    echo '<section class="pt-40 pb-32 px-6 text-center"><h1 class="font-display text-4xl font-bold text-brand-ink mb-4">Destination not found</h1><p class="text-brand-navy/60 mb-8">We don\'t have that town listed.</p><a href="/" class="inline-flex items-center gap-2 bg-brand-gold text-brand-ink font-semibold px-6 py-3 rounded-full cursor-pointer">Back home</a></section>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$cityName = $city['name'];
$pageTitle = 'Rent a Boat or Yacht in ' . $cityName;
$pageDescription = 'Rent a boat or yacht in ' . $cityName . ', Cyprus. Charter luxury yachts, catamarans, sailing boats and speedboats in ' . $cityName . ' — ' . rtrim($city['tagline'], '.') . '. Browse the fleet and inquire today.';
$pageKeywords = strtolower(
    'rent boat ' . $cityName . ', rent yacht ' . $cityName . ', charter yacht ' . $cityName . ', ' .
    $cityName . ' yacht charter, ' . $cityName . ' boat hire, boat rental ' . $cityName . ' cyprus, yacht hire ' . $cityName
);
$pageImage = $city['image_url'];
$canonical = base_url() . '/' . $city['slug'];
$structuredData = [
    '@context' => 'https://schema.org',
    '@type'    => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => base_url() . '/'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Fleet', 'item' => base_url() . '/boats'],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $city['name'], 'item' => base_url() . '/' . $city['slug']],
    ],
];

$filters = [
    'city_id' => (int) $city['id'],
    'type'    => $_GET['type'] ?? '',
    'sort'    => $_GET['sort'] ?? '',
];
$boats = get_boats($filters);
$types = db()->prepare("SELECT DISTINCT type FROM boats WHERE city_id = ? AND status='active' ORDER BY type");
$types->execute([(int) $city['id']]);
$cityTypes = $types->fetchAll(PDO::FETCH_COLUMN);

// Stats for the SEO bento (all active boats in this city, unfiltered)
$cityAllBoats  = get_boats(['city_id' => (int) $city['id']]);
$cityBoatCount = count($cityAllBoats);
$cityDayPrices = array_filter(array_map(fn($b) => (float) $b['price_day'], $cityAllBoats), fn($p) => $p > 0);
$cityPriceFrom = $cityDayPrices ? min($cityDayPrices) : 0;
$citySeo       = get_city_seo()[$city['slug']] ?? null;

include __DIR__ . '/includes/header.php';
?>

<!-- CITY HERO -->
<section class="relative h-[60vh] min-h-[420px] flex items-end overflow-hidden">
  <img src="<?php echo e($city['image_url']); ?>" alt="<?php echo e($city['name']); ?> coastline, Cyprus" class="absolute inset-0 w-full h-full object-cover">
  <div class="absolute inset-0 hero-overlay"></div>
  <div class="relative z-10 max-w-7xl mx-auto px-6 pb-12 w-full">
    <nav class="reveal text-white/70 text-sm mb-4 flex items-center gap-2" aria-label="Breadcrumb">
      <a href="/" class="hover:text-brand-gold cursor-pointer">Home</a>
      <span>/</span>
      <a href="/boats" class="hover:text-brand-gold cursor-pointer">Fleet</a>
      <span>/</span>
      <span class="text-white"><?php echo e($city['name']); ?></span>
    </nav>
    <p class="reveal text-brand-goldL font-medium uppercase tracking-[0.3em] text-xs mb-3"><?php echo e($city['tagline']); ?></p>
    <h1 class="reveal font-display text-4xl sm:text-6xl font-bold text-white">Rent a Boat or Yacht in <?php echo e($city['name']); ?></h1>
  </div>
</section>

<!-- INTRO -->
<section class="bg-brand-sand py-12 px-6">
  <div class="max-w-3xl mx-auto text-center reveal">
    <p class="text-brand-navy/70 text-lg leading-relaxed"><span class="text-brand-ink font-medium">Rent a yacht, catamaran or speedboat in <?php echo e($city['name']); ?>, Cyprus.</span> <?php echo e($city['description']); ?></p>
  </div>
</section>

<!-- LISTINGS -->
<section class="bg-brand-sand pb-20 px-6">
  <div class="max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <p class="text-brand-navy/70 text-sm"><span class="font-semibold text-brand-ink"><?php echo count($boats); ?></span> vessel<?php echo count($boats) === 1 ? '' : 's'; ?> in <?php echo e($city['name']); ?></p>
      <form method="get" action="/<?php echo e($city['slug']); ?>" class="flex items-center gap-2">
        <label for="ct" class="sr-only">Filter by type</label>
        <select id="ct" name="type" onchange="this.form.submit()" class="bg-white border border-brand-navy/10 rounded-full px-4 py-2 text-sm text-brand-ink focus:outline-none focus:ring-2 focus:ring-brand-aqua cursor-pointer">
          <option value="">All vessel types</option>
          <?php foreach ($cityTypes as $t): ?>
          <option value="<?php echo e($t); ?>" <?php echo ($filters['type'] === $t) ? 'selected' : ''; ?>><?php echo e($t); ?></option>
          <?php endforeach; ?>
        </select>
        <label for="cs" class="sr-only">Sort</label>
        <select id="cs" name="sort" onchange="this.form.submit()" class="bg-white border border-brand-navy/10 rounded-full px-4 py-2 text-sm text-brand-ink focus:outline-none focus:ring-2 focus:ring-brand-aqua cursor-pointer">
          <option value="">Recommended</option>
          <option value="price_asc"  <?php echo $filters['sort'] === 'price_asc' ? 'selected' : ''; ?>>Price ↑</option>
          <option value="price_desc" <?php echo $filters['sort'] === 'price_desc' ? 'selected' : ''; ?>>Price ↓</option>
          <option value="capacity"   <?php echo $filters['sort'] === 'capacity' ? 'selected' : ''; ?>>Capacity</option>
        </select>
      </form>
    </div>

    <?php if ($boats): ?>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-7 reveal-stagger">
      <?php foreach ($boats as $boat) { include __DIR__ . '/includes/boat-card.php'; } ?>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-2xl border border-brand-navy/10 p-14 text-center">
      <h3 class="font-display text-xl font-semibold text-brand-ink mb-2">No vessels here yet</h3>
      <p class="text-brand-navy/60 mb-6">Try another destination or browse the full fleet.</p>
      <a href="/boats" class="inline-flex items-center gap-2 bg-brand-gold hover:bg-brand-goldL text-brand-ink font-semibold px-6 py-3 rounded-full cursor-pointer">Browse all boats</a>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php if ($citySeo): ?>
<!-- SEO BENTO -->
<section class="bg-white py-20 px-6">
  <div class="max-w-7xl mx-auto">
    <div class="reveal max-w-2xl mb-10">
      <p class="text-brand-aquaD font-semibold uppercase tracking-[0.3em] text-xs mb-3">Why <?php echo e($city['name']); ?></p>
      <h2 class="font-display text-3xl sm:text-4xl font-bold text-brand-ink">Renting a Boat in <?php echo e($city['name']); ?>, Cyprus</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-5 reveal-stagger">

      <!-- Big image tile -->
      <div class="relative md:col-span-2 md:row-span-2 rounded-3xl overflow-hidden min-h-[300px] group">
        <img src="<?php echo e($citySeo['image']); ?>" alt="Rent a boat or yacht in <?php echo e($city['name']); ?>, Cyprus" loading="lazy" class="card-img absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-brand-ink/90 via-brand-ink/25 to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 p-7">
          <span class="inline-block text-brand-goldL text-xs font-semibold uppercase tracking-[0.2em] mb-2"><?php echo e($citySeo['best_for']); ?></span>
          <h3 class="font-display text-2xl sm:text-3xl font-bold text-white">Rent a yacht or boat in <?php echo e($city['name']); ?></h3>
        </div>
      </div>

      <!-- Intro text tile -->
      <div class="rounded-3xl bg-brand-sand border border-brand-navy/10 p-6 sm:p-7">
        <p class="text-brand-navy/75 leading-relaxed text-sm"><?php echo e($citySeo['intro']); ?></p>
      </div>

      <!-- Live stats tile -->
      <div class="rounded-3xl bg-brand-ink text-white p-6 sm:p-7 flex flex-col justify-center relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-brand-aqua/15 rounded-full blur-2xl"></div>
        <div class="relative grid grid-cols-2 gap-4">
          <div>
            <p class="font-display text-3xl font-bold"><?php echo (int) $cityBoatCount; ?></p>
            <p class="text-white/55 text-xs uppercase tracking-wide mt-1">Boats available</p>
          </div>
          <div>
            <p class="font-display text-3xl font-bold"><?php echo money($cityPriceFrom); ?></p>
            <p class="text-white/55 text-xs uppercase tracking-wide mt-1">From / day</p>
          </div>
        </div>
        <a href="/boats?city=<?php echo e($city['slug']); ?>" class="relative inline-flex items-center gap-1.5 text-brand-goldL hover:text-white font-semibold text-sm mt-5 transition-colors duration-200 cursor-pointer group/cta">
          See all <?php echo e($city['name']); ?> boats
          <svg class="w-4 h-4 group-hover/cta:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
      </div>

      <!-- Highlights tile -->
      <div class="md:col-span-2 rounded-3xl bg-brand-foam border border-brand-navy/10 p-6 sm:p-7">
        <h3 class="font-display text-lg font-semibold text-brand-ink mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-brand-aqua" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          Where you'll sail near <?php echo e($city['name']); ?>
        </h3>
        <ul class="grid sm:grid-cols-2 gap-x-6 gap-y-3">
          <?php foreach ($citySeo['highlights'] as $h): ?>
          <li class="flex items-center gap-2.5 text-brand-navy/75 text-sm">
            <span class="h-5 w-5 rounded-full bg-brand-aqua/15 text-brand-aqua flex items-center justify-center shrink-0">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </span>
            <?php echo e($h); ?>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <!-- Good to know tile -->
      <div class="rounded-3xl bg-white border border-brand-navy/10 p-6 sm:p-7">
        <h3 class="font-display text-lg font-semibold text-brand-ink mb-4">Good to know</h3>
        <dl class="space-y-4 text-sm">
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-brand-aqua shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <div><dt class="text-brand-navy/50 text-xs uppercase tracking-wide">Departs from</dt><dd class="text-brand-ink font-medium"><?php echo e($citySeo['departure']); ?></dd></div>
          </div>
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-brand-aqua shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.539-1.118l1.519-4.674a1 1 0 00-.363-1.118L2.075 9.8c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.673z"/></svg>
            <div><dt class="text-brand-navy/50 text-xs uppercase tracking-wide">Best for</dt><dd class="text-brand-ink font-medium"><?php echo e($citySeo['best_for']); ?></dd></div>
          </div>
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-brand-aqua shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <div><dt class="text-brand-navy/50 text-xs uppercase tracking-wide">Season</dt><dd class="text-brand-ink font-medium">Year-round &middot; best Apr–Oct</dd></div>
          </div>
        </dl>
      </div>

    </div>
  </div>
</section>
<?php endif; ?>

<!-- OTHER DESTINATIONS -->
<section class="bg-white py-16 px-6">
  <div class="max-w-7xl mx-auto">
    <h2 class="font-display text-2xl font-semibold text-brand-ink mb-6 reveal">Explore other destinations</h2>
    <div class="flex flex-wrap gap-3 reveal">
      <?php foreach (get_cities() as $c): if ($c['slug'] === $city['slug']) continue; ?>
      <a href="/<?php echo e($c['slug']); ?>" class="inline-flex items-center gap-2 bg-brand-sand hover:bg-brand-foam border border-brand-navy/10 text-brand-navy font-medium text-sm px-5 py-2.5 rounded-full transition-colors duration-200 cursor-pointer">
        <svg class="w-4 h-4 text-brand-aqua" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        <?php echo e($c['name']); ?>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
