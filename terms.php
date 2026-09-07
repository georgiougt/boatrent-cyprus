<?php
require_once __DIR__ . '/includes/legal.php';

$pageTitle = 'Terms & Conditions';
$pageDescription = 'The terms on which you may use the BoatRent Cyprus website and send a charter inquiry — our role as an intermediary, how a booking is actually formed, and the limits of our responsibility.';
$canonical = base_url() . '/terms';

$identity = legal_identity_html();

$sections = [];

$sections[] = ['id' => 'about', 'title' => 'These terms, and who you are dealing with', 'html' => <<<HTML
<p>These terms govern your use of the BoatRent Cyprus website and any inquiry you send through it. By using the site you accept them. If you do not, please do not use the site.</p>
{$identity}
<p>They are written to be read. Where a clause matters more than the others — because it decides who is responsible if something goes wrong — it is highlighted.</p>
HTML];

$sections[] = ['id' => 'our-role', 'title' => 'Our role: we are an intermediary', 'html' => <<<'HTML'
<div class="legal-note">
  <p><strong>Read this one first.</strong> BoatRent Cyprus does not own, crew, operate or maintain any of the vessels listed on this site. Every boat is owned and run by an independent, licensed <strong>charter operator</strong>. We put you in touch with them, help arrange the details, and pass information both ways. The charter itself is provided to you by the operator, under a contract between you and them.</p>
</div>
<p>What follows from that:</p>
<ul>
  <li>The operator is responsible for the vessel, its licensing and insurance, its crew, and the conduct of the trip.</li>
  <li>The operator's own terms — including its cancellation, deposit, damage and refund rules — apply to your charter alongside these terms. Ask for them before you pay anything, and read them.</li>
  <li>We are responsible for our own conduct: the accuracy of what we tell you, the care we take passing your inquiry on, and how we handle your data.</li>
</ul>
<p>Where we describe a boat as "our fleet", we mean the boats we represent, not boats we own.</p>
HTML];

$sections[] = ['id' => 'inquiry', 'title' => 'An inquiry is not a booking', 'html' => <<<'HTML'
<p>Sending an inquiry costs nothing and commits you to nothing. It is a request for availability and a price — not a reservation, and not a contract.</p>
<ol>
  <li>You send an inquiry through a boat page or the contact form.</li>
  <li>We check with the operator and come back to you with what is actually available and what it costs.</li>
  <li>If you want to go ahead, the booking is confirmed in writing and any deposit is paid <strong>directly to the operator</strong>, on the operator's terms.</li>
</ol>
<p>Nothing is held for you until that confirmation exists. Until then a boat may be taken by someone else, and neither we nor the operator are liable for that.</p>
<p>We take no payment through this website. No card details are collected here. If anyone asks you to pay BoatRent Cyprus through a channel we have not confirmed in writing, stop and contact us.</p>
HTML];

$sections[] = ['id' => 'listings', 'title' => 'Listings, prices and availability', 'html' => <<<'HTML'
<p>We take care to describe boats accurately using information from their operators, but:</p>
<ul>
  <li><strong>Prices are indicative</strong> until confirmed for your dates. They move with the season, the duration, the day of the week and what is included. Fuel, skipper, VAT, harbour dues, an advance provisioning allowance and extras may or may not be in a headline figure — the confirmed quote is what counts.</li>
  <li><strong>Specifications and photographs are illustrative.</strong> Capacity, cabins, equipment and finish are as reported by the operator. Photographs may show a sister vessel or a previous season's configuration.</li>
  <li><strong>Availability shown on the site is not live.</strong> A boat appearing here does not mean it is free on your dates.</li>
  <li><strong>Maximum capacity is set by the vessel's licence</strong> and is not negotiable, whatever a listing appears to allow.</li>
</ul>
<p>Where an obvious error appears — a price with a digit missing, say — we are not bound by it, and we will tell you the correct figure rather than hold you to a mistake.</p>
HTML];

$sections[] = ['id' => 'on-the-water', 'title' => 'Safety, weather and the skipper\'s authority', 'html' => <<<'HTML'
<div class="legal-note">
  <p>The skipper's decision is final on anything concerning the safety of the vessel and the people aboard. That includes shortening, altering or cancelling a trip for weather, sea state, mechanical trouble or the behaviour of guests. This is a legal duty owed by the master of a vessel, not a commercial preference, and no refund policy overrides it.</p>
</div>
<ul>
  <li>Trips may be cancelled or moved for weather. What happens then — a reschedule, a partial refund, a full refund — is governed by the operator's terms.</li>
  <li>You must follow the crew's safety instructions at all times, and are responsible for the children in your party.</li>
  <li>Alcohol, drugs, or behaviour that endangers others can end a charter early without refund.</li>
  <li>For a bareboat (self-drive) charter, you must genuinely hold any licence or certificate the operator and Cypriot law require, and produce it on request. Misrepresenting your qualifications can void the operator's insurance and leave you personally liable.</li>
  <li>Boats do not accommodate every access requirement. Tell us what you need before booking so we can check honestly rather than have you find out at the quay.</li>
  <li>Travel insurance is your responsibility and we strongly recommend it.</li>
</ul>
HTML];

