<?php
// board name
define('BOARD', 'kidpen board');

// board description
define('ABOUT', 'yet another message board');

// admin password
define('PASSWORD', 'pen');

// admin name
define('ADMIN', 'kid');

// admin mail
define('MAIL', '');

// admin homepage
define('HOMEPAGE', '');

// sqlite3 database, rename it first for security.
define('DATABASE', './db/board.db');

// how many items displayed in one page.
define('PAGEITEM', 10);

// if comment reply function enabled. TRUE or FALSE
define('COMMENT', TRUE);

// sort by post date time, regardless of new reply. TRUE or FALSE
define('SORTBYPOST', FALSE);

// your board style template
define('TEMPLATE', 'default');

// your time zone
define('TIMEZONE', 'Asia/Taipei');


// You can not pass here.
// We endure no flaw.
error_reporting(E_ALL | E_STRICT);
date_default_timezone_set(TIMEZONE);
define('PORT', $_SERVER['SERVER_PORT'] == '80' ? '' : ':'.$_SERVER['SERVER_PORT']);
$tmp = explode('/',$_SERVER['PHP_SELF']);
define('URL', '//'.$_SERVER['HTTP_HOST'].str_replace('/'.array_pop($tmp), '', $_SERVER['PHP_SELF']).PORT.'/');	// XXX: http/https

if (!file_exists(DATABASE)) {
	if (!touch(DATABASE))
		die('Could not create new database, check your database directory previlege or assign an existing database.');
}

if (!is_writable(DATABASE)) {
	die('Could not open database file! Check the db directory and '.DATABASE.' previlege, they must be writable.');
}

try {
	$db = new PDO('sqlite:'.DATABASE);
}
catch (PDOException $e) {
	print 'Connection failed: ' . $e->getMessage();
}

if (filesize(DATABASE) == 0) {	// Initialize Database
	$db->exec("
	CREATE TABLE post(
		id INTEGER PRIMARY KEY,
		reply INTEGER,
		title TEXT,
		datetime TEXT,
		change TEXT,
		content TEXT,
		author TEXT,
		password TEXT,
		ip TEXT,
		mail TEXT,
		homepage TEXT
	)");
}