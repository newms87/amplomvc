<? if ($testmode) { ?>
	<div class="message_box warning"><?= $text_testmode; ?></div>
<? } ?>
<form action="<?= $action; ?>" method="post" target="_blank">
	<input type="hidden" name="cmd" value="_cart"/>
	<input type="hidden" name="upload" value="1"/>
	<input type="hidden" name="business" value="<?= $business; ?>"/>
	<? $i = 1; ?>
	<? foreach ($products as $cart_product) { ?>
		<? $product = $cart_product['product']; ?>
		<input type="hidden" name="item_name_<?= $i; ?>" value="<?= $product['name']; ?>"/>
		<input type="hidden" name="item_number_<?= $i; ?>" value="<?= $product['model']; ?>"/>
		<input type="hidden" name="amount_<?= $i; ?>" value="<?= $cart_product['price']; ?>"/>
		<input type="hidden" name="quantity_<?= $i; ?>" value="<?= $cart_product['quantity']; ?>"/>
		<input type="hidden" name="weight_<?= $i; ?>" value="<?= $cart_product['weight']; ?>"/>
		<? if (!empty($cart_product['options'])) { ?>
			<? $j = 0; ?>
			<? foreach ($cart_product['options'] as $option_values) { ?>
				<? foreach ($option_values as $product_option_value) { ?>
					<? if ($product_option_value['display_value']) { ?>
						<input type="hidden" name="on<?= $j; ?>_<?= $i; ?>" value="<?= $product_option_value['name']; ?>"/>
						<input type="hidden" name="os<?= $j; ?>_<?= $i; ?>" value="<?= $product_option_value['display_value']; ?>"/>
					<? } else { ?>
						<input type="hidden" name="on<?= $j; ?>_<?= $i; ?>" value="<?= $product_option_value['display_name']; ?>"/>
						<input type="hidden" name="os<?= $j; ?>_<?= $i; ?>" value="<?= $product_option_value['value']; ?>"/>
					<? } ?>
				<? } ?>
				<? $j++; ?>
			<? } ?>
		<? } ?>
		<? $i++; ?>
	<? } ?>

	<? if (!empty($extras)) { ?>
		<? foreach ($extras as $extra) { ?>
			<input type="hidden" name="item_name_<?= $i; ?>" value="<?= $extra['name']; ?>"/>
			<input type="hidden" name="item_number_<?= $i; ?>" value="<?= $extra['model']; ?>"/>
			<input type="hidden" name="amount_<?= $i; ?>" value="<?= $extra['price']; ?>"/>
			<input type="hidden" name="quantity_<?= $i; ?>" value="<?= $extra['quantity']; ?>"/>
			<input type="hidden" name="weight_<?= $i; ?>" value="<?= $extra['weight']; ?>"/>
			<? $i++; ?>
		<? } ?>
	<? } ?>

	<? if ($discount_amount_cart) { ?>
		<input type="hidden" name="discount_amount_cart" value="<?= $discount_amount_cart; ?>"/>
	<? } ?>
	<input type="hidden" name="currency_code" value="<?= $currency_code; ?>"/>
	<input type="hidden" name="first_name" value="<?= $first_name; ?>"/>
	<input type="hidden" name="last_name" value="<?= $last_name; ?>"/>
	<input type="hidden" name="address1" value="<?= $address1; ?>"/>
	<input type="hidden" name="address2" value="<?= $address2; ?>"/>
	<input type="hidden" name="city" value="<?= $city; ?>"/>
	<input type="hidden" name="zip" value="<?= $zip; ?>"/>
	<input type="hidden" name="country" value="<?= $country; ?>"/>
	<input type="hidden" name="address_override" value="0"/>
	<input type="hidden" name="email" value="<?= $email; ?>"/>
	<input type="hidden" name="invoice" value="<?= $invoice; ?>"/>
	<input type="hidden" name="lc" value="<?= $lc; ?>"/>
	<input type="hidden" name="rm" value="2"/>
	<input type="hidden" name="no_note" value="1"/>
	<input type="hidden" name="charset" value="utf-8"/>
	<? if (!empty($return)) { ?>
		<input type="hidden" name="return" value="<?= $return; ?>"/>
	<? } ?>
	<input type="hidden" name="notify_url" value="<?= $notify_url; ?>"/>
	<input type="hidden" name="cancel_return" value="<?= $cancel_return; ?>"/>
	<input type="hidden" name="paymentaction" value="<?= $paymentaction; ?>"/>
	<input type="hidden" name="custom" value="<?= $custom; ?>"/>
	<input type="hidden" name="image_url" value="<?= $image_url; ?>"/>
	<? if ($page_style) { ?>
		<input type="hidden" name="page_style" value="<?= $page_style; ?>"/>
	<? } ?>

	<div class="buttons">
		<div class="right">
			<div id="submit_pp_button">
				<div id="submit_payment"><?= $text_submit_payment; ?></div>
				<input type="submit" value="<?= $button_confirm; ?>" class="button"/></div>
			<div id="processing_payment">
				<img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>"
					alt=""/><span><?= $text_processing_payment; ?></span><br/>
				<input type="submit" value="<?= $button_try_again; ?>" class="button"/>
			</div>
		</div>
	</div>
</form>

<script type="text/javascript">
	$('#submit_pp_button input').click(function () {
		$('#processing_payment').fadeIn(500);
		$('#submit_pp_button').hide();
		check_order_update()
	});

	function check_order_update() {
		$.ajax({
			url: "<?= $url_check_order_status; ?>",
			dataType: 'json',
			success: function (json) {
				if (json['redirect']) {
					window.location = json['redirect'];
				}
			},
			complete: function () {
				setTimeout(check_order_update, 2000);
			}
		});
	}
</script>
