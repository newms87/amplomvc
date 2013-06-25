<?= $header; ?><?= $column_left; ?><?= $column_right; ?>
<div id="content"><?= $content_top; ?>
	<?= $breadcrumbs; ?>
	<h1><?= $heading_title; ?></h1>
	<form action="<?= $action; ?>" method="post" enctype="multipart/form-data">
		<div class="content">
			<table class="form">
				<tr>
					<td><?= $entry_newsletter; ?></td>
					<td><? if ($newsletter) { ?>
						<input type="radio" name="newsletter" value="1" checked="checked" />
						<?= $text_yes; ?>&nbsp;
						<input type="radio" name="newsletter" value="0" />
						<?= $text_no; ?>
						<? } else { ?>
						<input type="radio" name="newsletter" value="1" />
						<?= $text_yes; ?>&nbsp;
						<input type="radio" name="newsletter" value="0" checked="checked" />
						<?= $text_no; ?>
						<? } ?></td>
				</tr>
			</table>
		</div>
		<div class="buttons">
			<div class="left"><a href="<?= $back; ?>" class="button"><?= $button_back; ?></a></div>
			<div class="right"><input type="submit" value="<?= $button_continue; ?>" class="button" /></div>
		</div>
	</form>
	<?= $content_bottom; ?></div>
<?= $footer; ?>