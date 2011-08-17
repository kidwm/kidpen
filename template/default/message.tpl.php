		<div id="message">
			<article<?php if ($author == ADMIN) echo ' class="admin"';?>>
				<header>
					<span><?php echo $id; ?></span>
<?php if($title): ?>
					<h3><?php echo $title; ?></h3>
<?php endif; ?>
				</header>
				<aside>
					<img src="<?php echo get_avatar($mail); ?>" alt="<?php echo $author; ?>" />
					<span title="IP: <?php echo long2ip($ip); ?>"><?php if (empty($homepage)) echo $author; else echo '<a href="'.$homepage.'">'.$author.'</a>'; ?></span>
				</aside>
				<section>
<?php echo indent_text(add_link(nl2br($content)), 4); ?>
				</section>
				<footer>
<?php if ($admin || !empty($password)): ?>
					<span><a href="?modify=<?php echo $id; ?>">Modify</a></span>
<?php endif; ?>
					<time><?php echo date('Y-m-d H:i:s', strtotime($datetime.' UTC')); ?></time>
				</footer>
			</article>
<?php foreach($comment as $item): ?>
			<article class="reply<?php if ($item['author'] == ADMIN) echo ' admin';?>">
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
					<span><a href="?modify=<?php echo $item['id']; ?>">Modify</a></span>
<?php endif; ?>
					<time><?php echo date('Y-m-d H:i:s', strtotime($item['datetime'].' UTC')); ?></time>
				</footer>
			</article>
<?php endforeach; ?>
		</div>
