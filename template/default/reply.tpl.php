		<div id="form">
			<form<?php echo empty($hint)? "": " class=\"show\"";?> action="" method="POST">
<?php if(!$admin): ?>
				<p class="author"><input id="author" type="text" name="author" value="<?php echo $reply_author; ?>" /> <label for="author">Name</label></p>
				<p class="mail"><input id="mail" type="text" name="mail" value="<?php echo $reply_mail; ?>" /> <label for="mail">Mail</label></p>
				<p class="homepage"><input id="homepage" type="text" name="homepage" value="<?php echo $reply_homepage; ?>" /> <label for="homepage">Hompage</label></p>
<?php endif; ?>
				<p class="title"><input type="text" id="title" name="title" value="<?php echo $reply_title; ?>" /> <label for="title">Title</label></p>
				<p class="text">
					<textarea name="content" placeholder="Text here…"><?php echo $reply_content; ?></textarea>
				</p>
<?php if(!$admin): ?>
				<p class="password">
					<input type="password" id="password" name="password" /> <label for="password">Pass</label> <span class="description">(for edit or delete)</span>
				</p>
				<p class="verify">
					<input type="text" name="verify" /> <img src="<?php echo URL.'?verify'; ?>" alt="CODE">
				</p>
<?php endif; ?>
				<input type="hidden" name="action" value="reply">
				<input type="hidden" name="id" value="<?php echo $id; ?>">
				<p class="submit">
					<input type="submit" class="maxWidth" jshow="REPLY" value="REPLY" />
				</p>
			</form>
		</div>
