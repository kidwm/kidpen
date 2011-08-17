	<div id="form">
		<form action="" method="POST">
			<p class="author"><label for="author">Name:</label><input type="text" name="author" required value="<?php echo $author; ?>" /></p>
			<p class="mail"><label for="mail">Mail:</label><input type="email" name="mail" value="<?php echo $mail; ?>" /></p>
			<p class="homepage"><label for="homepage">Hompage:</label><input type="text" name="homepage" value="<?php echo $homepage; ?>" /></p>
			<p class="title"><label for="title">Title:</label><input type="text" name="title" value="<?php echo $title; ?>" /></p>
			<p class="text">
				<span>Text:</span><textarea name="content" placeholder="Text here…" required><?php echo $content; ?></textarea>
			</p>
			<p class="submit">
				<input type="hidden" name="id" value="<?php echo $id; ?>">
				<input type="hidden" name="action" value="edit">
				<input type="submit" value="EDIT" />
			</p>
		</form>
	</div>
