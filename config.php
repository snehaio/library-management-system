<?php
// config.php - session start, JSON storage helpers and shared utility functions
session_start();
define('DATA_DIR', __DIR__ . '/data/');
define('LOAN_DAYS', 14);
define('FINE_PER_DAY', 2);

if (!is_dir(DATA_DIR)) { mkdir(DATA_DIR, 0755, true); }

function load($name) {
    $path = DATA_DIR . $name . '.json';
    if (!file_exists($path)) { return []; }
    $rows = json_decode(file_get_contents($path), true);
    return is_array($rows) ? $rows : [];
}
function save($name, $rows) {
    file_put_contents(DATA_DIR . $name . '.json', json_encode(array_values($rows), JSON_PRETTY_PRINT), LOCK_EX);
}
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function next_id($rows) {
    $max = 0;
    foreach ($rows as $r) { $max = max($max, (int)$r['id']); }
    return $max + 1;
}
function require_login() {
    if (empty($_SESSION['user'])) { header('Location: login.php'); exit; }
}
function csrf_token() {
    if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(16)); }
    return $_SESSION['csrf'];
}
function check_csrf() {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(400); die('Invalid request token.'); }
}
function find_by_id($rows, $id) {
    foreach ($rows as $r) { if ((int)$r['id'] === (int)$id) { return $r; } }
    return null;
}
function flash($msg = null, $type = 'ok') {
    if ($msg !== null) { $_SESSION['flash'] = [$msg, $type]; return null; }
    $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f;
}
// create default admin account on first run
if (!file_exists(DATA_DIR . 'users.json')) {
    save('users', [['id' => 1, 'username' => 'admin', 'password' => password_hash('admin123', PASSWORD_DEFAULT)]]);
}
