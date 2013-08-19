<div id='guest_checkout'>
	<form action="<?= $validate_guest_checkout; ?>" method="post">
		<div class="left general_form">
			<h2><?= $text_your_details; ?></h2>

			<div class="checkout_form">
				<?= $form_guest_info; ?>
			</div>
		</div>
		<div class="right payment_address">
			<h2><?= $text_payment_address; ?></h2>

			<div class="checkout_form">
				<?= $form_payment_address; ?>
			</div>
		</div>
		<? if (!empty($form_shipping_address)) { ?>
			<div style="clear:both">
				<input type="checkbox" name="same_shipping_address" value="1"
				       id="shipping" <?= $same_shipping_address ? 'checked="checked"' : ''; ?> />
				<label for="shipping"><?= $entry_shipping; ?></label>
			</div>
			<div id="guest_shipping_address" class="left shipping_address">
				<h2><?= $text_shipping_address; ?></h2>

				<div class="checkout_form">
					<?= $form_shipping_address; ?>
				</div>
			</div>
		<? } ?>
		<div id="guest_checkout_submit" class="checkout_form_submit">
			<input type="submit" name="submit_guest_checkout" class="button" value="<?= $button_guest_checkout; ?>"/>
		</div>
	</form>
</div>

<?= $this->builder->js('load_zones', '#guest_checkout .shipping_address, #guest_checkout .payment_address', '.country_select', '.zone_select'); ?>

<script type="text/javascript">//<!--
	$('#guest_checkout input[name=same_shipping_address]').change(function () {
		shipping_form = $('#guest_shipping_address');

		if ($(this).is(':checked')) {
			shipping_form.hide();
		}
		else {
			shipping_form.slideDown('fast');
		}
	}).trigger('change');
//--></script>