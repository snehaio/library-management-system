<?php
include 'header.php';
$books = load('books'); $members = load('members'); $issues = load('issues');
$today = date('Y-m-d');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (($_POST['action'] ?? '') === 'issue') {
        $b = find_by_id($books, $_POST['book_id'] ?? 0); $m = find_by_id($members, $_POST['member_id'] ?? 0);
        $held = 0;
        foreach ($issues as $i) { if ($m && (int)$i['member_id'] === (int)$m['id'] && $i['return_date'] === null) { $held++; } }
        if (!$b || !$m) { flash('Select a valid book and member.', 'err'); }
        elseif ($b['available'] < 1) { flash('No copies of this book are available.', 'err'); }
        elseif ($held >= 3) { flash('A member can hold at most 3 books at a time.', 'err'); }
        else {
            foreach ($books as &$bk) { if ((int)$bk['id'] === (int)$b['id']) { $bk['available']--; } } unset($bk);
            $issues[] = ['id' => next_id($issues), 'book_id' => $b['id'], 'member_id' => $m['id'], 'issue_date' => $today,
                         'due_date' => date('Y-m-d', strtotime('+' . LOAN_DAYS . ' days')), 'return_date' => null, 'fine' => 0];
            save('books', $books); save('issues', $issues);
            flash('Issued "' . $b['title'] . '" to ' . $m['name'] . '. Due in ' . LOAN_DAYS . ' days.');
        }
    }
    if (($_POST['action'] ?? '') === 'return') {
        foreach ($issues as &$i) {
            if ((int)$i['id'] === (int)$_POST['issue_id'] && $i['return_date'] === null) {
                $late = max(0, (int)floor((strtotime($today) - strtotime($i['due_date'])) / 86400));
                $i['return_date'] = $today; $i['fine'] = $late * FINE_PER_DAY;
                foreach ($books as &$bk) { if ((int)$bk['id'] === (int)$i['book_id']) { $bk['available']++; } } unset($bk);
                flash($late > 0 ? "Book returned. Fine: Rs. {$i['fine']} ($late days late)." : 'Book returned on time. No fine.');
            }
        } unset($i);
        save('books', $books); save('issues', $issues);
    }
    header('Location: issue.php'); exit;
}
$active = array_filter($issues, fn($i) => $i['return_date'] === null);
?>
<h2>Issue / Return</h2>
<div class="box">
    <form method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="issue">
        <select name="book_id"><option value="">Select book</option>
            <?php foreach ($books as $b): if ($b['available'] > 0): ?><option value="<?= (int)$b['id'] ?>"><?= e($b['title']) ?> (<?= (int)$b['available'] ?> left)</option><?php endif; endforeach; ?>
        </select>
        <select name="member_id"><option value="">Select member</option>
            <?php foreach ($members as $m): ?><option value="<?= (int)$m['id'] ?>"><?= e($m['name']) ?></option><?php endforeach; ?>
        </select>
        <button class="btn green" type="submit">Issue book</button>
        <div class="small">Loan period <?= LOAN_DAYS ?> days. Fine Rs. <?= FINE_PER_DAY ?> per day after the due date. Maximum 3 books per member.</div>
    </form>
</div>
<div class="box">
    <h2>Books currently issued</h2>
    <table>
        <tr><th>Book</th><th>Member</th><th>Issued</th><th>Due</th><th>Fine if returned today</th><th></th></tr>
        <?php foreach ($active as $i):
            $b = find_by_id($books, $i['book_id']); $m = find_by_id($members, $i['member_id']);
            $late = max(0, (int)floor((strtotime($today) - strtotime($i['due_date'])) / 86400)); ?>
        <tr class="<?= $late > 0 ? 'late' : '' ?>"><td><?= e($b['title'] ?? '') ?></td><td><?= e($m['name'] ?? '') ?></td>
            <td><?= e($i['issue_date']) ?></td><td><?= e($i['due_date']) ?></td><td>Rs. <?= $late * FINE_PER_DAY ?></td>
            <td><form method="post" style="margin:0"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="return"><input type="hidden" name="issue_id" value="<?= (int)$i['id'] ?>">
                <button class="btn" type="submit">Return</button></form></td></tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include 'footer.php'; ?>
