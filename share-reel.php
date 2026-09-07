<?php
$pageTitle = 'Share Your Reel';
$pageDescription = 'Filmed your day on the water in Cyprus? Send us your reel and we\'ll feature the best ones on the BoatRent Cyprus homepage.';
require_once __DIR__ . '/includes/functions.php';
session_start();

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError   = $_SESSION['flash_error'] ?? null;
$old          = $_SESSION['old_reel'] ?? [];
unset($_SESSION['flash_success'], $_SESSION['flash_error'], $_SESSION['old_reel']);

$maxBytes = reel_max_bytes();
$maxLabel = human_bytes($maxBytes);
$fleet    = get_boats();
$canonical = base_url() . '/share-your-reel';
include __DIR__ . '/includes/header.php';
?>

<section class="relative pt-36 pb-16 px-6 bg-brand-ink overflow-hidden">
  <img src="/assets/scenery/marina.webp" alt="" aria-hidden="true" class="absolute inset-0 w-full h-full object-cover opacity-20">
  <div class="relative max-w-4xl mx-auto text-center reveal">
    <p class="text-brand-goldL font-semibold uppercase tracking-[0.3em] text-xs mb-4">Guest reels</p>
    <h1 class="font-display text-5xl sm:text-6xl font-bold text-white">Share Your Reel</h1>
    <p class="text-white/70 mt-5 max-w-xl mx-auto">Filmed something good out there? Send us the clip. We feature our favourites on the homepage — with your name on it.</p>
  </div>
</section>

