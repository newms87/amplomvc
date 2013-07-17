<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
<div id="content"><?= $content_top; ?>
	<?= $this->breadcrumb->render(); ?>
	<h1><?= $heading_title; ?></h1>
	<h2><?= $text_address_book; ?></h2>
	<? foreach ($addresses as $result) { ?>
	<div class="content">
		<table style="width: 100%;">
			<tr>
				<td><?= $result['address']; ?></td>
				<td style="text-align: right;"><a href="<?= $result['update']; ?>" class="button"><?= $button_edit; ?></a> &nbsp; <a href="<?= $result['delete']; ?>" class="button"><?= $button_delete; ?></a></td>
			</tr>
		</table>
	</div>
	<? } ?>
	<div class="buttons">
		<div class="left"><a href="<?= $back; ?>" class="button"><?= $button_back; ?></a></div>
		<div class="right"><a href="<?= $insert; ?>" class="button"><?= $button_new_address; ?></a></div>
	</div>
	<?= $content_bottom; ?></div>
<?= $footer; ?>