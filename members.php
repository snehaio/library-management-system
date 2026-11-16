<?php
include 'header.php';
$members = load('members'); $issues = load('issues');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $name = trim($_POST['name'] ?? ''); $email = trim($_POST['email'] ?? ''); $phone = trim($_POST['phone'] ?? '');
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) { flash('Enter a name and a valid email address.', 'err'); }
    elseif ($phone !== '' && !preg_match('/^[0-9]{10}$/', $phone)) { flash('Phone number must have 10 digits.', 'err'); }
    else {
        $dup = false;
        foreach ($members as $m) { if (strcasecmp($m['email'], $email) === 0) { $dup = true; } }
        if ($dup) { flash('A member with this email already exists.', 'err'); }
        else {
            $members[] = ['id' => next_id($members), 'name' => $name, 'email' => $email, 'phone' => $phone, 'joined' => date('Y-m-d')];
            save('members', $members); flash('Member registered.');
        }
    }
    header('Location: members.php'); exit;
}
?>
<h2>Members</h2>
<div class="box">
    <form method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="text" name="name" placeholder="Full name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="phone" placeholder="Phone (10 digits)">
        <button class="btn green" type="submit">Register member</button>
    </form>
</div>
<div class="box">
    <table>
        <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Joined</th><th>Books held</th></tr>
        <?php foreach ($members as $m):
            $held = count(array_filter($issues, fn($i) => (int)$i['member_id'] === (int)$m['id'] && $i['return_date'] === null)); ?>
        <tr><td><?= (int)$m['id'] ?></td><td><?= e($m['name']) ?></td><td><?= e($m['email']) ?></td>
            <td><?= e($m['phone']) ?></td><td><?= e($m['joined']) ?></td><td><?= $held ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include 'footer.php'; ?>
