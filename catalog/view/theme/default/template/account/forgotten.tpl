<?= $header; ?>
<? if ($error_warning) { ?>
<div class="message_box warning"><?= $error_warning; ?></div>
<? } ?>
<?= $column_left; ?><?= $column_right; ?>
<div id="content"><?= $content_top; ?>
	<?= $breadcrumbs; ?>
	<h1><?= $heading_title; ?></h1>
	<form action="<?= $action; ?>" method="post" enctype="multipart/form-data">
		<p><?= $text_email; ?></p>
		<h2><?= $text_your_email; ?></h2>
		<div class="content">
			<table class="form">
				<tr>
					<td><?= $entry_email; ?></td>
					<td><input type="text" name="email" value="" /></td>
				</tr>
			</table>
		</div>
		<div class="buttons">
			<div class="left"><a href="<?= $back; ?>" class="button"><?= $button_back; ?></a></div>
			<div class="right">
				<input type="submit" value="<?= $button_continue; ?>" class="button" />
			</div>
		</div>
	</form>
	<?= $content_bottom; ?></div>
<?= $footer; ?>