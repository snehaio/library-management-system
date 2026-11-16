<?php
// seed.php - command-line script that loads sample data. Usage: php seed.php
if (php_sapi_name() !== 'cli') { die('Run from the command line: php seed.php'); }
require_once __DIR__ . '/config.php';

$books = [
    ['Introduction to Algorithms','Thomas H. Cormen','9780262033848','Computer Science',4],
    ['Database System Concepts','Abraham Silberschatz','9780078022159','Computer Science',3],
    ['Operating System Concepts','Abraham Silberschatz','9781118063330','Computer Science',3],
    ['Computer Networks','Andrew S. Tanenbaum','9780132126953','Networking',2],
    ['Clean Code','Robert C. Martin','9780132350884','Software Engineering',2],
    ['Let Us C','Yashavant Kanetkar','9788183331630','Programming',5],
    ['Discrete Mathematics','Kenneth H. Rosen','9780073383095','Mathematics',2],
    ['Wings of Fire','A. P. J. Abdul Kalam','9788173711466','Biography',3],
];
$rows = []; $id = 1;
foreach ($books as $b) { $rows[] = ['id'=>$id++,'title'=>$b[0],'author'=>$b[1],'isbn'=>$b[2],'category'=>$b[3],'copies'=>$b[4],'available'=>$b[4]]; }
save('books', $rows);

$today = date('Y-m-d');
$members = [
    ['Aarav Sharma','aarav.sharma@example.com','9810011001'],
    ['Diya Verma','diya.verma@example.com','9810022002'],
    ['Rohan Gupta','rohan.gupta@example.com','9810033003'],
    ['Ananya Singh','ananya.singh@example.com','9810044004'],
    ['Kabir Mehta','kabir.mehta@example.com','9810055005'],
];
$rows = []; $id = 1;
foreach ($members as $m) { $rows[] = ['id'=>$id++,'name'=>$m[0],'email'=>$m[1],'phone'=>$m[2],'joined'=>$today]; }
save('members', $rows);

// [book_id, member_id, days ago issued, returned?]  due = issue + 14 days
$tx = [[6,3,50,true],[1,1,25,false],[5,3,18,false],[6,4,9,false],[4,1,6,false],[3,5,3,false],[8,2,2,false]];
$issues = []; $id = 1; $books = load('books');
foreach ($tx as $t) {
    $issue = date('Y-m-d', strtotime("-{$t[2]} days"));
    $due = date('Y-m-d', strtotime($issue . ' +14 days'));
    $issues[] = ['id'=>$id++,'book_id'=>$t[0],'member_id'=>$t[1],'issue_date'=>$issue,'due_date'=>$due,
                 'return_date'=>$t[3] ? $due : null,'fine'=>0];
    if (!$t[3]) { foreach ($books as &$b) { if ($b['id'] === $t[0]) { $b['available']--; } } unset($b); }
}
save('books', $books); save('issues', $issues);
echo "Sample data loaded: " . count($books) . " books, " . count($rows) . " members, " . count($issues) . " transactions.\n";
