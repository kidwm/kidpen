<?php
define('VERSION', '0.9.5');
if (!file_exists('config.php'))
	die('Write down your information and setting in "config-sample.php" then save it as "config.php" first.');
else
	require 'config.php';
session_start();

// Verify Handle
if (isset($_GET['verify'])) {
	$width = isset($_GET['width']) && $_GET['width'] < 600 ? $_GET['width'] : '50';
	$height = isset($_GET['height']) && $_GET['height'] < 200 ? $_GET['height'] : '20';
	$characters = isset($_GET['characters']) && $_GET['characters'] > 2 ? $_GET['characters'] : '4';
	mt_srand((double)microtime()*1000000);
	$code = '';
	$possible = '0123456789';
	for($i=0; $i<$characters; $i++){
		$code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
	}
	$_SESSION['code'] = $code;
	$image = imagecreate($width, $height) or die('GD image creating error.');
	$background_color = imagecolorallocate($image, 239, 239, 239);
	$text_color = imagecolorallocate($image, 0, 169, 225);
	$noise_color = imagecolorallocate($image, 200, 200, 200);
	imagefill($image,0,0,$background_color);
	imagestring($image, 5, 8, 2, $code, $text_color);
	for ($i=0; $i<($width*$height)/150; $i++) {
		imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $noise_color);
	}
	header("Content-type:image/png");
	imagepng($image);
	imagedestroy($image);
	exit;
}

if (!empty($_POST['admin']) && $_POST['admin'] == PASSWORD) {
	$_SESSION['admin'] = TRUE;
	header('location: '.URL);
}

if (isset($_GET['exit']) && isset($_SESSION['admin'])) {
	session_destroy();
	header('location: '.URL);
}

$admin = isset($_SESSION['admin']) ? $_SESSION['admin'] : FALSE;
$hint = isset($_SESSION['hint']) ? $_SESSION['hint'] : '';

