<?php
require_once __DIR__ . '/includes/functions.php';
session_start();

// Prefer clean slug routing (/boat/{slug}); fall back to ?id= for internal/admin links.
$slug = trim($_GET['slug'] ?? '');
if ($slug !== '') {
    $boat = get_boat_by_slug($slug);
} else {
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $boat = $id ? get_boat($id) : null;
}

if (!$boat || $boat['status'] !== 'active') {
    http_response_code(404);
    $pageTitle = 'Vessel not found';
    include __DIR__ . '/includes/header.php';
    echo '<section class="pt-40 pb-32 px-6 text-center"><h1 class="font-display text-4xl font-bold text-brand-ink mb-4">Vessel not found</h1><p class="text-brand-navy/60 mb-8">This boat may have been removed.</p><a href="/boats" class="inline-flex items-center gap-2 bg-brand-gold text-brand-ink font-semibold px-6 py-3 rounded-full cursor-pointer">Browse the fleet</a></section>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = 'Rent ' . $boat['name'] . ' — ' . $boat['type'] . ' Charter in ' . $boat['city_name'];
$pageDescription = 'Rent the ' . $boat['name'] . ', a ' . $boat['type'] . ' for charter in ' . $boat['city_name'] . ', Cyprus, for up to ' . $boat['capacity'] . ' guests. ' . ($boat['crewed'] ? 'Crewed charter — ' : 'Self-drive or skippered — ') . 'send a free, no-obligation inquiry.';
$pageKeywords = strtolower(
    'rent ' . $boat['type'] . ' ' . $boat['city_name'] . ', charter ' . $boat['name'] . ', rent yacht ' . $boat['city_name'] . ', ' .
    $boat['type'] . ' charter cyprus, rent boat ' . $boat['city_name'] . ', boat rental ' . $boat['city_name']
);
$pageImage = $boat['image_url'];
$ogType = 'product';

$boatUrl = base_url() . '/boat/' . $boat['slug'];
$canonical = $boatUrl;
// Prefer the day rate for schema.org; fall back to any number in the headline label.
$offerPrice = (int) $boat['price_day'] ?: (int) cy_price_num($boat['price_label'] ?? '');
$structuredData = [
    '@context' => 'https://schema.org',
    '@type'    => 'Product',
    'name'     => $boat['name'],
    'description' => $boat['description'],
    'image'    => $boat['image_url'],
    'category' => $boat['type'],
    'brand'    => ['@type' => 'Brand', 'name' => 'BoatRent Cyprus'],
    'offers'   => [
        '@type'         => 'Offer',
        'price'         => (string) $offerPrice,
        'priceCurrency' => 'EUR',
        'availability'  => 'https://schema.org/InStock',
        'url'           => $boatUrl,
        'priceSpecification' => [
            '@type' => 'UnitPriceSpecification',
            'price' => (string) $offerPrice,
            'priceCurrency' => 'EUR',
            'unitText' => !empty($boat['price_day']) ? 'per day' : 'per charter',
        ],
    ],
];

$features = json_arr($boat['features']);
$gallery  = json_arr($boat['gallery']);
if (!$gallery) {
    $gallery = [$boat['image_url']];
}
// ensure main image is first
array_unshift($gallery, $boat['image_url']);
$gallery = array_values(array_unique($gallery));

$old = $_SESSION['old'] ?? [];
$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError   = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Related boats in same city
$related = array_filter(get_boats(['city_id' => (int) $boat['city_id']]), fn($b) => (int) $b['id'] !== (int) $boat['id']);
$related = array_slice(array_values($related), 0, 3);

include __DIR__ . '/includes/header.php';

echo json_ld([
    '@context' => 'https://schema.org',
    '@type'    => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => base_url() . '/'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Fleet', 'item' => base_url() . '/boats'],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $boat['city_name'], 'item' => base_url() . '/' . $boat['city_slug']],
        ['@type' => 'ListItem', 'position' => 4, 'name' => $boat['name'], 'item' => $boatUrl],
    ],
]);
?>

