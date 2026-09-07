<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'East Mediterranean Yacht Charter & Other Destinations';
$pageDescription = 'Sailing beyond Cyprus? We arrange yacht and boat charters across the East Mediterranean — Greece, Turkey, the Levant and further afield. Tell us what you have in mind and we come back with options.';
$pageKeywords = 'east mediterranean yacht charter, greek islands yacht charter, turkey yacht charter, mediterranean boat rental, bespoke yacht charter cyprus, charter yacht abroad';
$canonical = base_url() . '/east-mediterranean';
$pageImage = '/assets/scenery/sailing.webp';

$structuredData = [
    '@context'    => 'https://schema.org',
    '@type'       => 'Service',
    'name'        => 'East Mediterranean & bespoke yacht charter',
    'serviceType' => 'Yacht charter brokerage',
    'provider'    => [
        '@type' => 'Organization',
        'name'  => 'BoatRent Cyprus',
        'url'   => base_url() . '/',
    ],
    'areaServed'  => [
        ['@type' => 'Country', 'name' => 'Cyprus'],
        ['@type' => 'Country', 'name' => 'Greece'],
        ['@type' => 'Country', 'name' => 'Turkey'],
        ['@type' => 'Country', 'name' => 'Israel'],
        ['@type' => 'Country', 'name' => 'Lebanon'],
        ['@type' => 'Country', 'name' => 'Egypt'],
    ],
    'description' => 'Charters arranged on request across the East Mediterranean and beyond — you tell us the dates, the group and the kind of trip, we come back with vessels and prices.',
    'url'         => $canonical,
];

/* Regions we can realistically place a charter in. Nothing here is a listed
   vessel — every one of these starts as an inquiry, so the cards link to the
   contact form with the region pre-filled rather than to a fleet page. */
$regions = [
    [
        'name'    => 'Greek Islands',
        'subject' => 'Greek Islands charter',
        'blurb'   => 'Cyclades, Ionian, Saronic and the Dodecanese — bareboat, skippered or fully crewed, usually out of Athens, Lefkada, Kos or Rhodes.',
        'detail'  => 'Weekly charters, Sat-to-Sat',
    ],
    [
        'name'    => 'Turkish Riviera',
        'subject' => 'Turkish Riviera charter',
        'blurb'   => 'Bodrum, Göcek, Marmaris and Fethiye, plus traditional gulets for larger groups along the Lycian coast.',
        'detail'  => 'Gulets & sailing yachts',
    ],
    [
        'name'    => 'The Levant',
        'subject' => 'Levant charter',
        'blurb'   => 'Israel, Lebanon and the Syrian coast — day charters and short crossings, subject to the paperwork and conditions of the day.',
        'detail'  => 'Day charters & crossings',
    ],
    [
        'name'    => 'Egypt & the Red Sea',
        'subject' => 'Egypt / Red Sea charter',
        'blurb'   => 'Alexandria and the Egyptian Med coast, or liveaboards and dive boats down at Hurghada and Sharm.',
        'detail'  => 'Liveaboards & dive boats',
    ],
    [
        'name'    => 'Adriatic & Italy',
        'subject' => 'Adriatic / Italy charter',
        'blurb'   => 'Croatia, Montenegro, Sicily, Sardinia and the Amalfi coast — further west, still very much on the table.',
        'detail'  => 'Further afield',
    ],
    [
        'name'    => 'Somewhere else entirely',
        'subject' => 'Charter somewhere else',
        'blurb'   => 'Balearics, the Caribbean, a one-way delivery, a wedding flotilla — if you can describe it, we will try to place it.',
        'detail'  => 'Tell us the idea',
    ],
];

include __DIR__ . '/includes/header.php';
?>

