<rss version="2.0">
	<channel>
		<title><?php echo BOARD; ?></title>
		<description><?php echo ABOUT; ?></description>
		<link><?php echo URL; ?></link>
<?php foreach ($result as $list): ?>
		<item>
			<title><?php echo 'No.'.$list['id'].': '.$list['title']; ?></title>
			<link><?php echo URL.'?reply='.$list['id']; ?></link>
			<description>
<?php echo indent_text(htmlspecialchars(add_link(nl2br($list['content'])),ENT_COMPAT,'UTF-8'), 4); ?>
			</description>
			<author><?php echo $list['author']; ?></author>
			<category><?php if ($list['reply']) echo 'Reply'; else echo 'Post'; ?></category>
			<guid><?php echo URL.'post-'.$list['id']; ?></guid>
			<pubDate><?php echo date(DATE_RSS, strtotime($list['datetime'].' UTC')); ?></pubDate>
		</item>
<?php endforeach; ?>
	</channel>
</rss>