<?php
include 'header.php';
$books = load('books'); $issues = load('issues');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (($_POST['action'] ?? '') === 'add') {
        $title = trim($_POST['title'] ?? ''); $author = trim($_POST['author'] ?? '');
        $isbn = trim($_POST['isbn'] ?? ''); $cat = trim($_POST['category'] ?? '');
        $copies = (int)($_POST['copies'] ?? 0);
        if ($title === '' || $author === '' || $copies < 1) { flash('Title, author and at least 1 copy are required.', 'err'); }
        else {
            $dup = false;
            foreach ($books as $b) { if ($isbn !== '' && $b['isbn'] === $isbn) { $dup = true; } }
            if ($dup) { flash('A book with this ISBN already exists.', 'err'); }
            else {
                $books[] = ['id' => next_id($books), 'title' => $title, 'author' => $author, 'isbn' => $isbn,
                            'category' => $cat, 'copies' => $copies, 'available' => $copies];
                save('books', $books); flash('Book added successfully.');
            }
        }
    }
    if (($_POST['action'] ?? '') === 'delete') {
        $id = (int)$_POST['id']; $inUse = false;
        foreach ($issues as $i) { if ((int)$i['book_id'] === $id && $i['return_date'] === null) { $inUse = true; } }
        if ($inUse) { flash('This book is currently issued and cannot be deleted.', 'err'); }
        else { save('books', array_filter($books, fn($b) => (int)$b['id'] !== $id)); flash('Book deleted.'); }
    }
    header('Location: books.php'); exit;
}
$q = trim($_GET['q'] ?? '');
$list = array_filter($books, fn($b) => $q === '' || stripos($b['title'] . ' ' . $b['author'] . ' ' . $b['isbn'] . ' ' . $b['category'], $q) !== false);
?>
<h2>Books</h2>
<div class="box">
    <form method="post">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="add">
        <input type="text" name="title" placeholder="Title" required>
        <input type="text" name="author" placeholder="Author" required>
        <input type="text" name="isbn" placeholder="ISBN">
        <input type="text" name="category" placeholder="Category">
        <input type="number" name="copies" min="1" value="1" style="width:70px">
        <button class="btn green" type="submit">Add book</button>
    </form>
</div>
<div class="box">
    <form method="get"><input type="text" name="q" value="<?= e($q) ?>" placeholder="Search title, author, ISBN or category" style="width:360px">
    <button class="btn" type="submit">Search</button></form>
    <table>
        <tr><th>ID</th><th>Title</th><th>Author</th><th>ISBN</th><th>Category</th><th>Available</th><th></th></tr>
        <?php foreach ($list as $b): ?>
        <tr><td><?= (int)$b['id'] ?></td><td><?= e($b['title']) ?></td><td><?= e($b['author']) ?></td><td><?= e($b['isbn']) ?></td>
            <td><?= e($b['category']) ?></td><td><?= (int)$b['available'] ?> / <?= (int)$b['copies'] ?></td>
            <td><form method="post" style="margin:0"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
                <button class="btn red" type="submit">Delete</button></form></td></tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include 'footer.php'; ?>
