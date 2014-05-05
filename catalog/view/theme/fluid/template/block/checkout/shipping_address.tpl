<? if (!empty($allowed_geo_zones)) { ?>
	<h2><?= _l("We deliver to the following locations:"); ?></h2>
	<div class="allowed_zone_list">
		<? foreach ($allowed_geo_zones as $country_id => $country) { ?>
			<span class="allowed_zone_item"><?= $country['name']; ?><?= empty($c) ? $c = ',' : ''; ?></span>
		<? } ?>
	</div>
<? } ?>

<? if ($data_addresses) { ?>
	<input type="radio" name="shipping_address" value="existing"
		id="shipping-address-existing" <?= $data_addresses ? 'checked="checked"' : ''; ?> />
	<label for="shipping-address-existing"><?= _l("Use an existing delivery address:"); ?></label>

	<div id="shipping_existing" <?= $data_addresses ? '' : 'style="display: none;"'; ?>>
		<form action="<?= $validate_selection; ?>" method="post">
			<select name="address_id" onchange="ci_validate_form($(this).closest('form'))"
				style="width: 100%; margin-bottom: 15px;" size="5">
				<? foreach ($data_addresses as $address) { ?>
					<option value="<?= $address['address_id']; ?>" <?= $address['address_id'] == $shipping_address_id ? 'selected="selected"' : ''; ?>><?= $address['firstname']; ?> <?= $address['lastname']; ?>
						, <?= $address['address_1']; ?>, <?= $address['city']; ?>, <?= $address['zone']['name']; ?>
						, <?= $address['country']['name']; ?></option>
				<? } ?>
			</select>
			<noscript>
				<input type="submit" name="shipping_existing" value="<?= _l("Select"); ?>"/>
			</noscript>
		</form>
	</div>
<? } ?>

<p>
	<input type="radio" name="shipping_address" value="new" id="shipping-address-new" <?= $data_addresses ? '' : 'checked="checked"'; ?> />
	<label for="shipping-address-new"><?= _l("Use a new address:"); ?></label>
</p>
<div id="shipping_new" class="address_form" <?= $data_addresses ? 'style="display: none;"' : ''; ?>>
	<?= $form_shipping_address; ?>
</div>


<script type="text/javascript">
	$('[name=shipping_address]').change(function () {
		if ($('[name=shipping_address]:checked').val() == 'existing') {
			$('#shipping_new').hide();
			$('#shipping_existing').show();

			if (typeof ci_validate_form == 'function') {
				select = $('#shipping_existing [name=address_id]');
				if (select.children().length == 1) {
					select.val(select.find('option:first').val()).change();
				}
			}
		} else {
			$('#shipping_new').show();
			$('#shipping_existing').hide();
		}
	}).trigger('change');
</script>