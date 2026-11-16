# Library Management System (PHP)

A small library system built with plain PHP 8 and JSON file storage. No database needed.

## Run it
1. Install PHP 8.0 or later.
2. In this folder, optionally load sample data: `php seed.php`
3. Start the server: `php -S localhost:8000`
4. Open http://localhost:8000 and log in with **admin / admin123** (change it after installing).

The `data/` folder must be writable.

## Rules
14-day loan, Rs. 2 fine per day late, max 3 books per member.