<section class="bg-brand-sand pt-28 pb-6 px-6">
  <div class="max-w-7xl mx-auto">
    <nav class="text-brand-navy/60 text-sm flex items-center gap-2 flex-wrap" aria-label="Breadcrumb">
      <a href="/" class="hover:text-brand-aquaD cursor-pointer">Home</a><span>/</span>
      <a href="/boats" class="hover:text-brand-aquaD cursor-pointer">Fleet</a><span>/</span>
      <a href="/<?php echo e($boat['city_slug']); ?>" class="hover:text-brand-aquaD cursor-pointer"><?php echo e($boat['city_name']); ?></a><span>/</span>
      <span class="text-brand-ink"><?php echo e($boat['name']); ?></span>
    </nav>
  </div>
</section>

<section class="bg-brand-sand pb-8 px-6">
  <div class="max-w-7xl mx-auto grid lg:grid-cols-3 gap-8">

    <!-- LEFT: gallery + details -->
    <div class="lg:col-span-2">
      <!-- Gallery -->
      <div class="reveal">
        <div class="rounded-2xl overflow-hidden aspect-[16/10] bg-brand-navy/10" data-gallery-item data-full="<?php echo e($gallery[0]); ?>">
          <img id="boat-main-img" src="<?php echo e($gallery[0]); ?>" alt="<?php echo e($boat['name']); ?>" class="w-full h-full object-cover cursor-zoom-in">
        </div>
        <?php if (count($gallery) > 1): ?>
        <div class="grid grid-cols-4 gap-3 mt-3">
          <?php foreach ($gallery as $i => $g): ?>
          <button type="button" data-thumb="<?php echo e($g); ?>" class="rounded-xl overflow-hidden aspect-[4/3] cursor-pointer <?php echo $i === 0 ? 'ring-2 ring-brand-gold' : ''; ?>">
            <img src="<?php echo e($g); ?>" alt="<?php echo e($boat['name']); ?> photo <?php echo $i + 1; ?>" loading="lazy" class="w-full h-full object-cover">
          </button>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <!-- Title + specs -->
      <div class="reveal mt-8">
        <div class="flex flex-wrap items-center gap-2 mb-3">
          <span class="text-xs font-medium px-3 py-1 rounded-full <?php echo type_badge_class($boat['type']); ?>"><?php echo e($boat['type']); ?></span>
          <?php if ($boat['crewed']): ?><span class="text-xs font-medium px-3 py-1 rounded-full bg-brand-navy/10 text-brand-navy">Crew included</span><?php endif; ?>
          <?php if ($boat['featured']): ?><span class="text-xs font-medium px-3 py-1 rounded-full bg-brand-gold/20 text-brand-gold">Featured</span><?php endif; ?>
        </div>
        <h1 class="font-display text-4xl sm:text-5xl font-bold text-brand-ink"><?php echo e($boat['name']); ?></h1>
        <p class="flex items-center gap-1.5 text-brand-navy/60 mt-2">
          <svg class="w-4 h-4 text-brand-aqua" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          <?php echo e($boat['city_name']); ?>, Cyprus
        </p>
        <?php
        $meta = array_filter([
            !empty($boat['builder']) ? 'Built by ' . $boat['builder'] : null,
            !empty($boat['year']) ? 'Year ' . $boat['year'] : null,
            !empty($boat['beam']) ? 'Beam ' . $boat['beam'] : null,
        ]);
        if ($meta): ?>
        <p class="text-sm text-brand-navy/55 mt-1.5"><?php echo e(implode('  ·  ', $meta)); ?></p>
        <?php endif; ?>

        <!-- Spec grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-8">
          <?php
          $specs = [
            ['Guests', (int) $boat['capacity'], 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-5.13a4 4 0 11-8 0 4 4 0 018 0zm6 2a4 4 0 10-7.75 0'],
            ['Length', rtrim(rtrim(number_format((float) $boat['length_m'], 1), '0'), '.') . ' m', 'M4 4l16 16M4 4h6M4 4v6'],
            ['Cabins', (int) $boat['cabins'] ?: '—', 'M3 12h18M3 12V7a2 2 0 012-2h14a2 2 0 012 2v5'],
            ['Top speed', $boat['speed'] ?: '—', 'M13 10V3L4 14h7v7l9-11h-7z'],
          ];
          foreach ($specs as $sp): ?>
          <div class="bg-white rounded-xl border border-brand-navy/10 p-4 text-center">
            <div class="mx-auto h-9 w-9 rounded-lg bg-brand-foam flex items-center justify-center text-brand-aqua mb-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo $sp[2]; ?>"/></svg>
            </div>
            <p class="font-display text-xl font-semibold text-brand-ink"><?php echo e((string) $sp[1]); ?></p>
            <p class="text-xs text-brand-navy/50 uppercase tracking-wide"><?php echo e($sp[0]); ?></p>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- Description -->
        <div class="mt-10">
          <h2 class="font-display text-2xl font-semibold text-brand-ink mb-3">Rent the <?php echo e($boat['name']); ?> in <?php echo e($boat['city_name']); ?></h2>
          <p class="text-brand-navy/75 leading-relaxed"><?php echo e($boat['description']); ?></p>
        </div>

        <!-- Features -->
        <?php if ($features): ?>
        <div class="mt-10">
          <h2 class="font-display text-2xl font-semibold text-brand-ink mb-4">What's on board</h2>
          <ul class="grid sm:grid-cols-2 gap-x-8 gap-y-3">
            <?php foreach ($features as $f): ?>
            <li class="flex items-center gap-3 text-brand-navy/75">
              <span class="h-6 w-6 rounded-full bg-brand-aqua/10 text-brand-aqua flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
              </span>
              <?php echo e($f); ?>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- RIGHT: sticky inquiry card -->
    <aside class="lg:col-span-1">
      <div id="inquiry" class="lg:sticky lg:top-28 reveal-right">
        <div class="bg-white rounded-2xl border border-brand-navy/10 shadow-lg p-6">
          <p class="font-display text-3xl font-bold text-brand-ink leading-tight"><?php echo e(boat_price_label($boat)); ?></p>
          <?php
          $pricing = json_arr($boat['pricing'] ?? '');
          if ($pricing): ?>
          <dl class="mt-4 mb-4 divide-y divide-brand-navy/10 border-y border-brand-navy/10">
            <?php foreach ($pricing as $k => $v): if ($v === '' || $v === null) continue; ?>
            <div class="flex items-baseline justify-between py-2">
              <dt class="text-sm text-brand-navy/60"><?php echo e(pricing_row_label((string) $k)); ?></dt>
              <dd class="text-sm font-semibold text-brand-ink"><?php echo e((string) $v); ?></dd>
            </div>
            <?php endforeach; ?>
          </dl>
          <?php else: ?><div class="mb-4"></div><?php endif; ?>
          <?php if (!empty($boat['price_note'])): ?>
          <p class="text-[11px] leading-snug text-brand-navy/45 mb-4"><?php echo e($boat['price_note']); ?></p>
          <?php endif; ?>

          <?php if ($flashSuccess): ?>
          <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3 flex items-start gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            <span><?php echo e($flashSuccess); ?></span>
          </div>
          <?php endif; ?>
          <?php if ($flashError): ?>
          <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3"><?php echo e($flashError); ?></div>
          <?php endif; ?>

          <h2 class="font-display text-lg font-semibold text-brand-ink mb-1">Request this boat</h2>
          <p class="text-brand-navy/55 text-sm mb-5">Send your details — no payment now. We confirm availability and reply fast.</p>

          <form action="/submit-inquiry.php" method="post" data-validate novalidate class="space-y-3">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="boat_id" value="<?php echo (int) $boat['id']; ?>">
            <input type="hidden" name="city" value="<?php echo e($boat['city_name']); ?>">

            <div>
              <label for="name" class="block text-xs font-medium text-brand-navy/60 mb-1">Full name</label>
              <input id="name" name="name" type="text" required value="<?php echo e($old['name'] ?? ''); ?>" placeholder="Your name" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua">
              <p data-error-for="name" class="hidden text-red-600 text-xs mt-1">Please enter your name.</p>
            </div>
            <div>
              <label for="email" class="block text-xs font-medium text-brand-navy/60 mb-1">Email</label>
              <input id="email" name="email" type="email" required value="<?php echo e($old['email'] ?? ''); ?>" placeholder="you@email.com" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua">
              <p data-error-for="email" class="hidden text-red-600 text-xs mt-1">Enter a valid email.</p>
            </div>
            <div>
              <label for="phone" class="block text-xs font-medium text-brand-navy/60 mb-1">Phone <span class="text-brand-navy/40">(optional)</span></label>
              <div class="flex gap-2">
                <label for="dial_code" class="sr-only">Country code</label>
                <select id="dial_code" name="dial_code" class="w-28 shrink-0 bg-brand-sand border border-brand-navy/10 rounded-lg px-2 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua cursor-pointer">
                  <?php echo dial_code_options($old['dial_code'] ?? '+357'); ?>
                </select>
                <input id="phone" name="phone" type="tel" value="<?php echo e($old['phone'] ?? ''); ?>" placeholder="99 123456" class="flex-1 min-w-0 bg-brand-sand border border-brand-navy/10 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua">
              </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label for="date_from" class="block text-xs font-medium text-brand-navy/60 mb-1">From</label>
                <input id="date_from" name="date_from" type="date" value="<?php echo e($old['from'] ?? ''); ?>" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua cursor-pointer">
              </div>
              <div>
                <label for="date_to" class="block text-xs font-medium text-brand-navy/60 mb-1">To</label>
                <input id="date_to" name="date_to" type="date" value="<?php echo e($old['to'] ?? ''); ?>" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua cursor-pointer">
              </div>
            </div>
            <div>
              <label for="guests" class="block text-xs font-medium text-brand-navy/60 mb-1">Guests</label>
              <select id="guests" name="guests" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua cursor-pointer">
                <?php for ($i = 1; $i <= (int) $boat['capacity']; $i++): ?>
                <option value="<?php echo $i; ?>" <?php echo (string) ($old['guests'] ?? '') === (string) $i ? 'selected' : ''; ?>><?php echo $i; ?> <?php echo $i === 1 ? 'guest' : 'guests'; ?></option>
                <?php endfor; ?>
              </select>
            </div>
            <div>
              <label for="message" class="block text-xs font-medium text-brand-navy/60 mb-1">Message <span class="text-brand-navy/40">(optional)</span></label>
              <textarea id="message" name="message" rows="3" placeholder="Tell us about your plans…" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua resize-none"><?php echo e($old['message'] ?? ''); ?></textarea>
            </div>
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-brand-gold hover:bg-brand-goldL text-brand-ink font-semibold px-6 py-3.5 rounded-full transition-colors duration-200 cursor-pointer">
              Send Inquiry
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
            <p class="text-center text-xs text-brand-navy/45">Free to inquire · No obligation</p>
          </form>
        </div>
      </div>
    </aside>
  </div>
</section>

<!-- RELATED -->
<?php if ($related): ?>
<section class="bg-white py-16 px-6">
  <div class="max-w-7xl mx-auto">
    <h2 class="font-display text-2xl sm:text-3xl font-semibold text-brand-ink mb-8 reveal">More boats in <?php echo e($boat['city_name']); ?></h2>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-7 reveal-stagger">
      <?php foreach ($related as $boat) { include __DIR__ . '/includes/boat-card.php'; } ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- LIGHTBOX -->
<div id="lightbox" role="dialog" aria-modal="true" aria-label="Image viewer">
  <button id="lightbox-close" type="button" aria-label="Close" class="absolute top-6 right-6 z-10 h-11 w-11 flex items-center justify-center rounded-full bg-white/10 hover:bg-brand-gold hover:text-brand-ink text-white transition-colors duration-200 cursor-pointer">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
  </button>
  <button id="lightbox-prev" type="button" aria-label="Previous image" class="absolute left-4 sm:left-6 top-1/2 -translate-y-1/2 z-10 h-12 w-12 flex items-center justify-center rounded-full bg-white/10 hover:bg-brand-gold hover:text-brand-ink text-white transition-colors duration-200 cursor-pointer">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
  </button>
  <img id="lightbox-img" src="" alt="" class="max-h-[82vh] max-w-[90vw] w-auto rounded-xl object-contain">
  <button id="lightbox-next" type="button" aria-label="Next image" class="absolute right-4 sm:right-6 top-1/2 -translate-y-1/2 z-10 h-12 w-12 flex items-center justify-center rounded-full bg-white/10 hover:bg-brand-gold hover:text-brand-ink text-white transition-colors duration-200 cursor-pointer">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
  </button>
  <div id="lightbox-count" class="absolute bottom-6 left-1/2 -translate-x-1/2 text-white/85 text-sm font-medium bg-white/10 rounded-full px-3.5 py-1.5"></div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
