<?= $header; ?>
<div class="content">
<?= $breadcrumbs; ?>
<? if ($error_warning) { ?>
<div class="message_box warning"><?= $error_warning; ?></div>
<? } ?>
<div class="box">
	<div class="heading">
		<h1><img src="<?= HTTP_THEME_IMAGE . 'module.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
		<div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?= $button_save; ?></span></a><a href="<?= $cancel; ?>" class="button"><span><?= $button_cancel; ?></span></a></div>
	</div>
	<div class="content">
		<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
			<table class="form">
				<tr>
					<td class="required"> <?= $entry_public_key; ?></td>
					<td><input name="recaptcha_public_key" value="<?= $public_key; ?>" size="56"><br />
						<? if ($error_public_key) { ?>
						<span class="error"><?= $error_public_key; ?></span>
						<? } ?></td>
				</tr>
				<tr>
					<td class="required"> <?= $entry_private_key; ?></td>
					<td><input name="recaptcha_private_key" value="<?= $private_key; ?>" size="56"><br />
						<? if ($error_private_key) { ?>
						<span class="error"><?= $error_private_key; ?></span>
						<? } ?></td>
				</tr>
			</table>
		</form>
	<div class="help"><?= $text_help; ?></div>
	</div>
</div>
<?= $footer; ?>