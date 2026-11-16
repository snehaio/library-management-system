<?php
require_once 'config.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    foreach (load('users') as $user) {
        if ($user['username'] === $u && password_verify($p, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = $u;
            header('Location: index.php'); exit;
        }
    }
    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Login - Library Management System</title>
<link rel="stylesheet" href="style.css"></head><body>
<div class="login">
    <h2>Library Management System</h2>
    <p class="small">Librarian login</p>
    <?php if ($error): ?><div class="msg err"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <label>Username</label><input type="text" name="username" required>
        <label>Password</label><input type="password" name="password" required>
        <button class="btn" type="submit">Login</button>
    </form>
</div></body></html>
