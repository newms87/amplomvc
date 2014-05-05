<div id="guest_checkout">
	<form action="<?= $validate_guest_checkout; ?>" method="post">
		<div class="left general_form">
			<h2><?= _l("Your Personal Details"); ?></h2>

			<div class="checkout_form">
				<?= $form_guest_info; ?>
			</div>
		</div>
		<div class="right payment_address">
			<h2><?= _l("Your Billing Address"); ?></h2>

			<div class="checkout_form">
				<?= $form_payment_address; ?>
			</div>
		</div>
		<? if (!empty($form_shipping_address)) { ?>
			<div style="clear:both">
				<input type="checkbox" name="same_shipping_address" value="1"
					id="shipping" <?= $same_shipping_address ? 'checked="checked"' : ''; ?> />
				<label for="shipping"><?= _l("My delivery and billing addresses are the same."); ?></label>
			</div>
			<div id="guest_shipping_address" class="left shipping_address">
				<h2><?= _l("Your Delivery Address"); ?></h2>

				<div class="checkout_form">
					<?= $form_shipping_address; ?>
				</div>
			</div>
		<? } ?>
		<div id="guest_checkout_submit" class="checkout_form_submit">
			<input type="submit" name="submit_guest_checkout" class="button" value="<?= _l("Continue Guest Checkout"); ?>"/>
		</div>
	</form>
</div>

<script type="text/javascript">
	$('#guest_checkout .shipping_address .zone_select').ac_zoneselect({listen: '#guest_checkout .shipping_address .country_select'});
	$('#guest_checkout .payment_address .zone_select').ac_zoneselect({listen: '#guest_checkout .payment_address .country_select'});

	$('#guest_checkout input[name=same_shipping_address]').change(function () {
		shipping_form = $('#guest_shipping_address');

		if ($(this).is(':checked')) {
			shipping_form.hide();
		}
		else {
			shipping_form.slideDown('fast');
		}
	}).trigger('change');
</script>
