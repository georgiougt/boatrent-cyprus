<?php
require_once __DIR__ . '/includes/legal.php';

$pageTitle = 'Cookie Policy';
$pageDescription = 'BoatRent Cyprus uses one strictly necessary session cookie and no tracking of any kind — no analytics, no advertising cookies, no third-party embeds. Here is exactly what is stored on your device and why.';
$canonical = base_url() . '/cookies';

$sections = [];

$sections[] = ['id' => 'summary', 'title' => 'The short version', 'html' => <<<'HTML'
<div class="legal-note">
  <p>This website sets <strong>one cookie</strong>, and only because the site cannot work without it. There is no analytics, no advertising, no tracking and no third-party content on these pages. That is also why you are not being interrupted by a consent pop-up — under the ePrivacy rules, strictly necessary cookies do not need your consent, and we do not set anything else.</p>
</div>
<p>The rest of this page explains precisely what is stored, so you can verify that claim rather than take our word for it.</p>
HTML];

$sections[] = ['id' => 'what-is-a-cookie', 'title' => 'What a cookie actually is', 'html' => <<<'HTML'
<p>A cookie is a small text file a website asks your browser to keep and send back on your next request. It lets a site recognise the same browser between pages. Related technologies do the same job differently — <strong>local storage</strong>, for instance, keeps a value in your browser but never transmits it to the server at all.</p>
<p>Cookies are commonly split into those that are <strong>strictly necessary</strong> for a site to function, and everything else — preferences, analytics and advertising. Only the first group can be set without asking you first. We use only the first group.</p>
HTML];

$sections[] = ['id' => 'what-we-set', 'title' => 'Exactly what we store on your device', 'html' => <<<'HTML'
<div class="legal-table-wrap">
<table>
  <thead>
    <tr><th>Name</th><th>Type</th><th>Purpose</th><th>Expires</th></tr>
  </thead>
  <tbody>
    <tr>
      <td>PHPSESSID</td>
      <td>Cookie &middot; strictly necessary</td>
      <td>Our server's session identifier. It keeps what you typed in the contact form if the page reloads with an error, carries the "message sent" confirmation across to the next page, and protects forms against cross-site request forgery. On the owner-login area it is what keeps an administrator signed in. It holds a random identifier — no name, email or browsing history.</td>
      <td>When you close your browser</td>
    </tr>
    <tr>
      <td>brc_seen_intro</td>
      <td>Local storage &middot; functional</td>
      <td>Remembers that you have already seen the short opening animation on the homepage, so it plays once rather than on every visit. The value is literally the character <code>1</code>. It never leaves your browser and is not sent to our server.</td>
      <td>Stays until you clear your browser data</td>
    </tr>
  </tbody>
</table>
</div>
<p>That is the complete list. If you would like to check it yourself, open your browser's developer tools, go to the storage or application panel, and look at what this domain has set.</p>
HTML];

$sections[] = ['id' => 'not-used', 'title' => 'What we deliberately do not use', 'html' => <<<'HTML'
<ul>
  <li><strong>No analytics cookies.</strong> We do not run Google Analytics, Plausible, Matomo or anything equivalent.</li>
  <li><strong>No advertising or remarketing cookies.</strong> No Meta pixel, no Google Ads tag, no conversion tracking.</li>
  <li><strong>No social media embeds.</strong> Our Instagram and Facebook links are ordinary links — they load nothing from those companies until you click.</li>
  <li><strong>No third-party fonts.</strong> Typefaces are served from our own server, so loading a page makes no request to Google Fonts and reveals your IP address to no one but us.</li>
  <li><strong>No video or map embeds.</strong> Guest reels are files on our own server, not YouTube or Vimeo players.</li>
</ul>
<p>The practical effect is that visiting this site does not tell any other company that you were here.</p>
HTML];

$sections[] = ['id' => 'control', 'title' => 'How to control or delete them', 'html' => <<<'HTML'
<p>You are in charge of what your browser keeps. Every major browser lets you view and delete cookies and local storage, block them by site, or clear everything when you close it — look under Settings for "Privacy", "Cookies" or "Site data". Private or incognito windows discard both automatically when the window closes.</p>
<div class="legal-note">
  <p>Worth knowing before you block ours: if you refuse the session cookie, the contact and inquiry forms will fail their security check and you will not be able to send us a message. Nothing else on the site depends on it — you can browse the whole fleet with cookies blocked.</p>
</div>
HTML];

$sections[] = ['id' => 'changes', 'title' => 'If this ever changes', 'html' => <<<'HTML'
<p>If we later add anything that is not strictly necessary — analytics to see which boats get looked at, say, or an advertising tag — the law requires us to ask for your consent <strong>before</strong> it loads, to make refusing as easy as accepting, and to let you change your mind later. In that case a consent banner will appear and this page will be updated to list the new cookies and name the companies behind them.</p>
<p>Until you see such a banner, you can take it that nothing on this page has changed. The "last updated" date at the top confirms the version you are reading, and any questions are welcome at our <a href="/contact">contact page</a>.</p>
HTML];

include __DIR__ . '/includes/header.php';
render_legal_page([
    'eyebrow' => 'One cookie, no tracking',
    'title'   => 'Cookie Policy',
    'intro'   => 'A complete and verifiable list of what this website stores on your device — which is far less than most.',
], $sections);
include __DIR__ . '/includes/footer.php';
