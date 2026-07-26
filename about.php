<?php
$pageTitle = 'About';
require_once __DIR__ . '/includes/functions.php';
$totalBoats = (int) db()->query("SELECT COUNT(*) FROM boats WHERE status='active'")->fetchColumn();
$canonical = base_url() . '/about';
include __DIR__ . '/includes/header.php';
?>

<section class="relative pt-36 pb-20 px-6 overflow-hidden bg-brand-ink">
  <img src="/assets/scenery/ayia-napa.webp" alt="" aria-hidden="true" class="absolute inset-0 w-full h-full object-cover opacity-25">
  <div class="relative max-w-4xl mx-auto text-center reveal">
    <p class="text-brand-goldL font-semibold uppercase tracking-[0.3em] text-xs mb-4">Our story</p>
    <h1 class="font-display text-5xl sm:text-6xl font-bold text-white">Cyprus, by Sea</h1>
    <p class="text-white/70 mt-5 max-w-2xl mx-auto">We connect travellers with the island's best independent boat operators — one simple inquiry at a time.</p>
  </div>
</section>

<section class="bg-brand-sand py-20 px-6">
  <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-16 items-center">
    <div class="reveal-left">
      <div class="rounded-3xl overflow-hidden aspect-[4/3] shadow-xl">
        <img src="/assets/scenery/sailing.webp" alt="Sailing yacht on calm Cyprus waters" class="w-full h-full object-cover">
      </div>
    </div>
    <div class="reveal">
      <p class="text-brand-aquaD font-semibold uppercase tracking-[0.3em] text-xs mb-3">Why we exist</p>
      <h2 class="font-display text-4xl font-bold text-brand-ink mb-5 leading-tight">One marketplace for the whole Cyprus coastline.</h2>
      <p class="text-brand-navy/70 leading-relaxed mb-4">Renting a boat in Cyprus used to mean a dozen phone calls, scattered listings and unclear pricing. We bring every town's fleet into one place — from luxury yachts in Limassol to self-drive speedboats in Ayia Napa.</p>
      <p class="text-brand-navy/70 leading-relaxed">You browse, compare and send a single inquiry. We coordinate with the licensed local operator and come back to you with availability and the final details. No accounts, no booking fees, no fuss.</p>
    </div>
  </div>
</section>

<section class="bg-white py-16 px-6 border-y border-brand-navy/10">
  <div class="max-w-5xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-8 text-center reveal-stagger">
    <div><p class="font-display text-4xl font-bold text-brand-ink"><span class="counter" data-target="<?php echo $totalBoats; ?>">0</span></p><p class="text-brand-navy/60 text-xs uppercase tracking-wide mt-1">Vessels</p></div>
    <div><p class="font-display text-4xl font-bold text-brand-ink"><span class="counter" data-target="6">0</span></p><p class="text-brand-navy/60 text-xs uppercase tracking-wide mt-1">Towns</p></div>
    <div><p class="font-display text-4xl font-bold text-brand-ink"><span class="counter" data-target="20">0</span>+</p><p class="text-brand-navy/60 text-xs uppercase tracking-wide mt-1">Partner Operators</p></div>
    <div><p class="font-display text-4xl font-bold text-brand-ink"><span class="counter" data-target="1800">0</span>+</p><p class="text-brand-navy/60 text-xs uppercase tracking-wide mt-1">Guests Sailed</p></div>
  </div>
</section>

<section class="bg-brand-sand py-20 px-6">
  <div class="max-w-7xl mx-auto">
    <div class="reveal text-center max-w-2xl mx-auto mb-14">
      <p class="text-brand-aquaD font-semibold uppercase tracking-[0.3em] text-xs mb-3">What we stand for</p>
      <h2 class="font-display text-4xl sm:text-5xl font-bold text-brand-ink">Our Promise</h2>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8 reveal-stagger">
      <?php
      $values = [
        ['Licensed & insured', 'Every operator on the platform is vetted, licensed and insured for passenger charters in Cyprus.', 'M9 12l2 2 4-4m5.6 1A9 9 0 1 1 12 3a9 9 0 0 1 9.6 8z'],
        ['Transparent pricing', 'The price you see is the price you discuss — no hidden booking fees added at the end.', 'M12 8c-1.66 0-3 .9-3 2s1.34 2 3 2 3 .9 3 2-1.34 2-3 2m0-8V6m0 12v-2'],
        ['Local knowledge', 'Our partners know the hidden coves, the best swim stops and the perfect sunset routes.', 'M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z'],
      ];
      foreach ($values as $v): ?>
      <div class="bg-white rounded-2xl p-8 border border-brand-navy/10 hover:border-brand-aqua/40 transition-colors duration-300">
        <div class="h-12 w-12 rounded-xl bg-brand-aqua/10 flex items-center justify-center text-brand-aqua mb-5">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo $v[2]; ?>"/></svg>
        </div>
        <h3 class="font-display text-xl font-semibold text-brand-ink mb-2"><?php echo e($v[0]); ?></h3>
        <p class="text-brand-navy/65 text-sm leading-relaxed"><?php echo e($v[1]); ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="bg-white py-20 px-6">
  <div class="reveal max-w-5xl mx-auto rounded-3xl bg-gradient-to-br from-brand-navy to-brand-ink p-10 sm:p-14 text-center relative overflow-hidden">
    <div class="absolute -top-16 -left-16 w-72 h-72 bg-brand-aqua/15 rounded-full blur-3xl"></div>
    <h2 class="relative font-display text-3xl sm:text-4xl font-bold text-white mb-4">Ready to get on the water?</h2>
    <p class="relative text-white/70 max-w-xl mx-auto mb-8">Browse the fleet and send your first inquiry in under two minutes.</p>
    <a href="/boats" class="relative inline-flex items-center gap-2 bg-brand-gold hover:bg-brand-goldL text-brand-ink font-semibold px-8 py-4 rounded-full transition-colors duration-200 cursor-pointer">Browse the Fleet</a>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
