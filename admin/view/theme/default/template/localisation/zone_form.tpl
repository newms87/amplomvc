<?= $header; ?>
<div class="content">
	<?= $breadcrumbs; ?>
	<? if ($error_warning) { ?>
	<div class="message_box warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'country.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= $entry_name; ?></td>
						<td><input type="text" name="name" value="<?= $name; ?>" />
							<? if ($error_name) { ?>
							<span class="error"><?= $error_name; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_code; ?></td>
						<td><input type="text" name="code" value="<?= $code; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_country; ?></td>
						<td><select name="country_id">
								<? foreach ($countries as $country) { ?>
								<? if ($country['country_id'] == $country_id) { ?>
								<option value="<?= $country['country_id']; ?>" selected="selected"><?= $country['name']; ?></option>
								<? } else { ?>
								<option value="<?= $country['country_id']; ?>"><?= $country['name']; ?></option>
								<? } ?>
								<? } ?>
							</select></td>
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