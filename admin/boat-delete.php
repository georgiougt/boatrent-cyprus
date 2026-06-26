<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id) {
        $stmt = db()->prepare('DELETE FROM boats WHERE id = ?');
        $stmt->execute([$id]);
        $_SESSION['admin_flash'] = 'Boat deleted.';
    }
}
header('Location: /admin/boats.php');
exit;
