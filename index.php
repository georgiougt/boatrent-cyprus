<?php
$pageTitle = 'Yacht Charter & Boat Rental in Cyprus';
$pageDescription = 'Rent a yacht or boat in Cyprus with BoatRent Cyprus. Charter yachts, catamarans and speedboats in Limassol, Paphos, Larnaca, Ayia Napa, Protaras and Latsi — browse the fleet and inquire in minutes.';
$pageKeywords = 'rent yacht cyprus, rent boat cyprus, charter yacht cyprus, yacht charter cyprus, boat rental cyprus, rent a yacht cyprus, yacht hire cyprus, rent yacht limassol, rent boat limassol, rent yacht paphos, rent yacht larnaca, rent yacht ayia napa';
require_once __DIR__ . '/includes/functions.php';
$cities = get_cities();
$featured = get_featured_boats(6);
$types = get_boat_types();
$totalBoats = (int) db()->query("SELECT COUNT(*) FROM boats WHERE status='active'")->fetchColumn();
$canonical = base_url() . '/';
$heroPreload = '/assets/scenery/yacht-hero.webp'; // LCP image
include __DIR__ . '/includes/header.php';
?>

<!-- FIRST-VISIT CINEMATIC INTRO (drone shot -> homepage) -->
<div id="intro-overlay" aria-hidden="true">
  <img class="intro-img" src="/assets/scenery/yacht-hero.webp" alt="" width="1600" height="900" fetchpriority="high">
  <!-- Optional: drop a clip at /videos/intro.mp4 and it plays automatically over the image -->
  <video class="intro-video" muted playsinline preload="auto" tabindex="-1">
    <source src="/videos/intro.mp4" type="video/mp4">
  </video>
  <div class="intro-vignette"></div>
  <div class="intro-content">
    <span class="intro-mark">
      <svg class="w-12 h-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 17c1.5 1 3 1 4.5 0s3-1 4.5 0 3 1 4.5 0 3-1 4.5 0"/>
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 14l7-9 7 9M12 5v9"/>
      </svg>
    </span>
    <div class="intro-title">BoatRent<span>.</span>Cyprus</div>
    <p class="intro-tag">Your day on the Cyprus sea</p>
    <div class="intro-line"><span></span></div>
  </div>
</div>
<script>
(function () {
  var o = document.getElementById('intro-overlay');
  if (!o) return;
  var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
  var seen = false;
  try { seen = localStorage.getItem('brc_seen_intro') === '1'; } catch (e) {}
  if (seen || reduce) { if (o.parentNode) o.parentNode.removeChild(o); return; }
  try { localStorage.setItem('brc_seen_intro', '1'); } catch (e) {}
  document.documentElement.classList.add('intro-lock');
  var v = o.querySelector('.intro-video');
  if (v) {
    v.addEventListener('loadeddata', function () { v.classList.add('show'); });
    var p = v.play && v.play();
    if (p && p.catch) p.catch(function () {});
  }
  setTimeout(function () {
    document.documentElement.classList.remove('intro-lock');
    if (o.parentNode) o.parentNode.removeChild(o);
  }, 2950);
})();
</script>

