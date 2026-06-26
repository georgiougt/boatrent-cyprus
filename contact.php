<?php
$pageTitle = 'Contact';
require_once __DIR__ . '/includes/functions.php';
session_start();

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError   = $_SESSION['flash_error'] ?? null;
$old          = $_SESSION['old'] ?? [];
unset($_SESSION['flash_success'], $_SESSION['flash_error'], $_SESSION['old']);

$prefillSubject = trim($_GET['subject'] ?? '');
$canonical = base_url() . '/contact';
include __DIR__ . '/includes/header.php';
?>

<section class="relative pt-36 pb-16 px-6 bg-brand-ink overflow-hidden">
  <img src="https://images.unsplash.com/photo-1469796466635-455ede028aca?auto=format&fit=crop&w=2000&q=80" alt="" aria-hidden="true" class="absolute inset-0 w-full h-full object-cover opacity-20">
  <div class="relative max-w-4xl mx-auto text-center reveal">
    <p class="text-brand-goldL font-semibold uppercase tracking-[0.3em] text-xs mb-4">Get in touch</p>
    <h1 class="font-display text-5xl sm:text-6xl font-bold text-white">Contact Us</h1>
    <p class="text-white/70 mt-5 max-w-xl mx-auto">Questions about a boat, a booking, or listing your own vessel? We're here.</p>
  </div>
</section>

<section class="bg-brand-sand py-16 px-6">
  <div class="max-w-7xl mx-auto grid lg:grid-cols-5 gap-10">
    <div class="lg:col-span-2 reveal-left space-y-5">
      <?php
      $cards = [
        ['Email', 'hello@boatrentcyprus.com', 'mailto:hello@boatrentcyprus.com', 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
        ['Phone / WhatsApp', '+357 25 000 000', 'tel:+35725000000', 'M3 5a2 2 0 012-2h3.28a1 1 0 01.95.68l1.5 4.5a1 1 0 01-.5 1.2l-2.26 1.13a11 11 0 005.52 5.52l1.13-2.26a1 1 0 011.2-.5l4.5 1.5a1 1 0 01.68.95V19a2 2 0 01-2 2h-1C9.72 21 3 14.28 3 6V5z'],
        ['Office', 'Limassol Marina, Cyprus', '#', 'M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z'],
      ];
      foreach ($cards as $c): ?>
      <a <?php echo $c[2] !== '#' ? 'href="' . e($c[2]) . '"' : ''; ?> class="block bg-white rounded-2xl border border-brand-navy/10 p-6 hover:border-brand-aqua/40 transition-colors duration-300 <?php echo $c[2] !== '#' ? 'cursor-pointer' : ''; ?>">
        <div class="h-11 w-11 rounded-xl bg-brand-aqua/10 flex items-center justify-center text-brand-aqua mb-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo $c[3]; ?>"/></svg>
        </div>
        <h3 class="font-display text-lg font-semibold text-brand-ink"><?php echo e($c[0]); ?></h3>
        <p class="text-brand-navy/65 text-sm mt-1"><?php echo e($c[1]); ?></p>
      </a>
      <?php endforeach; ?>

      <div class="bg-white rounded-2xl border border-brand-navy/10 p-6">
        <h3 class="font-display text-lg font-semibold text-brand-ink mb-3">Office hours</h3>
        <ul class="text-sm text-brand-navy/65 space-y-1.5">
          <li class="flex justify-between gap-4"><span>Mon – Fri</span><span class="text-brand-ink">09:00 – 19:00</span></li>
          <li class="flex justify-between gap-4"><span>Saturday</span><span class="text-brand-ink">09:00 – 17:00</span></li>
          <li class="flex justify-between gap-4"><span>Sunday</span><span class="text-brand-ink">10:00 – 15:00</span></li>
        </ul>
      </div>
    </div>

    <div class="lg:col-span-3 reveal-right">
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

        <h2 class="font-display text-2xl font-semibold text-brand-ink mb-1">Send a message</h2>
        <p class="text-brand-navy/55 text-sm mb-7">We usually reply within a few hours during office hours.</p>

        <form action="/submit-inquiry.php" method="post" data-validate novalidate class="grid sm:grid-cols-2 gap-5">
          <?php echo csrf_field(); ?>
          <div>
            <label for="name" class="block text-sm font-medium text-brand-navy/70 mb-1.5">Full name</label>
            <input id="name" name="name" type="text" required value="<?php echo e($old['name'] ?? ''); ?>" placeholder="Your name" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua">
            <p data-error-for="name" class="hidden text-red-600 text-xs mt-1">Please enter your name.</p>
          </div>
          <div>
            <label for="email" class="block text-sm font-medium text-brand-navy/70 mb-1.5">Email</label>
            <input id="email" name="email" type="email" required value="<?php echo e($old['email'] ?? ''); ?>" placeholder="you@email.com" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua">
            <p data-error-for="email" class="hidden text-red-600 text-xs mt-1">Enter a valid email.</p>
          </div>
          <div>
            <label for="phone" class="block text-sm font-medium text-brand-navy/70 mb-1.5">Phone <span class="text-brand-navy/40">(optional)</span></label>
            <div class="flex gap-2">
              <label for="dial_code" class="sr-only">Country code</label>
              <select id="dial_code" name="dial_code" class="w-32 shrink-0 bg-brand-sand border border-brand-navy/10 rounded-lg px-2 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua cursor-pointer">
                <?php echo dial_code_options($old['dial_code'] ?? '+357'); ?>
              </select>
              <input id="phone" name="phone" type="tel" value="<?php echo e($old['phone'] ?? ''); ?>" placeholder="99 123456" class="flex-1 min-w-0 bg-brand-sand border border-brand-navy/10 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua">
            </div>
          </div>
          <div>
            <label for="city" class="block text-sm font-medium text-brand-navy/70 mb-1.5">Destination <span class="text-brand-navy/40">(optional)</span></label>
            <select id="city" name="city" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua cursor-pointer">
              <option value="">No preference</option>
              <?php foreach (get_cities() as $c): ?>
              <option value="<?php echo e($c['name']); ?>"><?php echo e($c['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="sm:col-span-2">
            <label for="message" class="block text-sm font-medium text-brand-navy/70 mb-1.5">Message</label>
            <textarea id="message" name="message" rows="5" required placeholder="How can we help?" class="w-full bg-brand-sand border border-brand-navy/10 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-aqua resize-none"><?php echo e($old['message'] ?? ($prefillSubject ? $prefillSubject . ': ' : '')); ?></textarea>
            <p data-error-for="message" class="hidden text-red-600 text-xs mt-1">Please enter a message.</p>
          </div>
          <div class="sm:col-span-2">
            <button type="submit" class="inline-flex items-center justify-center gap-2 bg-brand-gold hover:bg-brand-goldL text-brand-ink font-semibold px-8 py-3.5 rounded-full transition-colors duration-200 cursor-pointer">
              Send Message
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<section class="bg-white">
  <div class="reveal w-full h-[400px]">
    <iframe src="https://www.google.com/maps?q=Limassol+Marina,Cyprus&output=embed" class="w-full h-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="BoatRent Cyprus location — Limassol Marina"></iframe>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
