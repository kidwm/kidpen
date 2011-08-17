<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8" />
	<link href="template/<?php echo TEMPLATE; ?>/style.css" rel="stylesheet" />
	<link rel="alternate" type="application/rss+xml" title="RSS" href="<?php echo URL.'?feed'; ?>">
	<title><?php echo BOARD; ?></title>
</head>
<body>
	<header>
		<a href="<?php echo URL; ?>" title="top page"><?php echo BOARD; ?></a>
	</header>
<?php if($admin): ?>
	<div id="admin"><a href="?exit">Logout</a></div>
<?php else: ?>
	<div id="admin"><a href="?admin">Login</a></div>
<?php endif; ?>
	<div id="feed"><a href="?feed">RSS</a></div>
<?php if (!empty($hint)): ?>
	<div id="hint">
		<ul>
<?php foreach ($hint as $item): ?>
			<li><?php echo $item; ?></li>
<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>
