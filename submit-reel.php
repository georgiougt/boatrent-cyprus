<?php
/**
 * Receive a guest reel (short video testimonial) and park it in the moderation
 * queue. Nothing a guest uploads is ever shown on the site until an admin
 * approves it in /admin/reels.php.
 */
require_once __DIR__ . '/includes/functions.php';
session_start();

$redirect = '/share-your-reel';

$fail = function (string $message) use ($redirect) {
    $_SESSION['flash_error'] = $message;
    header('Location: ' . $redirect . '#share');
    exit;
};

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $redirect);
    exit;
}

// A body larger than post_max_size is discarded by PHP before it reaches us:
// $_POST and $_FILES arrive empty, which would otherwise look like a CSRF
// failure. Catch it here so the guest gets the real reason.
if (empty($_POST) && empty($_FILES) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    $fail('That video is too large for the server to accept. Please keep it under ' . human_bytes(reel_max_bytes()) . '.');
}

if (!csrf_check()) {
    $fail('Your session expired. Please try again.');
}

// Honeypot — real people leave this hidden field empty.
if (trim($_POST['website'] ?? '') !== '') {
    $_SESSION['flash_success'] = 'Thanks! Your reel is with our team for review.';
    header('Location: ' . $redirect . '#share');
    exit;
}

$name     = trim($_POST['guest_name'] ?? '');
$email    = trim($_POST['guest_email'] ?? '');
$location = trim($_POST['location'] ?? '');
$boatName = trim($_POST['boat_name'] ?? '');
$caption  = trim($_POST['caption'] ?? '');
$consent  = !empty($_POST['consent']);
$ip       = $_SERVER['REMOTE_ADDR'] ?? '';

$_SESSION['old_reel'] = compact('name', 'email', 'location', 'boatName', 'caption');

if ($name === '') {
    $fail('Please tell us your name so we can credit the reel.');
}
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $fail('That email address doesn\'t look right — leave it blank or correct it.');
}
if (!$consent) {
    $fail('Please confirm you filmed the reel and are happy for us to publish it.');
}

// Light rate limit: no more than 3 reels per IP per hour.
if ($ip !== '') {
    $recent = db()->prepare("SELECT COUNT(*) FROM reels WHERE ip = ? AND created_at > datetime('now', '-1 hour')");
    $recent->execute([$ip]);
    if ((int) $recent->fetchColumn() >= 3) {
        $fail('You\'ve just sent us a few reels — please try again in an hour.');
    }
}

/* ---------------- The video ---------------- */

$file = $_FILES['reel'] ?? null;
if (!$file || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
    $fail('Please choose a video to upload.');
}
if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
    $fail('That video is too large. The limit is ' . human_bytes(reel_max_bytes()) . '.');
}
if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
    $fail('The upload didn\'t finish. Please check your connection and try again.');
}
if ((int) $file['size'] > reel_max_bytes()) {
    $fail('That video is ' . human_bytes((int) $file['size']) . '. Please trim it down to ' . human_bytes(reel_max_bytes()) . ' or less.');
}

// Trust the sniffed type, not the browser-supplied one or the file name.
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = (string) $finfo->file($file['tmp_name']);
$allowed = reel_allowed_types();
if (!isset($allowed[$mime])) {
    $fail('That file isn\'t a video we can play. Please upload an MP4, MOV or WebM.');
}
$ext = $allowed[$mime];

$dir = reels_dir();
if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
    $fail('We couldn\'t store your reel just now. Please try again shortly.');
}

$basename  = date('Ymd') . '-' . bin2hex(random_bytes(8));
$videoFile = $dir . '/' . $basename . '.' . $ext;
if (!move_uploaded_file($file['tmp_name'], $videoFile)) {
    $fail('We couldn\'t store your reel just now. Please try again shortly.');
}
@chmod($videoFile, 0644);
$videoPath = '/uploads/reels/' . $basename . '.' . $ext;

/* ---------------- Optional poster frame ---------------- */
// The browser grabs a still from the video before uploading (see js/main.js) so
// the homepage grid can show images instead of loading every video at once.
$posterPath = null;
$posterData = (string) ($_POST['poster'] ?? '');
if (strpos($posterData, 'data:image/jpeg;base64,') === 0) {
    $binary = base64_decode(substr($posterData, 23), true);
    // Cap the still at 500 KB and require it to actually decode as an image.
    if ($binary !== false && strlen($binary) <= 512000 && @getimagesizefromstring($binary) !== false) {
        $posterFile = $dir . '/' . $basename . '.jpg';
        if (@file_put_contents($posterFile, $binary) !== false) {
            @chmod($posterFile, 0644);
            $posterPath = '/uploads/reels/' . $basename . '.jpg';
        }
    }
}

$stmt = db()->prepare("
    INSERT INTO reels (guest_name, guest_email, location, boat_name, caption,
                       video_path, poster_path, mime, filesize, status, on_home, ip)
    VALUES (:guest_name, :guest_email, :location, :boat_name, :caption,
            :video_path, :poster_path, :mime, :filesize, 'pending', 0, :ip)
");
$stmt->execute([
    ':guest_name'  => $name,
    ':guest_email' => $email !== '' ? $email : null,
    ':location'    => $location !== '' ? $location : null,
    ':boat_name'   => $boatName !== '' ? $boatName : null,
    ':caption'     => $caption !== '' ? $caption : null,
    ':video_path'  => $videoPath,
    ':poster_path' => $posterPath,
    ':mime'        => $mime,
    ':filesize'    => (int) $file['size'],
    ':ip'          => $ip,
]);

unset($_SESSION['old_reel']);
$_SESSION['flash_success'] = 'Thanks ' . $name . '! Your reel is uploaded and with our team — we\'ll review it before it goes live on the site.';
header('Location: ' . $redirect . '#share');
exit;
