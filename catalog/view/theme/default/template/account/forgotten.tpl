<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
	<div id="content">
		<?= $this->breadcrumb->render(); ?>
		<?= $content_top; ?>

		<h1><?= $head_title; ?></h1>

		<form action="<?= $action; ?>" method="post" enctype="multipart/form-data">
			<p><?= $text_email; ?></p>

			<h2><?= $text_your_email; ?></h2>

			<div class="section">
				<table class="form">
					<tr>
						<td><?= $entry_email; ?></td>
						<td><input type="text" name="email" value=""/></td>
					</tr>
				</table>
			</div>
			<div class="buttons">
				<div class="left"><a href="<?= $back; ?>" class="button"><?= $button_back; ?></a></div>
				<div class="right">
					<input type="submit" value="<?= $button_continue; ?>" class="button"/>
				</div>
			</div>
		</form>

		<?= $content_bottom; ?>
	</div>

<?= $footer; ?>