$sections[] = ['id' => 'using-the-site', 'title' => 'Using this website', 'html' => <<<'HTML'
<p>You may browse the site, and send genuine inquiries about chartering a boat. You may not:</p>
<ul>
  <li>Send false, automated or bulk inquiries, or use the forms to send advertising or malicious content.</li>
  <li>Scrape, harvest or systematically copy the listings, descriptions or photographs, whether by hand or by software.</li>
  <li>Attempt to gain access to the administration area, the database, or any part of the site not made public.</li>
  <li>Interfere with the site's operation, or upload anything containing malware.</li>
  <li>Impersonate anyone, or misrepresent your connection with a person or business.</li>
</ul>
<p>We may withdraw access to anyone doing these things, and report unlawful activity to the authorities.</p>
HTML];

$sections[] = ['id' => 'user-content', 'title' => 'Guest reels and anything else you send us', 'html' => <<<'HTML'
<p>If you submit a video, photograph, review or other content ("your content"), the following applies.</p>

<h3>What you are promising us</h3>
<ul>
  <li>The content is <strong>yours</strong> — you filmed it or own the rights to it.</li>
  <li><strong>Everyone identifiable in it has agreed</strong> to it being published on our website and social channels. This matters: a recognisable face is that person's personal data, and you need their permission, not just your own. For anyone under 18, that means a parent or guardian.</li>
  <li>It contains no music, footage or branding belonging to someone else, and nothing unlawful, defamatory, offensive or misleading.</li>
</ul>

<h3>What you are granting us</h3>
<p>You give us a non-exclusive, royalty-free, worldwide licence to host, display, resize, crop, re-encode and share your content on this website and our social media, to promote BoatRent Cyprus. You keep ownership of it. We pay nothing for it and you are not entitled to a fee.</p>

<h3>Getting it taken down</h3>
<p>Ask us and we will remove your content — no reason needed, no argument. If you appear in someone else's reel and did not agree to it, tell us and we will take it down while we sort it out. Removal is not retroactive: copies already shared or downloaded by others are beyond our reach.</p>
<p>We review submissions before publishing, may decline any for any reason, and are not obliged to publish anything.</p>
HTML];

$sections[] = ['id' => 'ip', 'title' => 'Our intellectual property', 'html' => <<<'HTML'
<p>The design, text, layout, code and original photography on this site belong to BoatRent Cyprus or are used with permission, and are protected by copyright. Vessel photographs and specifications generally belong to the operators or their photographers.</p>
<p>You may view pages, print a copy for your own planning and share links. You may not republish, sell or reuse the content commercially without written permission. Deep-linking to a boat page is fine; presenting our pages inside your own site's frame or branding is not.</p>
HTML];

$sections[] = ['id' => 'liability', 'title' => 'Our responsibility, and its limits', 'html' => <<<'HTML'
<p>We take our part seriously, and we do not try to disclaim it entirely.</p>
<p><strong>Nothing in these terms limits our liability for</strong> death or personal injury caused by our negligence, fraud or fraudulent misrepresentation, or any liability that Cypriot or EU law does not allow us to exclude. If you are a consumer, your statutory rights are unaffected by anything on this page.</p>
<p>Subject to that:</p>
<ul>
  <li>We are <strong>not liable for the charter itself</strong> — the condition of the vessel, the conduct of the crew, delays, cancellations, injury or lost property during a trip. Those are matters between you and the operator, whose insurance covers them.</li>
  <li>We are not liable for the operator's insolvency, or its failure to honour a booking.</li>
  <li>The website is provided as it is. We do not promise it will be available uninterrupted or free of errors, and we may change or withdraw listings at any time.</li>
  <li>We are not liable for indirect or consequential loss — a missed flight, a ruined itinerary, lost enjoyment — arising from a fault in the website or a delay in passing on an inquiry.</li>
  <li>Where we are liable to you in connection with our own services, our total liability is limited to the commission we received in relation to your booking, or &euro;500 if no booking was made.</li>
</ul>
<p>This site is not a package travel organiser. If we ever combine transport, accommodation and a charter into a single package, the Package Travel Directive protections would apply to that package and we would say so explicitly at the time.</p>
HTML];

$sections[] = ['id' => 'complaints', 'title' => 'If something goes wrong', 'html' => <<<'HTML'
<p>Tell the skipper or operator at the time — most problems can be fixed on the spot, and a complaint raised days later is much harder to resolve. If that gets you nowhere, contact us with what happened, when, and the boat and date, and we will take it up with the operator on your behalf.</p>
<p>As an EU consumer you may also use the European Commission's online dispute resolution platform, and you can bring a complaint to the Cyprus Consumer Protection Service. Using either does not affect your right to go to court.</p>
HTML];

$sections[] = ['id' => 'general', 'title' => 'General terms', 'html' => <<<'HTML'
<ul>
  <li><strong>Changes.</strong> We may update these terms. The version published when you send an inquiry is the one that applies to it, and the date at the top of this page tells you which that is.</li>
  <li><strong>Links out.</strong> Where we link to another website, we do not control it and are not responsible for its content.</li>
  <li><strong>Severability.</strong> If a court finds any clause unenforceable, the rest stays in force.</li>
  <li><strong>No waiver.</strong> If we do not enforce a term straight away, we have not given up the right to enforce it later.</li>
  <li><strong>Governing law.</strong> These terms are governed by the law of the Republic of Cyprus, and the Cypriot courts have jurisdiction. If you are a consumer resident elsewhere in the EU, this does not deprive you of the protection of your own country's mandatory consumer law, or of your right to bring proceedings there.</li>
</ul>
HTML];

include __DIR__ . '/includes/header.php';
render_legal_page([
    'eyebrow' => 'The rules of the road',
    'title'   => 'Terms & Conditions',
    'intro'   => 'What you can expect from us, what we need from you, and — importantly — who is responsible for what once you are on the water.',
], $sections);
include __DIR__ . '/includes/footer.php';
