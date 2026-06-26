<?php
require_once __DIR__ . '/includes/functions.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /boats');
    exit;
}

$boatId = isset($_POST['boat_id']) && $_POST['boat_id'] !== '' ? (int) $_POST['boat_id'] : null;

// Resolve the boat once — used for the clean redirect and the dashboard label.
$inquiryBoat = $boatId ? get_boat($boatId) : null;
$redirect = $inquiryBoat ? '/boat/' . $inquiryBoat['slug'] : '/contact';

if (!csrf_check()) {
    $_SESSION['flash_error'] = 'Your session expired. Please try again.';
    header('Location: ' . $redirect);
    exit;
}

$name      = trim($_POST['name'] ?? '');
$email     = trim($_POST['email'] ?? '');
$phone     = trim($_POST['phone'] ?? '');
$dial_code = trim($_POST['dial_code'] ?? '');
$city      = trim($_POST['city'] ?? '');
$from    = trim($_POST['date_from'] ?? '');
$to      = trim($_POST['date_to'] ?? '');
$guests  = isset($_POST['guests']) && $_POST['guests'] !== '' ? (int) $_POST['guests'] : null;
$message = trim($_POST['message'] ?? '');

// Server-side validation
$errors = [];
if ($name === '')  $errors[] = 'name';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'email';

if ($errors) {
    $_SESSION['flash_error'] = 'Please add your name and a valid email so we can reply.';
    $_SESSION['old'] = compact('name', 'email', 'phone', 'dial_code', 'city', 'from', 'to', 'guests', 'message');
    header('Location: ' . $redirect . '#inquiry');
    exit;
}

// Prefix the country dial code to the number for storage (only if a number was given).
if ($phone !== '' && $dial_code !== '') {
    $phone = $dial_code . ' ' . $phone;
}

// Boat details for the dashboard (already fetched above)
$boatName = null;
if ($inquiryBoat) {
    $boatName = $inquiryBoat['name'];
    if (!$city) {
        $city = $inquiryBoat['city_name'];
    }
}

$stmt = db()->prepare("
    INSERT INTO inquiries (boat_id, boat_name, name, email, phone, city, date_from, date_to, guests, message, status)
    VALUES (:boat_id, :boat_name, :name, :email, :phone, :city, :date_from, :date_to, :guests, :message, 'new')
");
$stmt->execute([
    ':boat_id'   => $boatId,
    ':boat_name' => $boatName,
    ':name'      => $name,
    ':email'     => $email,
    ':phone'     => $phone,
    ':city'      => $city,
    ':date_from' => $from,
    ':date_to'   => $to,
    ':guests'    => $guests,
    ':message'   => $message,
]);

$_SESSION['flash_success'] = 'Thanks ' . $name . '! Your inquiry is in — our team will get back to you shortly.';
unset($_SESSION['old']);
header('Location: ' . $redirect . '#inquiry');
exit;