<!-- HERO -->
<section class="relative min-h-screen flex items-center justify-center overflow-hidden">
  <img src="/assets/scenery/yacht-hero.webp" alt="Luxury yacht moored on turquoise Cyprus waters" width="1600" height="900" fetchpriority="high" class="absolute inset-0 w-full h-full object-cover">
  <div class="absolute inset-0 hero-overlay"></div>

  <div class="relative z-10 max-w-5xl mx-auto px-6 text-center pt-24 pb-16">
    <p class="reveal inline-flex items-center gap-2 text-brand-goldL font-medium uppercase tracking-[0.3em] text-xs mb-6">
      <span class="h-px w-8 bg-brand-gold"></span> Cyprus &middot; Est. 2024 <span class="h-px w-8 bg-brand-gold"></span>
    </p>
    <h1 class="reveal font-display text-4xl sm:text-6xl lg:text-7xl font-bold text-white leading-[1.08] mb-6">
      Your Day on the<br><span class="italic text-brand-goldL">Cyprus Sea</span> Awaits
    </h1>
    <p class="reveal text-white/80 text-base sm:text-lg max-w-2xl mx-auto mb-10" style="transition-delay:.1s">
      Rent a yacht, catamaran or speedboat in Cyprus — browse hand-picked boats across six coastal towns, send one inquiry, and we handle the rest.
    </p>

    <!-- Search bar -->
    <form action="/boats" method="get" class="reveal max-w-3xl mx-auto bg-white/95 backdrop-blur rounded-2xl sm:rounded-full p-3 shadow-2xl flex flex-col sm:flex-row gap-2" style="transition-delay:.2s">
      <div class="flex-1 flex items-center gap-2 px-3">
        <svg class="w-5 h-5 text-brand-aqua shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        <label for="hero-city" class="sr-only">Destination</label>
        <select id="hero-city" name="city" class="w-full bg-transparent py-2.5 text-brand-ink focus:outline-none cursor-pointer">
          <option value="">All destinations</option>
          <?php foreach ($cities as $c): ?>
          <option value="<?php echo e($c['slug']); ?>"><?php echo e($c['name']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="hidden sm:block w-px bg-brand-navy/15 my-2"></div>
      <div class="flex-1 flex items-center gap-2 px-3">
        <svg class="w-5 h-5 text-brand-aqua shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17c1.5 1 3 1 4.5 0s3-1 4.5 0 3 1 4.5 0 3-1 4.5 0M5 14l7-9 7 9"/></svg>
        <label for="hero-type" class="sr-only">Vessel type</label>
        <select id="hero-type" name="type" class="w-full bg-transparent py-2.5 text-brand-ink focus:outline-none cursor-pointer">
          <option value="">Any vessel</option>
          <?php foreach ($types as $t): ?>
          <option value="<?php echo e($t); ?>"><?php echo e($t); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="inline-flex items-center justify-center gap-2 bg-brand-gold hover:bg-brand-goldL text-brand-ink font-semibold px-7 py-3.5 rounded-full transition-colors duration-200 cursor-pointer">
        Search
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
      </button>
    </form>

    <div class="reveal flex flex-wrap items-center justify-center gap-x-8 gap-y-3 mt-10 text-white/70 text-sm" style="transition-delay:.3s">
      <span class="flex items-center gap-2"><svg class="w-4 h-4 text-brand-gold" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M16.7 5.3a1 1 0 010 1.4l-7.5 7.5a1 1 0 01-1.4 0L3.3 9.7a1 1 0 011.4-1.4l3.1 3.1 6.8-6.8a1 1 0 011.4 0z"/></svg> Licensed local operators</span>
      <span class="flex items-center gap-2"><svg class="w-4 h-4 text-brand-gold" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M16.7 5.3a1 1 0 010 1.4l-7.5 7.5a1 1 0 01-1.4 0L3.3 9.7a1 1 0 011.4-1.4l3.1 3.1 6.8-6.8a1 1 0 011.4 0z"/></svg> No booking fees</span>
      <span class="flex items-center gap-2"><svg class="w-4 h-4 text-brand-gold" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M16.7 5.3a1 1 0 010 1.4l-7.5 7.5a1 1 0 01-1.4 0L3.3 9.7a1 1 0 011.4-1.4l3.1 3.1 6.8-6.8a1 1 0 011.4 0z"/></svg> Fast reply, every time</span>
    </div>
  </div>

  <div class="absolute bottom-0 left-0 right-0 wave-divider pointer-events-none" aria-hidden="true">
    <svg class="wave-svg wave-svg--back" viewBox="0 0 1440 120" preserveAspectRatio="none">
      <g class="wave-move wave-move--back">
        <path d="M0,60 C240,100 480,100 720,60 C960,20 1200,20 1440,60 L1440,120 L0,120 Z"></path>
        <path d="M0,60 C240,100 480,100 720,60 C960,20 1200,20 1440,60 L1440,120 L0,120 Z" transform="translate(1440,0)"></path>
      </g>
    </svg>
    <svg class="wave-svg wave-svg--mid" viewBox="0 0 1440 120" preserveAspectRatio="none">
      <g class="wave-move wave-move--mid">
        <path d="M0,50 C300,90 540,10 720,50 C960,100 1140,20 1440,50 L1440,120 L0,120 Z"></path>
        <path d="M0,50 C300,90 540,10 720,50 C960,100 1140,20 1440,50 L1440,120 L0,120 Z" transform="translate(1440,0)"></path>
      </g>
    </svg>
    <svg class="wave-svg wave-svg--front" viewBox="0 0 1440 120" preserveAspectRatio="none">
      <g class="wave-move wave-move--front">
        <path d="M0,70 C240,40 480,40 720,70 C960,100 1200,100 1440,70 L1440,120 L0,120 Z"></path>
        <path d="M0,70 C240,40 480,40 720,70 C960,100 1200,100 1440,70 L1440,120 L0,120 Z" transform="translate(1440,0)"></path>
      </g>
    </svg>
  </div>
</section>

<!-- STATS -->
<section class="bg-brand-sand pt-6 pb-14 px-6">
  <div class="max-w-5xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-6 text-center reveal-stagger">
    <div><p class="font-display text-4xl font-bold text-brand-ink"><span class="counter" data-target="<?php echo $totalBoats; ?>">0</span></p><p class="text-brand-navy/60 text-xs uppercase tracking-wide mt-1">Vessels Listed</p></div>
    <div><p class="font-display text-4xl font-bold text-brand-ink"><span class="counter" data-target="6">0</span></p><p class="text-brand-navy/60 text-xs uppercase tracking-wide mt-1">Coastal Towns</p></div>
    <div><p class="font-display text-4xl font-bold text-brand-ink"><span class="counter" data-target="1800">0</span>+</p><p class="text-brand-navy/60 text-xs uppercase tracking-wide mt-1">Guests Sailed</p></div>
    <div><p class="font-display text-4xl font-bold text-brand-ink"><span class="counter" data-target="4">0</span>.9</p><p class="text-brand-navy/60 text-xs uppercase tracking-wide mt-1">Avg. Rating</p></div>
  </div>
</section>

<!-- DESTINATIONS -->
<section class="bg-brand-sand py-20 px-6">
  <div class="max-w-7xl mx-auto">
    <div class="reveal text-center max-w-2xl mx-auto mb-14">
      <p class="text-brand-aquaD font-semibold uppercase tracking-[0.3em] text-xs mb-3">Where to sail</p>
      <h2 class="font-display text-4xl sm:text-5xl font-bold text-brand-ink">Rent a Boat by Destination</h2>
      <p class="text-brand-navy/60 mt-4">Rent a yacht or boat in any of Cyprus' six coastal towns — each with its own coastline, character and fleet. Pick where you want to set off from.</p>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 reveal-stagger">
      <?php foreach ($cities as $c): $cnt = count_boats_in_city((int) $c['id']); ?>
      <a href="/<?php echo e($c['slug']); ?>" class="group relative block rounded-2xl overflow-hidden h-72 cursor-pointer shadow-sm hover:shadow-xl transition-shadow duration-300">
        <img src="<?php echo e($c['image_url']); ?>" alt="Boat rentals in <?php echo e($c['name']); ?>, Cyprus" loading="lazy" class="card-img w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-brand-ink/90 via-brand-ink/30 to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 p-6">
          <div class="flex items-center justify-between">
            <h3 class="font-display text-2xl font-semibold text-white"><?php echo e($c['name']); ?></h3>
            <span class="text-xs text-brand-ink font-semibold bg-brand-gold px-2.5 py-1 rounded-full"><?php echo $cnt; ?> boats</span>
          </div>
          <p class="text-white/75 text-sm mt-1"><?php echo e($c['tagline']); ?></p>
          <span class="inline-flex items-center gap-1.5 text-brand-goldL text-sm font-medium mt-3 opacity-0 group-hover:opacity-100 -translate-x-2 group-hover:translate-x-0 transition-all duration-300">
            Explore fleet
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
          </span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php section_wave('#ffffff', '#E7F7FA'); // sand -> white ?>

<!-- FEATURED FLEET -->
<section class="bg-white py-20 px-6">
  <div class="max-w-7xl mx-auto">
    <div class="reveal flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-12">
      <div>
        <p class="text-brand-aquaD font-semibold uppercase tracking-[0.3em] text-xs mb-3">Hand-picked</p>
        <h2 class="font-display text-4xl sm:text-5xl font-bold text-brand-ink">Featured Yachts &amp; Boats to Rent</h2>
      </div>
      <a href="/boats" class="inline-flex items-center gap-2 text-brand-navy hover:text-brand-aquaD font-semibold transition-colors duration-200 cursor-pointer group">
        View all <?php echo $totalBoats; ?> boats
        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
      </a>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-7 reveal-stagger">
      <?php foreach ($featured as $boat) { include __DIR__ . '/includes/boat-card.php'; } ?>
    </div>
  </div>
</section>

<?php section_wave('#0D1A33', '#ffffff'); // white -> ink ?>

<!-- HOW IT WORKS -->
<section class="bg-brand-ink text-white py-20 px-6 relative overflow-hidden">
  <div class="absolute -top-20 -right-20 w-96 h-96 bg-brand-aqua/10 rounded-full blur-3xl"></div>
  <div class="relative max-w-7xl mx-auto">
    <div class="reveal text-center max-w-2xl mx-auto mb-16">
      <p class="text-brand-goldL font-semibold uppercase tracking-[0.3em] text-xs mb-3">Simple as it sounds</p>
      <h2 class="font-display text-4xl sm:text-5xl font-bold">How It Works</h2>
    </div>
    <div class="grid md:grid-cols-3 gap-8 reveal-stagger">
      <?php
      $steps = [
        ['1', 'Browse the fleet', 'Filter by town, vessel type, group size and budget until you find the perfect match.', 'M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z'],
        ['2', 'Send one inquiry', 'Tell us your dates and group size on the boat page. No accounts, no upfront payment.', 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
        ['3', 'We handle the rest', 'Our team confirms availability with the operator and gets back to you fast with the details.', 'M5 13l4 4L19 7'],
      ];
      foreach ($steps as $s): ?>
      <div class="relative bg-white/5 border border-white/10 rounded-2xl p-8 hover:border-brand-gold/40 transition-colors duration-300">
        <div class="absolute -top-5 left-8 h-10 w-10 rounded-full bg-brand-gold text-brand-ink font-display font-bold flex items-center justify-center"><?php echo $s[0]; ?></div>
        <div class="h-12 w-12 rounded-xl bg-brand-aqua/15 flex items-center justify-center text-brand-aqua mb-5 mt-3">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo $s[3]; ?>"/></svg>
        </div>
        <h3 class="font-display text-xl font-semibold mb-2"><?php echo e($s[1]); ?></h3>
        <p class="text-white/60 text-sm leading-relaxed"><?php echo e($s[2]); ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php section_wave('#E7F7FA', '#0D1A33'); // ink -> sand ?>

<!-- TESTIMONIALS -->
<section class="bg-brand-sand py-20 px-6">
  <div class="max-w-7xl mx-auto">
    <div class="reveal text-center max-w-2xl mx-auto mb-14">
      <p class="text-brand-aquaD font-semibold uppercase tracking-[0.3em] text-xs mb-3">Happy crews</p>
      <h2 class="font-display text-4xl sm:text-5xl font-bold text-brand-ink">What Guests Say</h2>
    </div>
    <div class="grid md:grid-cols-3 gap-7 reveal-stagger">
      <?php
      $reviews = [
        ['Hannah & James', 'UK', 'Booked the Azure Princess for our anniversary. The whole thing was handled in a day — flawless from inquiry to sunset on deck.'],
        ['Marios K.', 'Cyprus', 'Used BoatRent to find a speedboat in Ayia Napa. Easy to compare options and the operator was exactly as described.'],
        ['Lena S.', 'Germany', 'A catamaran day from Latsi to the Blue Lagoon. Smooth communication and a boat that matched the photos perfectly.'],
      ];
      foreach ($reviews as $r): ?>
      <div class="bg-white rounded-2xl p-8 border border-brand-navy/10 shadow-sm">
        <div class="flex gap-1 mb-4 text-brand-gold" aria-label="5 out of 5 stars">
          <?php for ($i = 0; $i < 5; $i++): ?><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M9.05 2.93c.3-.92 1.6-.92 1.9 0l1.52 4.67a1 1 0 00.95.69h4.91c.97 0 1.37 1.24.59 1.81l-3.98 2.89a1 1 0 00-.36 1.12l1.51 4.67c.3.92-.75 1.69-1.53 1.12l-3.98-2.89a1 1 0 00-1.18 0l-3.97 2.89c-.79.57-1.84-.2-1.54-1.12l1.52-4.67a1 1 0 00-.36-1.12L1.58 10.8c-.78-.57-.38-1.81.59-1.81h4.91a1 1 0 00.95-.69L9.05 2.93z"/></svg><?php endfor; ?>
        </div>
        <p class="text-brand-navy/75 leading-relaxed mb-6">&ldquo;<?php echo e($r[2]); ?>&rdquo;</p>
        <p class="font-display font-semibold text-brand-ink"><?php echo e($r[0]); ?> <span class="text-brand-navy/50 font-body font-normal text-sm">&middot; <?php echo e($r[1]); ?></span></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php section_wave('#ffffff', '#E7F7FA'); // sand -> white ?>

<!-- FAQ -->
<section class="bg-white py-20 px-6">
  <div class="max-w-3xl mx-auto">
    <div class="reveal text-center mb-12">
      <p class="text-brand-aquaD font-semibold uppercase tracking-[0.3em] text-xs mb-3">Good to know</p>
      <h2 class="font-display text-4xl sm:text-5xl font-bold text-brand-ink">Frequently Asked Questions</h2>
    </div>
    <div class="space-y-4 reveal">
      <?php foreach (array_slice(get_faqs(), 0, 5) as $i => $faq): ?>
      <details class="group bg-brand-sand rounded-2xl border border-brand-navy/10 overflow-hidden" <?php echo $i === 0 ? 'open' : ''; ?>>
        <summary class="flex items-center justify-between gap-4 cursor-pointer list-none px-6 py-5 font-display text-lg font-semibold text-brand-ink hover:text-brand-aquaD transition-colors duration-200">
          <span><?php echo e($faq['q']); ?></span>
          <span class="shrink-0 h-8 w-8 rounded-full bg-white text-brand-aquaD flex items-center justify-center transition-transform duration-300 group-open:rotate-45">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
          </span>
        </summary>
        <div class="px-6 pb-5 -mt-1 text-brand-navy/75 leading-relaxed"><?php echo e($faq['a']); ?></div>
      </details>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-8 reveal">
      <a href="/faq" class="inline-flex items-center gap-2 text-brand-navy hover:text-brand-aquaD font-semibold transition-colors duration-200 cursor-pointer group">
        See all questions
        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
      </a>
    </div>
  </div>
</section>

<!-- OWNER CTA -->
<section class="bg-brand-sand py-20 px-6">
  <div class="reveal max-w-5xl mx-auto rounded-3xl bg-gradient-to-br from-brand-navy to-brand-ink p-10 sm:p-14 relative overflow-hidden">
    <div class="absolute -bottom-16 -right-10 w-72 h-72 bg-brand-gold/15 rounded-full blur-3xl"></div>
    <div class="relative grid md:grid-cols-2 gap-8 items-center">
      <div>
        <h2 class="font-display text-3xl sm:text-4xl font-bold text-white mb-4">Own a boat in Cyprus?</h2>
        <p class="text-white/70">List your vessel on BoatRent Cyprus and reach travellers searching for exactly what you offer. We send you qualified inquiries — you stay in control of the calendar.</p>
      </div>
      <div class="md:text-right">
        <a href="/contact?subject=List+my+boat" class="inline-flex items-center gap-2 bg-brand-gold hover:bg-brand-goldL text-brand-ink font-semibold px-8 py-4 rounded-full transition-colors duration-200 cursor-pointer">
          List Your Boat
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
