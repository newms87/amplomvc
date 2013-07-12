<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<? if ($error_warning) { ?>
	<div class="message_box warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'payment.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= $entry_merchant_id; ?></td>
						<td><input type="text" name="google_checkout_merchant_id" value="<?= $google_checkout_merchant_id; ?>" />
							<? if ($error_merchant_id) { ?>
							<span class="error"><?= $error_merchant_id; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td class="required"> <?= $entry_merchant_key; ?></td>
						<td><input type="text" name="google_checkout_merchant_key" value="<?= $google_checkout_merchant_key; ?>" />
							<? if ($error_merchant_key) { ?>
							<span class="error"><?= $error_merchant_key; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_test; ?></td>
						<td><? if ($google_checkout_test) { ?>
							<input type="radio" name="google_checkout_test" value="1" checked="checked" />
							<?= $text_yes; ?>
							<input type="radio" name="google_checkout_test" value="0" />
							<?= $text_no; ?>
							<? } else { ?>
							<input type="radio" name="google_checkout_test" value="1" />
							<?= $text_yes; ?>
							<input type="radio" name="google_checkout_test" value="0" checked="checked" />
							<?= $text_no; ?>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_status; ?></td>
						<td><select name="google_checkout_status">
								<? if ($google_checkout_status) { ?>
								<option value="1" selected="selected"><?= $text_enabled; ?></option>
								<option value="0"><?= $text_disabled; ?></option>
								<? } else { ?>
								<option value="1"><?= $text_enabled; ?></option>
								<option value="0" selected="selected"><?= $text_disabled; ?></option>
								<? } ?>
							</select></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
<?= $footer; ?> 