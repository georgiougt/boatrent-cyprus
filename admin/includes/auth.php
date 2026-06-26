<?php
/** Admin authentication helpers. Include at the top of every protected admin page. */
require_once __DIR__ . '/../../includes/functions.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function admin_logged_in(): bool
{
    return !empty($_SESSION['admin_id']);
}

function require_admin(): void
{
    if (!admin_logged_in()) {
        header('Location: /admin/login.php');
        exit;
    }
}

function admin_username(): string
{
    return $_SESSION['admin_username'] ?? 'admin';
}

function attempt_login(string $username, string $password): bool
{
    $stmt = db()->prepare('SELECT * FROM admins WHERE username = ?');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int) $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        return true;
    }
    return false;
}

/* Dashboard metric helpers */
function admin_stats(): array
{
    $pdo = db();
    return [
        'boats'      => (int) $pdo->query('SELECT COUNT(*) FROM boats')->fetchColumn(),
        'active'     => (int) $pdo->query("SELECT COUNT(*) FROM boats WHERE status='active'")->fetchColumn(),
        'inquiries'  => (int) $pdo->query('SELECT COUNT(*) FROM inquiries')->fetchColumn(),
        'new'        => (int) $pdo->query("SELECT COUNT(*) FROM inquiries WHERE status='new'")->fetchColumn(),
    ];
}
