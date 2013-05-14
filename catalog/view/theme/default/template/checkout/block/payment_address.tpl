<? if ($data_addresses) { ?>
<input type="radio" name="payment_address" value="existing" id="payment-address-existing" <?= $data_addresses ? 'checked="checked"' : ''; ?> />
<label for="payment-address-existing"><?= $text_address_existing; ?></label>

<div id="payment_existing" <?= $data_addresses ? '' : 'style="display: none;"'; ?>>
	<form action="<?= $validate_selection; ?>" method="post">
		<select name="address_id" onchange="validate_form($(this).closest('form'))" style="width: 100%; margin-bottom: 15px;" size="5">
			<? foreach ($data_addresses as $address) { ?>
				<option value="<?= $address['address_id']; ?>" <?= $address['address_id'] == $address_id ? 'selected="selected"' : '';?>><?= $address['firstname']; ?> <?= $address['lastname']; ?>, <?= $address['address_1']; ?>, <?= $address['city']; ?>, <?= $address['zone']; ?>, <?= $address['country']; ?></option>
			<? } ?>
		</select>
		<noscript>
			<input type="submit" name="payment_existing" value="<?= $button_select; ?>" />
		</noscript>
	</form>
</div>
<? } ?>

<p>
	<input type="radio" name="payment_address" value="new" onchange="toggle_payment_address()" id="payment-address-new" <?= $data_addresses ? '' : 'checked="checked"'; ?> />
	<label for="payment-address-new"><?= $text_address_new; ?></label>
</p>
<div id="payment_new" class="address_form" <?= $data_addresses ? 'style="display: none;"' : ''; ?>>
	<?= $form_payment_address;?>
</div>

<script type="text/javascript">//<!--
function toggle_payment_address(){
	if($('[name=payment_address]:checked').val() == 'existing'){
		$('#payment_new').hide();
		$('#payment_existing').show();
	} else {
		$('#payment_new').show();
		$('#payment_existing').hide();
	}
}
//--></script>