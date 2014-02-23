<? if (!empty($no_shipping_address)) { ?>
	<h2><?= _l("Please select a delivery address.");; ?></h2>

<? } elseif (!empty($cart_error_shipping_method)) { ?>
	<h2><?= $cart_error_shipping_method; ?></h2>

	<? if (!empty($allowed_shipping_zones)) { ?>
		<br/>
		<h2><?= _l("We deliver to the following locations:"); ?></h2>
		<div class="allowed_zone_list">
			<? foreach ($allowed_shipping_zones as $country_id => $country) { ?>
				<span class="allowed_zone_item"><?= $country['name']; ?><?= empty($c) ? $c = ',' : ''; ?></span>
			<? } ?>
		</div>
	<? } ?>

<? } else { ?>
	<form action="<?= $validate_shipping_method; ?>" method="post">

		<table class="radio">
			<? foreach ($shipping_methods as $shipping_method) { ?>
				<tr>
					<td colspan="3"><b><?= $shipping_method['title']; ?></b></td>
				</tr>
				<? foreach ($shipping_method['quotes'] as $quote) { ?>
					<? $checked = !empty($quote['selected']) ? 'checked="checked"' : ''; ?>
					<? $id = $shipping_method['code'] . ',' . $quote['shipping_key']; ?>
					<tr class="shipping_method checkout_method highlight">
						<td class="method_id">
							<input type="radio" id="<?= $id; ?>" name="shipping_method" value="<?= $id; ?>" <?= $checked; ?> />
						</td>
						<td class="method_title"><label for="<?= $id; ?>"><?= $quote['title']; ?></label></td>
						<td class="method_content"><label for="<?= $id; ?>"><?= $quote['cost_display']; ?></label></td>
					</tr>
				<? } ?>
			<? } ?>
		</table>

	</form>
<? } ?>