<section class="bg-brand-sand py-16 px-6">
  <div class="max-w-7xl mx-auto grid lg:grid-cols-5 gap-10">

    <div class="lg:col-span-2 reveal-left space-y-5">
      <div class="bg-white rounded-2xl border border-brand-navy/10 p-6">
        <div class="h-11 w-11 rounded-xl bg-brand-aqua/10 flex items-center justify-center text-brand-aqua mb-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.55-2.27A1 1 0 0121 8.62v6.76a1 1 0 01-1.45.89L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
        </div>
        <h2 class="font-display text-lg font-semibold text-brand-ink">What works best</h2>
        <ul class="text-sm text-brand-navy/65 mt-3 space-y-2">
          <li class="flex gap-2"><span class="text-brand-aqua">&bull;</span> Filmed upright (portrait), like an Instagram reel.</li>
          <li class="flex gap-2"><span class="text-brand-aqua">&bull;</span> Short and sweet — 15 to 30 seconds.</li>
          <li class="flex gap-2"><span class="text-brand-aqua">&bull;</span> MP4, MOV or WebM, up to <strong class="text-brand-ink"><?php echo e($maxLabel); ?></strong>.</li>
          <li class="flex gap-2"><span class="text-brand-aqua">&bull;</span> Over the limit? Trim it in your phone's Photos app first — that usually does it.</li>
          <li class="flex gap-2"><span class="text-brand-aqua">&bull;</span> The boat, the water, the people — no need to edit.</li>
        </ul>
      </div>

      <div class="bg-white rounded-2xl border border-brand-navy/10 p-6">
        <div class="h-11 w-11 rounded-xl bg-brand-aqua/10 flex items-center justify-center text-brand-aqua mb-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M12 3l7 4v5c0 4.42-3.13 7.85-7 9-3.87-1.15-7-4.58-7-9V7l7-4z"/></svg>
        </div>
        <h2 class="font-display text-lg font-semibold text-brand-ink">Before it goes live</h2>
        <p class="text-sm text-brand-navy/65 mt-3 leading-relaxed">Every reel is reviewed by our team before it appears anywhere on the site. Your email stays private — we only use it if we need to check something with you. Changed your mind? Email us and we'll take it down.</p>
      </div>
    </div>

    <div id="share" class="lg:col-span-3 reveal-right">
      <div class="bg-white rounded-2xl border border-brand-navy/10 shadow-sm p-8 sm:p-10">
        <?php if ($flashSuccess): ?>
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3 flex items-start gap-2">
          <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          <span><?php echo e($flashSuccess); ?></span>
        </div>
        <?php endif; ?>
        <?php if ($flashError): ?>
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3"><?php echo e($flashError); ?></div>
        <?php endif; ?>

        <h2 class="font-display text-2xl font-semibold text-brand-ink mb-1">Send us your clip</h2>
        <p class="text-brand-navy/55 text-sm mb-7">Takes a minute. Big files can take a little longer to upload — hang tight after you hit send.</p>

        <form action="/submit-reel.php" method="post" enctype="multipart/form-data" data-reel-form class="grid sm:grid-cols-2 gap-5">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo (int) $maxBytes; ?>">
          <input type="hidden" name="poster" data-reel-poster value="">
          <!-- Honeypot: hidden from people, irresistible to bots. -->
          <p class="hidden" aria-hidden="true"><label for="website">Website</label><input id="website" name="website" type="text" tabindex="-1" autocomplete="off"></p>

          <div class="sm:col-span-2">
            <label for="reel" class="block text-sm font-medium text-brand-navy/70 mb-1.5">Your reel <span class="text-brand-navy/40">(max <?php echo e($maxLabel); ?>)</span></label>
            <label for="reel" data-reel-drop class="flex flex-col items-center justify-center gap-2 w-full border-2 border-dashed border-brand-navy/15 hover:border-brand-aqua rounded-xl px-4 py-9 text-center cursor-pointer transition-colors duration-200 bg-brand-sand">
              <span class="h-12 w-12 rounded-full bg-white flex items-center justify-center text-brand-aqua">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0L8 8m4-4l4 4M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2"/></svg>
              </span>
              <span data-reel-label class="text-sm font-medium text-brand-ink">Tap to choose a video</span>
              <span class="text-xs text-brand-navy/50">MP4, MOV or WebM &middot; portrait works best</span>
            </label>
            <input id="reel" name="reel" type="file" required accept="video/mp4,video/quicktime,video/webm,video/x-m4v,video/*" class="sr-only">
            <p data-reel-error class="hidden text-red-600 text-xs mt-2"></p>
            <div data-reel-preview class="hidden mt-4">
              <video class="w-40 rounded-xl border border-brand-navy/10 bg-brand-ink" muted playsinline controls preload="metadata"></video>
            </div>
          </div>

          <div>
            <label for="guest_name" class="block text-sm font-medium text-brand-navy/70 mb-1.5">Your name</label>
            <input id="guest_name" name="guest_name" type="text" required maxlength="60" value="<?php echo e($old['name'] ?? ''); ?>" placeholder="How you'd like to be credited" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua">
          </div>
          <div>
            <label for="location" class="block text-sm font-medium text-brand-navy/70 mb-1.5">Where you're from <span class="text-brand-navy/40">(optional)</span></label>
            <input id="location" name="location" type="text" maxlength="60" value="<?php echo e($old['location'] ?? ''); ?>" placeholder="e.g. Manchester, UK" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua">
          </div>
          <div>
            <label for="guest_email" class="block text-sm font-medium text-brand-navy/70 mb-1.5">Email <span class="text-brand-navy/40">(optional)</span></label>
            <input id="guest_email" name="guest_email" type="email" value="<?php echo e($old['email'] ?? ''); ?>" placeholder="you@email.com" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua">
          </div>
          <div>
            <label for="boat_name" class="block text-sm font-medium text-brand-navy/70 mb-1.5">Which boat? <span class="text-brand-navy/40">(optional)</span></label>
            <select id="boat_name" name="boat_name" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua cursor-pointer">
              <option value="">Not sure / not listed</option>
              <?php foreach ($fleet as $b): ?>
              <option value="<?php echo e($b['name']); ?>" <?php echo ($old['boatName'] ?? '') === $b['name'] ? 'selected' : ''; ?>><?php echo e($b['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="sm:col-span-2">
            <label for="caption" class="block text-sm font-medium text-brand-navy/70 mb-1.5">A line about the day <span class="text-brand-navy/40">(optional)</span></label>
            <textarea id="caption" name="caption" rows="3" maxlength="280" placeholder="Blue Lagoon from Latsi — best day of the trip." class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua resize-none"><?php echo e($old['caption'] ?? ''); ?></textarea>
          </div>

          <div class="sm:col-span-2">
            <label class="flex items-start gap-3 text-sm text-brand-navy/70 cursor-pointer">
              <input type="checkbox" name="consent" value="1" required class="mt-0.5 h-4 w-4 shrink-0 rounded border-brand-navy/25 text-brand-aqua focus:ring-brand-aqua cursor-pointer">
              <span>This is my own video, everyone recognisable in it is happy to appear, and BoatRent Cyprus may publish it on the website and social channels. I can ask for it to be taken down at any time.</span>
            </label>
            <!-- Links live outside the <label>: inside it, clicking one would
                 navigate away *and* toggle the checkbox. -->
            <p class="text-xs text-brand-navy/45 mt-2 pl-7">Full detail in our <a href="/terms#user-content" class="underline hover:text-brand-aquaD cursor-pointer">Terms</a> and <a href="/privacy" class="underline hover:text-brand-aquaD cursor-pointer">Privacy Policy</a>.</p>
          </div>

          <div class="sm:col-span-2">
            <button type="submit" data-reel-submit class="inline-flex items-center justify-center gap-2 bg-brand-gold hover:bg-brand-goldL text-brand-ink font-semibold px-8 py-3.5 rounded-full transition-colors duration-200 cursor-pointer disabled:opacity-60 disabled:cursor-wait">
              <span data-reel-submit-label>Send My Reel</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
