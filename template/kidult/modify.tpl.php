	<div id="message">
		<article>
			<header>
				<span><?php echo $id; ?></span>
<?php if($title): ?>
				<h3><?php echo $title; ?></h3>
<?php endif; ?>
			</header>
			<aside>
				<img src="<?php echo get_avatar($mail); ?>" alt="<?php echo $author; ?>" />
				<span title="IP: <?php echo long2ip($ip); ?>"><?php echo $author; ?></span>
			</aside>
			<section>
<?php echo indent_text(add_link(nl2br($content)), 4); ?>
			</section>
			<footer>
				<time><?php echo date('Y-m-d H:i:s', strtotime($datetime.' UTC')); ?></time>
			</footer>
		</article>
	</div>
	<div id="modify">
		<form action="" method="POST">
			<p>
<?php if(!$admin): ?>
				<label>Pass:<input type="password" name="password" /></label>
<?php endif; ?>
				<label>Action:
					<select name="action">
						<option value="modify">MOD</option>
						<option value="delete">DEL</option>
					</select>
				</label>
				<input type="hidden" name="id" value="<?php echo $id; ?>">
				<input type="hidden" name="type" value="modify">
				<input type="submit" value="Modify" />
			</p>
		</form>
	</div>
