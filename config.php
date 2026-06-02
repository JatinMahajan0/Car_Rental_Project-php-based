<?php
session_start();

$host    = getenv('DB_HOST') ?: '127.0.0.1';
$port    = getenv('DB_PORT') ?: '3306';        // Standard XAMPP = 3306. Change if yours differs.
$db      = getenv('DB_NAME') ?: 'car_rental_elite';
$user    = getenv('DB_USER') ?: 'root';
$pass    = getenv('DB_PASS') ?: '';
$charset = 'utf8mb4';

$dsn     = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    if ($e->getCode() == 1049) {
        try {
            $pdo_init = new PDO("mysql:host=$host;port=$port;charset=$charset", $user, $pass, $options);
            $pdo_init->exec("CREATE DATABASE IF NOT EXISTS `$db`");
            $pdo = new PDO($dsn, $user, $pass, $options);
            $sql_file = __DIR__ . '/includes/db.sql';
            if (file_exists($sql_file)) {
                $sql = file_get_contents($sql_file);
                $pdo->exec($sql);
            }
        } catch (\PDOException $e2) {
            throw new \PDOException($e2->getMessage(), (int)$e2->getCode());
        }
    } else {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
}

// ── Auth helpers ──────────────────────────────────────────────
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin(string $returnUrl = ''): void {
    if (!isLoggedIn()) {
        $url = '/car-rental/login.php';
        if ($returnUrl) $url .= '?return_url=' . urlencode($returnUrl);
        redirect($url);
    }
}

function requireAdmin(): void {
    requireLogin();
    if (!isAdmin()) redirect('/car-rental/index.php');
}

function redirect(string $url): never {
    header("Location: $url");
    exit;
}

// ── Flash messages ────────────────────────────────────────────
function flash(string $key, string $msg, string $type = 'success'): void {
    $_SESSION['flash'][$key] = ['msg' => $msg, 'type' => $type];
}

function getFlash(string $key): ?array {
    if (isset($_SESSION['flash'][$key])) {
        $f = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $f;
    }
    return null;
}

// ── CSRF ──────────────────────────────────────────────────────
function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfVerify(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('Invalid CSRF token.');
    }
}

function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . csrfToken() . '">';
}

// ── Notifications ─────────────────────────────────────────────
function notify(PDO $pdo, int $userId, string $message, string $type = 'info', string $linkRef = ''): void {
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, type, link_ref) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $message, $type, $linkRef]);
}

function unreadNotifCount(PDO $pdo, int $userId): int {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND status = 'unread'");
    $stmt->execute([$userId]);
    return (int)$stmt->fetchColumn();
}
?>
