<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $content_top; ?>

	<h1><?= _l("My Account Information"); ?></h1>

	<form action="<?= $save; ?>" method="post" enctype="multipart/form-data">
		<div class="section left">
			<table class="form">
				<tr>
					<td colspan="2"><h2><?= _l("Your Personal Details"); ?></h2></td>
				</tr>
				<tr>
					<td class="required"> <?= _l("First Name:"); ?></td>
					<td><input type="text" name="firstname" value="<?= $firstname; ?>"/></td>
				</tr>
				<tr>
					<td class="required"> <?= _l("Last Name:"); ?></td>
					<td><input type="text" name="lastname" value="<?= $lastname; ?>"/></td>
				</tr>
				<tr>
					<td class="required"> <?= _l("E-Mail:"); ?></td>
					<td><input type="text" name="email" value="<?= $email; ?>"/></td>
				</tr>
				<tr>
					<td class="required"> <?= _l("Birthdate:"); ?></td>
					<td><input type="text" class="datepicker" name="metadata[birthdate]" value="<?= !empty($metadata['birthdate']) ? $metadata['birthdate'] : ''; ?>"/></td>
				</tr>
				<tr>
					<td colspan="2"><h2><?= _l("Change Password"); ?></h2></td>
				</tr>
				<tr>
					<td class="required"> <?= _l("Password:"); ?></td>
					<td>
						<input type="password" autocomplete="off" name="password" value=""/>
						<span class="help"><?= _l("Leave blank to keep the same."); ?></span>
					</td>
				</tr>
				<tr>
					<td class="required"> <?= _l("Confirm Password:"); ?></td>
					<td><input type="password" autocomplete="off" name="confirm" value=""/></td>
				</tr>
				<tr>
					<td><h2><?= _l("Newsletter:"); ?></h2></td>
				</tr>
				<tr>
					<td><?= _l("Join our mailing list?"); ?></td>
					<td><input type="checkbox" class="ac_checkbox" name="newsletter" value="1" <?= $newsletter ? 'checked="checked"' : ''; ?> /></td>
				</tr>
			</table>
		</div>

		<div class="section right">
			<div class="shipping_address">
				<h2><?= _l("Choose Your Default Shipping Address:"); ?></h2>
				<? if (empty($data_addresses)) { ?>
					<h3><?= _l("You do not have an address registered with us."); ?></h3>
					<a href="<?= $add_address; ?>" class="button" onclick="return colorbox($(this));"><?= _l("Register New Address"); ?></a>
				<? } else { ?>
					<div class="address_list_box">
						<div class="address_list noselect">
							<? foreach ($data_addresses as $address) { ?>
								<div class="address <?= $address['default_shipping'] ? 'checked' : ''; ?>">
									<input id="shipaddress<?= $address['address_id']; ?>" type="radio" name="metadata[default_shipping_address_id]" value="<?= $address['address_id']; ?>" <?= $address['default_shipping'] ? 'checked="checked"' : ''; ?> />
									<label for="shipaddress<?= $address['address_id']; ?>"><?= $address['display']; ?></label>
									<a href="<?= $address['remove']; ?>" class="remove"></a>
								</div>
							<? } ?>
							<a href="<?= $add_address; ?>" class="address add_slide noradio" onclick="return colorbox($(this));"><?= _l("Add New Address"); ?></a>
						</div>
					</div>
				<? } ?>
			</div>

			<div class="credit_card">
				<h2><?= _l("Default Credit Card:"); ?></h2>

				<div class="credit_card_list">
					<?= $card_select; ?>
				</div>
			</div>
		</div>

		<div class="clear buttons">
			<div class="left"><a href="<?= $back; ?>" class="button"><?= _l("Back"); ?></a></div>
			<div class="right">
				<input type="submit" value="<?= _l("Save"); ?>" class="button"/>
			</div>
		</div>
	</form>

	<?= $content_bottom; ?>
</div>

<script type="text/javascript">
	var addresses = $('.address_list').ac_radio();

	if (addresses.children().length > 2) {
		addresses.ac_slidelist({pad_y: -15, x_dir: -1});
	}

	$('.address_list .remove').click(function () {
		var address = $(this);
		$.get(address.attr('href'), {}, function (json) {
			if (json['error']) {
				show_msgs(json['error'], 'error');
			} else {
				location.reload();
			}
		});
		return false;
	});

	$.ac_datepicker({changeYear: true, yearRange: "c-150:c", changeMonth: true});
</script>
<?= $footer; ?>