<!-- HERO -->
<section class="relative pt-36 pb-20 px-6 overflow-hidden bg-brand-ink">
  <img src="/assets/scenery/sailing.webp" alt="" aria-hidden="true" class="absolute inset-0 w-full h-full object-cover opacity-25">
  <div class="relative max-w-4xl mx-auto text-center reveal">
    <p class="text-brand-goldL font-semibold uppercase tracking-[0.3em] text-xs mb-4">Beyond Cyprus</p>
    <h1 class="font-display text-4xl sm:text-6xl font-bold text-white leading-[1.08]">East Mediterranean<br class="hidden sm:block"> &amp; Other Destinations</h1>
    <p class="text-white/75 mt-5 max-w-2xl mx-auto">Cyprus is where our fleet lives, but it is not where we stop. Tell us where you want to sail and what kind of trip you have in mind — we work our partner network across the region and come back with what is actually available.</p>
    <div class="mt-9 flex flex-col sm:flex-row items-center justify-center gap-3">
      <a href="/contact?subject=<?php echo rawurlencode('East Mediterranean charter inquiry'); ?>" class="inline-flex items-center gap-2 bg-brand-gold hover:bg-brand-goldL text-brand-ink font-semibold px-7 py-3.5 rounded-full transition-colors duration-200 cursor-pointer">
        Tell us what you want
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
      </a>
      <a href="https://wa.me/35799000000" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 border border-white/25 hover:border-brand-gold text-white font-semibold px-7 py-3.5 rounded-full transition-colors duration-200 cursor-pointer">
        Message us on WhatsApp
      </a>
    </div>
  </div>
</section>

<!-- WHAT THIS IS -->
<section class="bg-brand-sand py-20 px-6">
  <div class="max-w-3xl mx-auto text-center reveal">
    <p class="text-brand-aquaD font-semibold uppercase tracking-[0.3em] text-xs mb-3">How this works</p>
    <h2 class="font-display text-3xl sm:text-4xl font-bold text-brand-ink">Nothing here is a fixed listing</h2>
    <p class="text-brand-navy/70 mt-4 leading-relaxed">Outside Cyprus we do not run a catalogue — boats, dates and prices move too fast for that to mean anything. Instead you send us one message with your dates, your group and your budget, and we go and find the vessel. If we can do it, you get real options with real prices. If we cannot, we say so straight away rather than stringing you along.</p>
  </div>

  <div class="max-w-5xl mx-auto grid md:grid-cols-3 gap-8 mt-14 reveal-stagger">
    <?php
    $steps = [
      ['1', 'You describe the trip', 'Where, when, how many people, and roughly what you want to spend. Rough is fine — we will ask the rest.'],
      ['2', 'We work the network', 'We check with operators and brokers in that region for real availability on your dates.'],
      ['3', 'You get options back', 'Usually within a day or two: vessels, crew arrangement, what is included, all-in prices.'],
    ];
    foreach ($steps as $s): ?>
    <div class="bg-white rounded-2xl border border-brand-navy/10 p-7 text-center">
      <span class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-brand-aqua/10 text-brand-aquaD font-display text-lg font-bold mb-4"><?php echo e($s[0]); ?></span>
      <h3 class="font-display text-lg font-semibold text-brand-ink mb-2"><?php echo e($s[1]); ?></h3>
      <p class="text-brand-navy/65 text-sm leading-relaxed"><?php echo e($s[2]); ?></p>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<?php section_wave('#ffffff', '#E7F7FA'); // sand -> white ?>

<!-- REGIONS -->
<section class="bg-white py-20 px-6">
  <div class="max-w-7xl mx-auto">
    <div class="reveal text-center max-w-2xl mx-auto mb-14">
      <p class="text-brand-aquaD font-semibold uppercase tracking-[0.3em] text-xs mb-3">Where we can place a charter</p>
      <h2 class="font-display text-4xl sm:text-5xl font-bold text-brand-ink">The East Mediterranean, and past it</h2>
      <p class="text-brand-navy/60 mt-4">A guide to the regions we are asked about most. It is not a limit — if your destination is not on this list, ask anyway.</p>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 reveal-stagger">
      <?php foreach ($regions as $r): ?>
      <a href="/contact?subject=<?php echo rawurlencode($r['subject']); ?>" class="group block bg-brand-sand rounded-2xl border border-brand-navy/10 p-7 hover:border-brand-aqua/50 hover:shadow-lg transition-all duration-300 cursor-pointer">
        <div class="flex items-start justify-between gap-3 mb-3">
          <h3 class="font-display text-xl font-semibold text-brand-ink"><?php echo e($r['name']); ?></h3>
          <svg class="w-5 h-5 text-brand-aqua shrink-0 mt-1 opacity-0 group-hover:opacity-100 -translate-x-1 group-hover:translate-x-0 transition-all duration-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </div>
        <p class="text-brand-navy/65 text-sm leading-relaxed mb-4"><?php echo e($r['blurb']); ?></p>
        <span class="inline-block text-xs font-semibold uppercase tracking-wide text-brand-aquaD bg-brand-foam px-3 py-1 rounded-full"><?php echo e($r['detail']); ?></span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php section_wave('#0D1A33', '#ffffff'); // white -> ink ?>

