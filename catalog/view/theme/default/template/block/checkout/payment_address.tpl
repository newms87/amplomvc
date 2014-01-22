<? if ($data_addresses) { ?>
	<input type="radio" name="payment_address" value="existing"
		id="payment-address-existing" <?= $data_addresses ? 'checked="checked"' : ''; ?> />
	<label for="payment-address-existing"><?= _l("Use an existing billing address:"); ?></label>

	<div id="payment_existing" <?= $data_addresses ? '' : 'style="display: none;"'; ?>>
		<form action="<?= $validate_selection; ?>" method="post">
			<select name="address_id" onchange="ci_validate_form($(this).closest('form'))"
				style="width: 100%; margin-bottom: 15px;" size="5">
				<? foreach ($data_addresses as $address) { ?>
					<option value="<?= $address['address_id']; ?>" <?= $address['address_id'] == $payment_address_id ? 'selected="selected"' : ''; ?>><?= $address['firstname']; ?> <?= $address['lastname']; ?>
						, <?= $address['address_1']; ?>, <?= $address['city']; ?>, <?= $address['zone']['name']; ?>
						, <?= $address['country']['name']; ?></option>
				<? } ?>
			</select>
			<noscript>
				<input type="submit" name="payment_existing" value="<?= _l("Select"); ?>"/>
			</noscript>
		</form>
	</div>
<? } ?>

<p>
	<input type="radio" name="payment_address" value="new"
		id="payment-address-new" <?= $data_addresses ? '' : 'checked="checked"'; ?> />
	<label for="payment-address-new"><?= _l("Use a new address"); ?></label>
</p>
<div id="payment_new" class="address_form" <?= $data_addresses ? 'style="display: none;"' : ''; ?>>
	<?= $form_payment_address; ?>
</div>

<script type="text/javascript">
	$('[name=payment_address]').change(function () {
		if ($('[name=payment_address]:checked').val() == 'existing') {
			$('#payment_new').hide();
			$('#payment_existing').show();

			if (typeof ci_validate_form == 'function') {
				select = $('#payment_existing [name=address_id]');
				if (select.children().length == 1) {
					select.val(select.find('option:first').val()).change();
				}
			}
		} else {
			$('#payment_new').show();
			$('#payment_existing').hide();
		}
	}).trigger('change');
</script>
