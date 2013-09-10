<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $content_top; ?>

	<h1><?= $head_title; ?></h1>

	<form action="<?= $save; ?>" method="post" enctype="multipart/form-data">
		<div class="section left">
			<table class="form">
				<tr><td colspan="2"><h2><?= $section_info; ?></h2></td></tr>
				<tr>
					<td class="required"> <?= $entry_firstname; ?></td>
					<td><input type="text" name="firstname" value="<?= $firstname; ?>"/></td>
				</tr>
				<tr>
					<td class="required"> <?= $entry_lastname; ?></td>
					<td><input type="text" name="lastname" value="<?= $lastname; ?>"/></td>
				</tr>
				<tr>
					<td class="required"> <?= $entry_email; ?></td>
					<td><input type="text" name="email" value="<?= $email; ?>"/></td>
				</tr>
				<tr>
					<td class="required"> <?= $entry_birthdate; ?></td>
					<td><input type="text" class="datepicker" name="metadata[birthdate]" value="<?= !empty($metadata['birthdate']) ? $metadata['birthdate'] : ''; ?>"/></td>
				</tr>
				<tr><td colspan="2"><h2><?= $section_password; ?></h2></td></tr>
				<tr>
					<td class="required"> <?= $entry_password; ?></td>
					<td>
						<input type="password" autocomplete='off' name="password" value=""/>
						<span class="help"><?= $text_password_help; ?></span>
					</td>
				</tr>
				<tr>
					<td class="required"> <?= $entry_confirm; ?></td>
					<td><input type="password" autocomplete='off' name="confirm" value=""/></td>
				</tr>
				<tr><td><h2><?= $section_newsletter; ?></h2></td></tr>
				<tr>
					<td><?= $entry_newsletter; ?></td>
					<td><input type="checkbox" class="ac_checkbox" name="newsletter" value="1" <?= $newsletter ? 'checked="checked"' :''; ?> /></td>
				</tr>
			</table>
		</div>

		<div class="section right">
			<h2><?= $text_ship_to; ?></h2>
			<div id="address_list" class="noselect">
				<? foreach ($data_addresses as $address) { ?>
					<? $checked = ($address['address_id'] == $metadata['default_shipping_address_id']) ? 'checked="checked"' : ''; ?>
					<div class="address <?= $checked ? 'checked' : ''; ?>">
						<input id="shipaddress<?= $address['address_id']; ?>" type="radio" name="metadata[default_shipping_address_id]" value="<?= $address['address_id']; ?>" <?= $checked; ?> />
						<label for="shipaddress<?= $address['address_id']; ?>"><?= $address['display']; ?></label>
					</div>
				<? } ?>
			</div>
			<a href="<?= $add_address; ?>" class="add_address" onclick="return colorbox($(this).attr('href', '<?= $ajax_add_address; ?>'));"><?= $button_add_address; ?></a>
		</div>

		<div class="clear buttons">
			<div class="left"><a href="<?= $back; ?>" class="button"><?= $button_back; ?></a></div>
			<div class="right">
				<input type="submit" value="<?= $button_save; ?>" class="button"/>
			</div>
		</div>
	</form>

	<?= $content_bottom; ?>
</div>

<script type="text/javascript">//<!--
$('#address_list .address').ac_radio();

$.ac_datepicker({changeYear: true, yearRange: "c-150:c", changeMonth: true});
//--></script>
<?= $footer; ?>