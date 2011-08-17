	<div id="form">
		<form action="" method="POST">
<?php if(!$admin): ?>
			<p class="author"><label>Name:<input type="text" name="author" required value="<?php echo $reply_author; ?>" /></label></p>
			<p class="mail"><label for="mail">Mail:</label><input type="email" name="mail" value="<?php echo $reply_mail; ?>" /></p>
			<p class="homepage"><label>Hompage:<input type="text" name="homepage" value="<?php echo $reply_homepage; ?>" /></label></p>
<?php endif; ?>
			<p class="title"><label>Title:<input type="text" name="title" value="<?php echo $reply_title; ?>" /></label></p>
			<p class="text">
				<span>Text:</span><textarea name="content" placeholder="Text here…" required><?php echo $reply_content; ?></textarea>
			</p>
<?php if(!$admin): ?>
			<p class="password">
				<label>Pass:<input type="password" name="password" /></label><span>for edit or delete</span>
			</p>
			<p class="verify">
				<label>Code:<input type="text" name="verify" required /></label><img src="<?php echo URL.'?verify'; ?>" alt="CODE">
			</p>
<?php endif; ?>
			<p class="submit">
				<input type="hidden" name="action" value="reply">
				<input type="hidden" name="id" value="<?php echo $id; ?>">
				<input type="submit" value="REPLY" />
			</p>
		</form>
	</div>
