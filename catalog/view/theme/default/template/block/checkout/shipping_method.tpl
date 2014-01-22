<? if (!empty($no_shipping_address)) { ?>
	<h2><?= _l("Please select a delivery address.");; ?></h2>

<? } elseif (!empty($cart_error_shipping_method)) { ?>
	<h2><?= $cart_error_shipping_method; ?></h2>

	<? if (!empty($allowed_shipping_zones)) { ?>
		<br/>
		<h2><?= _l("We deliver to the following locations:"); ?></h2>
		<div class="allowed_zone_list">
			<? foreach ($allowed_shipping_zones as $i => $geo_zone) { ?>
				<span
					class="allowed_zone_item"><?= $geo_zone['country']['name'] . (($i == count($allowed_shipping_zones) - 1) ? '' : ', '); ?></span>
			<? } ?>
		</div>
	<? } ?>

<? } else { ?>
	<form action="<?= $validate_shipping_method; ?>" method="post">

		<table class="radio">
			<? foreach ($shipping_methods as $id => $shipping_method) { ?>
				<tr>
					<td colspan="3"><b><?= $shipping_method['code_title']; ?></b></td>
				</tr>
				<tr class="shipping_method checkout_method highlight">
					<td class="method_id">
						<input type="radio" name="shipping_method" value="<?= $id; ?>"
							id="<?= $id; ?>" <?= $id == $shipping_method_id ? 'checked="checked"' : ''; ?> />
					</td>
					<td class="method_title"><label for="<?= $id; ?>"><?= $shipping_method['title']; ?></label></td>
					<td class="method_content"><label for="<?= $id; ?>"><?= $shipping_method['text']; ?></label></td>
				</tr>
			<? } ?>
		</table>

	</form>
<? } ?>
