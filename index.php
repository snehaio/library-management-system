<?php
include 'header.php';
$books = load('books'); $members = load('members'); $issues = load('issues');
$today = date('Y-m-d');
$active = array_filter($issues, fn($i) => $i['return_date'] === null);
$overdue = array_filter($active, fn($i) => $i['due_date'] < $today);
$copies = array_sum(array_column($books, 'copies'));
$recent = array_slice(array_reverse($issues), 0, 6);
?>
<h2>Dashboard</h2>
<div class="cards">
    <div class="card"><div class="n"><?= count($books) ?></div><div class="l">Book titles (<?= $copies ?> copies)</div></div>
    <div class="card"><div class="n"><?= count($members) ?></div><div class="l">Registered members</div></div>
    <div class="card"><div class="n"><?= count($active) ?></div><div class="l">Books currently issued</div></div>
    <div class="card warn"><div class="n"><?= count($overdue) ?></div><div class="l">Overdue returns</div></div>
</div>
<div class="box">
    <h2>Recent transactions</h2>
    <table>
        <tr><th>Book</th><th>Member</th><th>Issued</th><th>Due</th><th>Status</th></tr>
        <?php foreach ($recent as $i):
            $b = find_by_id($books, $i['book_id']); $m = find_by_id($members, $i['member_id']);
            if ($i['return_date'] !== null) { $s = '<span class="tag in">Returned</span>'; }
            elseif ($i['due_date'] < $today) { $s = '<span class="tag late">Overdue</span>'; }
            else { $s = '<span class="tag out">Issued</span>'; } ?>
        <tr><td><?= e($b['title'] ?? 'Deleted') ?></td><td><?= e($m['name'] ?? 'Deleted') ?></td>
            <td><?= e($i['issue_date']) ?></td><td><?= e($i['due_date']) ?></td><td><?= $s ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include 'footer.php'; ?>