<!-- WHAT TO TELL US -->
<section class="bg-brand-ink text-white py-20 px-6 relative overflow-hidden">
  <div class="absolute -top-24 -left-20 w-96 h-96 bg-brand-aqua/10 rounded-full blur-3xl"></div>
  <div class="relative max-w-5xl mx-auto grid lg:grid-cols-2 gap-14 items-center">
    <div class="reveal-left">
      <p class="text-brand-goldL font-semibold uppercase tracking-[0.3em] text-xs mb-3">Make it easy on yourself</p>
      <h2 class="font-display text-3xl sm:text-4xl font-bold mb-5 leading-tight">What to put in your message</h2>
      <p class="text-white/70 leading-relaxed">None of this is compulsory — send us two lines if that is all you have. But the more of it you include, the faster the first useful answer comes back.</p>
    </div>
    <ul class="reveal-right space-y-4">
      <?php
      $asks = [
        'Destination or region — even loosely ("somewhere in the Greek islands")',
        'Dates, or the window you are flexible within',
        'How many people, and how many need to sleep aboard',
        'Bareboat, skippered or fully crewed',
        'Rough budget, and whether that is per day or for the whole trip',
        'Anything that matters: kids aboard, diving, a birthday, accessibility',
      ];
      foreach ($asks as $a): ?>
      <li class="flex items-start gap-3 text-white/85 text-sm">
        <svg class="w-5 h-5 text-brand-gold shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M16.7 5.3a1 1 0 010 1.4l-7.5 7.5a1 1 0 01-1.4 0L3.3 9.7a1 1 0 011.4-1.4l3.1 3.1 6.8-6.8a1 1 0 011.4 0z"/></svg>
        <span><?php echo e($a); ?></span>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<?php section_wave('#E7F7FA', '#0D1A33'); // ink -> sand ?>

<!-- CTA -->
<section class="bg-brand-sand py-20 px-6">
  <div class="max-w-3xl mx-auto text-center reveal">
    <h2 class="font-display text-3xl sm:text-4xl font-bold text-brand-ink">Sailing somewhere we have not listed?</h2>
    <p class="text-brand-navy/65 mt-4">Send it over. Worst case we tell you it is not something we can arrange — and that answer comes back the same as any other, quickly and without a sales pitch.</p>
    <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
      <a href="/contact?subject=<?php echo rawurlencode('East Mediterranean charter inquiry'); ?>" class="inline-flex items-center gap-2 bg-brand-gold hover:bg-brand-goldL text-brand-ink font-semibold px-7 py-3.5 rounded-full transition-colors duration-200 cursor-pointer">
        Start an inquiry
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
      </a>
      <a href="/boats" class="inline-flex items-center gap-2 border border-brand-navy/20 hover:border-brand-aqua text-brand-navy font-semibold px-7 py-3.5 rounded-full transition-colors duration-200 cursor-pointer">
        Browse the Cyprus fleet
      </a>
    </div>
  </div>
</section>

<!-- CYPRUS DESTINATIONS -->
<section class="bg-white py-16 px-6">
  <div class="max-w-7xl mx-auto">
    <h2 class="font-display text-2xl font-semibold text-brand-ink mb-6 reveal">Or set off from Cyprus</h2>
    <div class="flex flex-wrap gap-3 reveal">
      <?php foreach (get_cities() as $c): ?>
      <a href="/<?php echo e($c['slug']); ?>" class="inline-flex items-center gap-2 bg-brand-sand hover:bg-brand-foam border border-brand-navy/10 text-brand-navy font-medium text-sm px-5 py-2.5 rounded-full transition-colors duration-200 cursor-pointer">
        <svg class="w-4 h-4 text-brand-aqua" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        <?php echo e($c['name']); ?>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
