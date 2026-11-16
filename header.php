<?php require_once 'config.php'; require_login(); $page = basename($_SERVER['SCRIPT_NAME']); ?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Library Management System</title>
<link rel="stylesheet" href="style.css"></head><body>
<div class="topbar"><div class="wrap">
    <span class="brand">Library Management System</span>
    <span class="user">Logged in as <?= e($_SESSION['user']) ?> | <a href="logout.php">Logout</a></span>
</div></div>
<div class="nav"><div class="wrap">
    <a class="<?= $page=='index.php'?'on':'' ?>" href="index.php">Dashboard</a>
    <a class="<?= $page=='books.php'?'on':'' ?>" href="books.php">Books</a>
    <a class="<?= $page=='members.php'?'on':'' ?>" href="members.php">Members</a>
    <a class="<?= $page=='issue.php'?'on':'' ?>" href="issue.php">Issue / Return</a>
</div></div>
<div class="wrap main">
<?php if ($f = flash()): ?><div class="msg <?= e($f[1]) ?>"><?= e($f[0]) ?></div><?php endif; ?>
