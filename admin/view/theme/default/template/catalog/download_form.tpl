<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs(); ?>
	<? if ($error_warning) { ?>
	<div class="message_box warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'download.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= $entry_name; ?></td>
						<td><? foreach ($languages as $language) { ?>
							<input type="text" name="download_description[<?= $language['language_id']; ?>][name]" value="<?= isset($download_description[$language['language_id']]) ? $download_description[$language['language_id']]['name'] : ''; ?>" />
							<img src="<?= HTTP_THEME_IMAGE . 'flags/<?= $language['image']; ?>'; ?>" title="<?= $language['name']; ?>" /><br />
							<? if (isset($error_name[$language['language_id']])) { ?>
							<span class="error"><?= $error_name[$language['language_id']]; ?></span><br />
							<? } ?>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_filename; ?></td>
						<td><input type="file" name="download" value="" />
							<br/>
							<span class="help"><?= $filename; ?></span>
							<? if ($error_download) { ?>
							<span class="error"><?= $error_download; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_remaining; ?></td>
						<td><input type="text" name="remaining" value="<?= $remaining; ?>" size="6" /></td>
					</tr>
					<? if ($show_update) { ?>
					<tr>
						<td><?= $entry_update; ?></td>
						<td><? if ($update) { ?>
							<input type="checkbox" name="update" value="1" checked="checked" />
							<? } else { ?>
							<input type="checkbox" name="update" value="1" />
							<? } ?></td>
					</tr>
					<? } ?>
				</table>
			</form>
		</div>
	</div>
</div>
<?= $footer; ?>