// Post Handle
if (!empty($_POST) && isset($_POST['action'])) {
	switch($_POST['action']) {
		case 'post':
			$title = isset($_POST['title']) ? $_POST['title'] : '';
			$_SESSION['title'] = $title;
			$_SESSION['content'] = $_POST['content'];
			if (!$admin && !isset($_POST['author'])) {		//admin has logout
				hint('Admin has been logout!');
				header('location: '.URL);
				$db = NULL;		// close the database connection
				exit;
			}
			$pass = !empty($_POST['password']) ? hash('sha256', $_POST['password']) : '';
			if ($admin) {
				$author = ADMIN;
				$mail = MAIL;
				$homepage = HOMEPAGE;
			} else {
				$author = !empty($_POST['author']) ? trim($_POST['author']) : '';
				$mail = !empty($_POST['mail']) ? $_POST['mail'] : '';
				$homepage = !empty($_POST['homepage']) ? url_input($_POST['homepage']) : '';
			}
			$reply = 0;
			$content = trim($_POST['content']);
			$ok = TRUE;
			if ((!$admin && (empty($_SESSION['code']) || empty($_POST['verify']))) || !$admin && ($_POST['verify'] != $_SESSION['code'])) {
				$ok = FALSE;
				hint('Wrong verify code!');
			}
			if (empty($author)) {
				$ok = FALSE;
				hint('You forgot filling your name.');
			}
			if ($author == ADMIN && !$admin) {
				$ok = FALSE;
				hint('The name "'.$author.'" has been reserved for admin.');
			}
			if (empty($content)) {
				$ok = FALSE;
				hint('You should say something.');
			}
			if ($ok) {
				$title = htmlspecialchars($title,ENT_COMPAT,'UTF-8', false);
				$content = htmlspecialchars($_POST['content'],ENT_COMPAT,'UTF-8', false);
				$ip = ip2long($_SERVER['REMOTE_ADDR']);
				$stmt = $db->prepare("
				INSERT INTO post (id, reply, title, datetime, change, content, author, password, ip, mail, homepage)
				VALUES (NULL, :REPLY, :TITLE, datetime('now'), datetime('now'), :CONTENT, :AUTHOR, :PASS, :IP, :MAIL, :HOMEPAGE);
				");
				$stmt->bindValue(':REPLY', $reply);
				$stmt->bindValue(':TITLE', $title);
				$stmt->bindValue(':CONTENT', $content);
				$stmt->bindValue(':AUTHOR', $author);
				$stmt->bindValue(':PASS', $pass);
				$stmt->bindValue(':IP', $ip);
				$stmt->bindValue(':MAIL', $mail);
				$stmt->bindValue(':HOMEPAGE', $homepage);
				$stmt->execute();
				unset($_SESSION['title']);
				unset($_SESSION['content']);
			}
			unset($_SESSION['verify']);
			setcookie('AUTHOR', $author, time() + (86400 * 7));
			setcookie('MAIL', $mail, time() + (86400 * 7));
			setcookie('HOMEPAGE', $homepage, time() + (86400 * 7));
			header('location: '.URL);
			$db = NULL;		// close the database connection
			exit;
			break;
		case 'modify':
			$sth = $db->prepare('SELECT * FROM post WHERE id = '.$_POST['id']);
			$sth->execute();
			$sth->bindColumn('password', $password);
			$sth->fetch();
			if ($admin || (!empty($password) && $password == hash('sha256', $_POST['password']))) {
				$_SESSION['modify'] = $_POST['id'];
				header('location: '.URL.'?edit='.$_POST['id']);
			} else {
				hint('Wrong Password!');
				header('location: '.URL.'?modify='.$_POST['id']);
			}
			$db = NULL;		// close the database connection
			exit;
			break;
		case 'edit':
			$author = trim($_POST['author']);
			$content = trim($_POST['content']);
			$ok = TRUE;
			if (empty($author)) {
				$ok = FALSE;
				hint('You forgot filling your name.');
			}
			if ($author == ADMIN && !$admin) {
				$ok = FALSE;
				hint('The name "'.$author.'" has been reserved for admin.');
			}
			if (empty($content)) {
				$ok = FALSE;
				hint('You should say something.');
			}
			if ($ok) {
				$mail = $_POST['mail'];
				$homepage = url_input($_POST['homepage']);
				$title = htmlspecialchars($_POST['title'],ENT_COMPAT,'UTF-8', false);
				$content = htmlspecialchars($_POST['content'],ENT_COMPAT,'UTF-8', false);
				$id = $_POST['id'];
				$stmt = $db->prepare("
				UPDATE post SET title = :TITLE, change = datetime('now'), content = :CONTENT, author = :AUTHOR, mail = :MAIL, homepage = :HOMEPAGE WHERE id = :ID;
				");
				$stmt->bindValue(':TITLE', $title);
				$stmt->bindValue(':CONTENT', $content);
				$stmt->bindValue(':AUTHOR', $author);
				$stmt->bindValue(':MAIL', $mail);
				$stmt->bindValue(':HOMEPAGE', $homepage);
				$stmt->bindValue(':ID', $id);
				$stmt->execute();
				unset($_SESSION['modify']);
				header('location: '.URL);
			} else {
				header('location: '.URL.'?edit='.$_POST['id']);
			}
			$db = NULL;		// close the database connection
			exit;
			break;
		case 'reply':
			if(!COMMENT) {
				hint('Reply function has been disabled.');
				header('location: '.URL);
				$db = NULL;		// close the database connection
				exit;
			}
			$_SESSION['reply_title'] = $_POST['title'];
			$_SESSION['reply_content'] = $_POST['content'];
			if (!$admin && !isset($_POST['author'])) {		//admin has logout
				hint('Admin has been logout!');
				header('location: '.URL.'?reply='.$_POST['id']);
				$db = NULL;		// close the database connection
				exit;
			}
			$pass = !empty($_POST['password']) ? hash('sha256', $_POST['password']) : '';
			if ($admin) {
				$author = ADMIN;
				$mail = MAIL;
				$homepage = HOMEPAGE;
			} else {
				$author = trim($_POST['author']);
				$mail = $_POST['mail'];
				$homepage = $_POST['homepage'];
			}
			$reply = $_POST['id'];
			$content = trim($_POST['content']);
			$ok = TRUE;
			if ((!$admin && (empty($_SESSION['code']) || empty($_POST['verify']))) || !$admin && ($_POST['verify'] != $_SESSION['code'])) {
				$ok = FALSE;
				hint('Wrong verify code!');
			}
			if (empty($author)) {
				$ok = FALSE;
				hint('You forgot filling your name.');
			}
			if ($author == ADMIN && !$admin) {
				$ok = FALSE;
				hint('The name "'.$author.'" has been reserved for admin.');
			}
			if (empty($content)) {
				$ok = FALSE;
				hint('You should say something.');
			}
			if ($ok) {
				$title = htmlspecialchars($_POST['title'],ENT_COMPAT,'UTF-8', false);
				$content = htmlspecialchars($_POST['content'],ENT_COMPAT,'UTF-8', false);
				$ip = ip2long($_SERVER['REMOTE_ADDR']);
				$stmt = $db->prepare("
				INSERT INTO post (id, reply, title, datetime, change, content, author, password, ip, mail, homepage)
				VALUES (NULL, :REPLY, :TITLE, datetime('now'), datetime('now'), :CONTENT, :AUTHOR, :PASS, :IP, :MAIL, :HOMEPAGE);
				");
				$stmt->bindValue(':REPLY', $reply);
				$stmt->bindValue(':TITLE', $title);
				$stmt->bindValue(':CONTENT', $content);
				$stmt->bindValue(':AUTHOR', $author);
				$stmt->bindValue(':PASS', $pass);
				$stmt->bindValue(':IP', $ip);
				$stmt->bindValue(':MAIL', $mail);
				$stmt->bindValue(':HOMEPAGE', $homepage);
				$stmt->execute();
				$stmt = $db->exec("UPDATE post SET change = datetime('now') WHERE id = ".$reply);
				unset($_SESSION['reply_title']);
				unset($_SESSION['reply_content']);
			}
			unset($_SESSION['verify']);
			setcookie('AUTHOR', $author, time() + (86400 * 7));
			setcookie('MAIL', $mail, time() + (86400 * 7));
			setcookie('HOMEPAGE', $homepage, time() + (86400 * 7));
			header('location: '.URL.'?reply='.$_POST['id']);
			$db = NULL;		// close the database connection
			exit;
			break;
		case 'delete':
			$sth = $db->prepare('SELECT * FROM post WHERE id = '.$_POST['id']);
			$sth->execute();
			$sth->bindColumn('password', $password);
			$sth->fetch();
			if ($admin || (!empty($password) && $password == hash('sha256', $_POST['password']))) {
				$db->exec('DELETE FROM post WHERE id = '.$_POST['id']);
				$db->exec('DELETE FROM post WHERE reply = '.$_POST['id']);
				header('location: '.URL);
			} else {
				hint('Wrong Password!');
				header('location: '.URL.'?modify='.$_POST['id']);
			}
			$db = NULL;		// close the database connection
			exit;
			break;
		default:
	}
}

// Post Modify Page
if (isset($_GET['modify']) && is_numeric($_GET['modify'])) {
	$sth = $db->prepare('SELECT * FROM post WHERE id = '.$_GET['modify']);
	$sth->execute();
	$sth->bindColumn('id', $id);
	$sth->bindColumn('author', $author);
	$sth->bindColumn('mail', $mail);
	$sth->bindColumn('title', $title);
	$sth->bindColumn('content', $content);
	$sth->bindColumn('homepage', $homepage);
	$sth->bindColumn('ip', $ip);
	$sth->bindColumn('datetime', $datetime);
	$sth->fetch();
	if(empty($id)) {
		hint('No Article No.'.$_GET['modify'].' !');
		header('location: '.URL);
	} else {
		include 'template/'.TEMPLATE.'/header.tpl.php';
		include 'template/'.TEMPLATE.'/modify.tpl.php';
		include 'template/'.TEMPLATE.'/footer.tpl.php';
		unset($_SESSION['hint']);
	}
	// close the database connection
	$db = NULL;
	exit;
}

// Post Reply Page
if (isset($_GET['reply']) && is_numeric($_GET['reply'])) {
	$sth = $db->prepare('SELECT * FROM post WHERE id = '.$_GET['reply']);
	$sth->execute();
	$sth->bindColumn('id', $id);
	$sth->bindColumn('reply', $reply);
	$sth->bindColumn('author', $author);
	$sth->bindColumn('mail', $mail);
	$sth->bindColumn('title', $title);
	$sth->bindColumn('content', $content);
	$sth->bindColumn('homepage', $homepage);
	$sth->bindColumn('ip', $ip);
	$sth->bindColumn('datetime', $datetime);
	$sth->fetch();
	if (empty($id)) {
		hint('No Article No.'.$_GET['reply'].' !');
		header('location: '.URL);
	} else if ($reply) {
		header('location: '.URL.'?reply='.$reply);
	} else {
		$sth = $db->prepare('SELECT * FROM post WHERE reply = '.$_GET['reply']);
		$sth->execute();
		$comment = $sth->fetchAll();
		$reply_author = isset($_COOKIE['AUTHOR']) ? $_COOKIE['AUTHOR'] : '';
		$reply_mail = isset($_COOKIE['MAIL']) ? $_COOKIE['MAIL'] : '';
		$reply_homepage = isset($_COOKIE['HOMEPAGE']) ? $_COOKIE['HOMEPAGE'] : '';
		$reply_title = isset($_SESSION['reply_title']) ? $_SESSION['reply_title'] : 'RE: '.$title;
		$reply_content = isset($_SESSION['reply_content']) ? $_SESSION['reply_content'] : '';
		include 'template/'.TEMPLATE.'/header.tpl.php';
		if (COMMENT) {
			include 'template/'.TEMPLATE.'/reply.tpl.php';
		}
		include 'template/'.TEMPLATE.'/message.tpl.php';
		include 'template/'.TEMPLATE.'/footer.tpl.php';
		unset($_SESSION['hint']);
	}
	// close the database connection
	$db = NULL;
	exit;
}

// Post Edit Page
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
	if (!isset($_SESSION['modify']) || ($_SESSION['modify'] != $_GET['edit'] && !$admin)) {
		hint('No privilege!');
		header('location: '.URL.'?modify='.$_GET['edit']);
	} else {
		$sth = $db->prepare('SELECT * FROM post WHERE id = '.$_GET['edit']);
		$sth->execute();
		$sth->bindColumn('id', $id);
		$sth->bindColumn('author', $author);
		$sth->bindColumn('mail', $mail);
		$sth->bindColumn('title', $title);
		$sth->bindColumn('content', $content);
		$sth->bindColumn('homepage', $homepage);
		$sth->fetch();
		if(empty($id)) {
			hint('No Article No.'.$_GET['edit'].' !');
			header('location: '.URL);
		} else {
			include 'template/'.TEMPLATE.'/header.tpl.php';
			include 'template/'.TEMPLATE.'/edit.tpl.php';
			include 'template/'.TEMPLATE.'/footer.tpl.php';
			unset($_SESSION['hint']);
		}
	}
	// close the database connection
	$db = NULL;
	exit;
}

// Admin Page
if (isset($_GET['admin'])) {
	include 'template/'.TEMPLATE.'/header.tpl.php';
	include 'template/'.TEMPLATE.'/admin.tpl.php';
	include 'template/'.TEMPLATE.'/footer.tpl.php';
	// close the database connection
	$db = NULL;
	exit;
}


// Feed Page
if (isset($_GET['feed'])) {
	header('Content-type: application/xml; charset=utf-8');
	echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
	$sth = $db->prepare('SELECT * FROM post ORDER BY id DESC LIMIT 20');
	$sth->execute();
	$result = $sth->fetchAll();
	include 'template/'.TEMPLATE.'/feed.tpl.php';
	// close the database connection
	$db = NULL;
	exit;
}

// Default Page
$sth = $db->prepare('SELECT COUNT(*) FROM post WHERE reply = 0');
$sth->execute();
$page_items = $sth->fetchColumn();
$total_pages = ceil($page_items/PAGEITEM);
$current_page = 1;
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
	$current_page = (int)$_GET['page'];  
}
if ($current_page > $total_pages) {
	$current_page = $total_pages;  
}
if ($current_page < 1) {
   $current_page = 1;
}
$offset = ($current_page - 1) * PAGEITEM;  

$sort = SORTBYPOST ? 'datetime' : 'change';
$sth = $db->prepare('SELECT * FROM post WHERE reply = 0 ORDER BY '.$sort.' DESC LIMIT '.$offset.', '.PAGEITEM);
$sth->execute();
$result = $sth->fetchAll();

foreach($result as $row) {
	$sth = $db->prepare('SELECT * FROM post WHERE reply = '.$row['id']);
	$sth->execute();
	$comment[$row['id']] = $sth->fetchAll();
}

$author = isset($_COOKIE['AUTHOR']) ? $_COOKIE['AUTHOR'] : '';
$mail = isset($_COOKIE['MAIL']) ? $_COOKIE['MAIL'] : '';
$homepage = isset($_COOKIE['HOMEPAGE']) ? $_COOKIE['HOMEPAGE'] : '';
$title = isset($_SESSION['title']) ? $_SESSION['title'] : '';
$content = isset($_SESSION['content']) ? $_SESSION['content'] : '';

include 'template/'.TEMPLATE.'/header.tpl.php';
include 'template/'.TEMPLATE.'/form.tpl.php';
include 'template/'.TEMPLATE.'/board.tpl.php';
include 'template/'.TEMPLATE.'/footer.tpl.php';
unset($_SESSION['hint']);
// close the database connection
$db = NULL;

function hint($hint) {
	if (!isset($_SESSION['hint']))
		$_SESSION['hint'] = array($hint);
	else
		array_push($_SESSION['hint'], $hint);
}

function url_input($url) {
	if (preg_match('/^http/',$url))
		return $url;
	else
		return 'http://'.$url;
}

function add_link($str) {
	$str = preg_replace('#(http|https|ftp|telnet)://([0-9a-z\.\-]+)(:?[0-9]*)([0-9a-z\_\/\?\&\=\%\.\;\#\-\~\+\(\)\!]*)#i','<a href="\1://\2\3\4" rel="nofollow">\1://\2\3\4</a>', $str);
	return $str;
}

function get_avatar($mail, $size = 80) {
	return ('http://www.gravatar.com/avatar/'.md5($mail).'?s='.$size);
}

function indent_text($string, $indent) {
	if (is_int($indent) && $indent > 0) {
		$string = explode("\n", $string);
		foreach ($string as &$line)
			$line = str_repeat("\t", $indent).$line;
		$string = implode("\n", $string);
	}
	return $string."\n";
}

function page_items($items_page, $current_page, $total_items) {
	if ($total_items > 0) {
		$total_pages = ceil($total_items / $items_page);
		if ($current_page < 0)
			$current_page = $total_pages + $current_page + 1; //-1 means the last page.
		if ($current_page < 1)
			$current_page = 1;
		if ($current_page > $total_pages)
			$current_page = $total_pages;
		$start = $items_page * ($current_page - 1);
		$query['start'] = $start;
		$query['total_pages'] = $total_pages;
		$query['current_page'] = $current_page;
		return $query;
	} else {
		$query['start'] = 0;
		$query['total_pages'] = 0;
		$query['current_page'] = 0;
		return $query;
	}
}
?>
