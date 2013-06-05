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
						<td class="required"> <?= $entry_title; ?></td>
						<td><input type="text" name="title" value="<?= $title; ?>" />
							<? if ($error_title) { ?>
							<span class="error"><?= $error_title; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td class="required"> <?= $entry_code; ?></td>
						<td><input type="text" name="code" value="<?= $code; ?>" />
							<? if ($error_code) { ?>
							<span class="error"><?= $error_code; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_symbol_left; ?></td>
						<td><input type="text" name="symbol_left" value="<?= $symbol_left; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_symbol_right; ?></td>
						<td><input type="text" name="symbol_right" value="<?= $symbol_right; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_decimal_place; ?></td>
						<td><input type="text" name="decimal_place" value="<?= $decimal_place; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_value; ?></td>
						<td><input type="text" name="value" value="<?= $value; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_status; ?></td>
						<td><select name="status">
								<? if ($status) { ?>
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