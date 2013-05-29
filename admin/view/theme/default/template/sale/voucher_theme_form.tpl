<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs(); ?>
	<? if ($error_warning) { ?>
	<div class="message_box warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'payment.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td><span class="required"></span> <?= $entry_name; ?></td>
						<td><? foreach ($languages as $language) { ?>
							<input type="text" name="voucher_theme_description[<?= $language['language_id']; ?>][name]" value="<?= isset($voucher_theme_description[$language['language_id']]) ? $voucher_theme_description[$language['language_id']]['name'] : ''; ?>" />
							<img src="<?= HTTP_THEME_IMAGE . 'flags/<?= $language['image']; ?>'; ?>" title="<?= $language['name']; ?>" /><br />
							<? if (isset($error_name[$language['language_id']])) { ?>
							<span class="error"><?= $error_name[$language['language_id']]; ?></span><br />
							<? } ?>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_image; ?></td>
						<td valign="top"><div class="image"><img src="<?= $thumb; ?>" alt="" id="thumb" />
							<input type="hidden" name="image" value="<?= $image; ?>" id="image" />
							<br /><a onclick="upload_image('image','thumb');"><?= $text_browse; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#thumb').attr('src', '<?= $no_image; ?>'); $('#image').attr('value', '');"><?= $text_clear; ?></a></div>
							<? if ($error_image) { ?>
							<span class="error"><?= $error_image; ?></span>
							<? } ?></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>

<?= $footer; ?>