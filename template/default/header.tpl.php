<?php include("template.conf.php");?>
<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8" />
	<link href="template/<?php echo TEMPLATE;?>/style.css" rel="stylesheet" />
	<link href='http://fonts.googleapis.com/css?family=Jura:500|Monofett&v2' rel='stylesheet' type='text/css'>
	<link rel="alternate" type="application/rss+xml" title="RSS" href="<?php echo URL.'?feed'; ?>">
	<title><?php echo BOARD;?> - <?php echo ABOUT;?></title>
	<script>
	var ie = false;
	</script>
	<!--[if IE]>
	<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<script>
	ie = true;
	</script>
	<![endif]-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
	<script src="template/<?php echo TEMPLATE;?>/jquery.easing.js"></script>
	<script src="template/<?php echo TEMPLATE;?>/jquery.js"></script>
</head>
<body>
	<div id="wrapper"<?php echo (defined("TEMPLATE_MENU") && TEMPLATE_MENU)? "": " style=\"width:690px\"";?>>
<?php if(defined("TEMPLATE_MENU") && TEMPLATE_MENU):?>
	<aside id="sidebar">
			<div id="admin_info">
<?php if(MAIL):?>
				<img class="admin_gavatar" src="<?php echo get_avatar(MAIL, 50);?>" />
				<span><?php echo ADMIN;?></span>
<?php endif;?>
			</div>
			<nav id="menu">
				<ul>
<?php foreach($menu as $link_title => $link_url):?>
					<li><a class="tooltip" title="<?php echo $link_title;?>" href="<?php echo $link_url;?>"><?php echo $link_title;?></a>
<?php endforeach;?>
				</ul>
			</nav>
		</aside>
<?php endif;?>
		<header id="header">
			<span class="copyright">kidpen</span>
			<a class="admin" title="登入 login / 登出 logout" href="?<?php echo $admin? "exit": "admin"?>">login / logout</a>
			<a class="feed" title="訂閱本站最新留言 (20筆)" href="?feed">RSS</a>
			<a class="sitename" href="<?php echo URL;?>" title="<?php echo ABOUT;?>"><?php echo BOARD;?></a>
		</header>
<?php if (!empty($hint)): ?>
		<div id="hint">
			<ul>
<?php foreach ($hint as $item): ?>
				<li><?php echo $item?></li>
<?php endforeach; ?>
			</ul>
		</div>
<?php endif; ?>
