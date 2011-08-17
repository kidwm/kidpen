		<div class="show" id="form">
			<form<?php echo empty($hint)? "": " class=\"show\"";?> action="" method="POST">
				<p class="author"><input type="text" id="author" name="author" required value="<?php echo $author; ?>" /> <label for="author">Name</label></p>
				<p class="mail"><input type="text" id="mail" name="mail" value="<?php echo $mail; ?>" /> <label for="mail">Mail</label></p>
				<p class="homepage"><input type="text" id="homepage" name="homepage" value="<?php echo $homepage; ?>" /> <label for="homepage">Hompage</label></p>
				<p class="title"><input type="text" id="title" name="title" value="<?php echo $title; ?>" /> <label for="title">Title</label></p>
				<p class="text">
					<textarea name="content" placeholder="Text here…" required><?php echo $content; ?></textarea>
				</p>
				<input type="hidden" name="id" value="<?php echo $id; ?>">
				<input type="hidden" name="action" value="edit">
				<p class="submit">
					<input type="submit" class="maxWidth" jshow="EDIT" value="EDIT" />
				</p>
			</form>
		</div>
