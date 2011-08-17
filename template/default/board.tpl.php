		<div id="message">
<?php if (empty($result)): ?>
			<article class="No-Messges">
				<p>No Messages.</p>
			</article>
<?php else: ?>
<?php foreach($result as $row): ?>
			<article id="post-<?php echo $row['id']; ?>"<?php if ($row['author'] == ADMIN) echo ' class="admin"';?>>
				<header>
					<span><a href="?reply=<?php echo $row['id']; ?>" title="Post Link"><?php echo $row['id']; ?></a></span>
<?php if ($row['title']): ?>
					<h3><?php echo $row['title']; ?></h3>
<?php endif; ?>
				</header>
				<aside>
					<img src="<?php echo get_avatar($row['mail']); ?>" alt="<?php echo $row['author']; ?>" />
					<span title="IP: <?php echo long2ip($row['ip']); ?>"><?php if (empty($row['homepage'])) echo $row['author']; else echo '<a href="'.$row['homepage'].'">'.$row['author'].'</a>'; ?></span>
				</aside>
				<section>
<?php echo indent_text(add_link(nl2br($row['content'])), 4); ?>
				</section>
				<footer>
<?php if (COMMENT): ?>
					<span><a href="?reply=<?php echo $row['id']; ?>#open">Reply</a></span> - 
<?php endif; ?>
<?php if ($admin || !empty($row['password'])): ?>
					<span><a href="?modify=<?php echo $row['id']; ?>">Modify</a></span> - 
<?php endif; ?>
					<time><?php echo date('Y-m-d H:i:s', strtotime($row['datetime'].' UTC')); ?></time>
				</footer>
			</article>
<?php foreach($comment[$row['id']] as $item): ?>
			<article class="reply reto-<?php echo $row['id']?><?php if ($item['author'] == ADMIN) echo ' admin';?>">
				<header>
					<span><?php echo $item['id']; ?></span>
<?php if($item['title']): ?>
					<h3><?php echo $item['title']; ?></h3>
<?php endif; ?>
				</header>
				<aside>
					<img src="<?php echo get_avatar($item['mail'], 60); ?>" alt="<?php echo $item['author']; ?>" />
					<span title="IP: <?php echo long2ip($item['ip']); ?>"><?php if (empty($item['homepage'])) echo $item['author']; else echo '<a href="'.$item['homepage'].'">'.$item['author'].'</a>'; ?></span>
				</aside>
				<section>
<?php echo indent_text(add_link(nl2br($item['content'])), 4); ?>
				</section>
				<footer>
<?php if ($admin || !empty($item['password'])): ?>
					<span><a href="?modify=<?php echo $item['id']; ?>">Modify</a></span> - 
<?php endif; ?>
					<time><?php echo date('Y-m-d H:i:s', strtotime($item['datetime'].' UTC')); ?></time>
				</footer>
			</article>
<?php endforeach; ?>
<?php endforeach; ?>
<?php endif; ?>
		</div>
<?php if(!empty($result)): ?>
		<nav id="pagebar">
			<ul>
<?php
$range = 3;
$paging = '';
$indent = "\t\t\t\t";
if ($current_page > 1) {
	$paging .= $indent."<li><a href=\"?page=1\">&lt;&lt;</a></li>\n";
	$prev_page = $current_page - 1;  
	$paging .= $indent."<li><a href=\"?page=$prev_page\">&lt;</a></li>\n"; 
}
else {
	$paging .= $indent."<li class=\"dummy\">&lt;&lt;</li>\n";
	$paging .= $indent."<li class=\"dummy\">&lt;</li>\n"; 
}
for ($x = (($current_page - $range) - 1); $x < (($current_page + $range) + 1); $x++) {  
	if (($x > 0) && ($x <= $total_pages)) { 
		if ($x == $current_page) {
			$paging .= $indent."<li class=\"current\">$x</li>\n";
		} else {
				$paging .= $indent."<li><a href=\"?page=$x\">$x</a></li>\n";
		}
	}
}
if ($current_page != $total_pages) {
	$next_page = $current_page + 1; 
	$paging .= $indent."<li><a href=\"?page=$next_page\">&gt;</a></li>\n";
	$paging .= $indent."<li><a href=\"?page=$total_pages\">&gt;&gt;</a></li>\n";
}
else {
	$paging .= $indent."<li class=\"dummy\">&gt;</li>\n";
	$paging .= $indent."<li class=\"dummy\">&gt;&gt;</li>\n";
}
?>
<?php echo $paging; ?>
			</ul>
		</nav>
<?php endif; ?>
