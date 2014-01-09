<div id="confirm_address_block">
	<? if (!empty($shipping_address)) { ?>
		<div class="address_view">
			<h2><?= _l("Delivery Address:"); ?></h2>

			<div class="address_item shipping">
				<?= $shipping_address; ?>
			</div>
			<a onclick="load_checkout_item($('#customer_information'))"><?= _l("Change"); ?></a>
		</div>
	<? } ?>

	<div class="address_view">
		<h2><?= _l("Billing Address:"); ?></h2>

		<div class="address_item payment">
			<?= $payment_address; ?>
		</div>
		<a onclick="load_checkout_item($('#customer_information'))"><?= _l("Change"); ?></a>
	</div>
</div>