<? if ($testmode) { ?>
	<div class="message_box warning"><?= _l("The PayPal payment method is in sandbox mode. Your account will not be charged."); ?></div>
<? } ?>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
	<!-- Identify your business so that you can collect the payments. -->
	<input type="hidden" name="business" value="<?= $business; ?>">

	<!-- Specify a Subscribe button. -->
	<input type="hidden" name="cmd" value="_xclick-subscriptions">
	<!-- Identify the subscription. -->
	<input type="hidden" name="item_name" value="<?= $subsciption['name']; ?>">
	<input type="hidden" name="item_number" value="<?= $subscription['code']; ?>">

	<!-- Set the terms of the regular subscription. -->
	<input type="hidden" name="currency_code" value="<?= $currency_code; ?>">
	<input type="hidden" name="a3" value="<?= $subscription['amount']; ?>">
	<input type="hidden" name="p3" value="<?= $subscription['time']; ?>">
	<input type="hidden" name="t3" value="<?= $subscription['time_unit']; ?>">

	<!-- Set recurring payments until canceled. -->
	<input type="hidden" name="src" value="<?= $subscription['recurring']; ?>">
	<input type="hidden" name="srt" value="<?= $subscription['cycles']; ?>">

	<!-- Display the payment button. -->
	<div class="submit_pp_button">
		<input type="image" name="submit" border="0"
			src="https://www.paypalobjects.com/en_US/i/btn/btn_subscribe_LG.gif"
			alt="PayPal - The safer, easier way to pay online">
		<img alt="" border="0" width="1" height="1" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif">
	</div>
</form>
<!--
<form action="<?= $action; ?>" method="post" target="_blank">
	<input type="hidden" name="cmd" value="_cart" />
	<input type="hidden" name="upload" value="1" />
	<input type="hidden" name="business" value="<?= $business; ?>" />
	<? $i = 1; ?>
	<? foreach ($products as $product) { ?>
		<input type="hidden" name="item_name_<?= $i; ?>" value="<?= $product['name']; ?>" />
		<input type="hidden" name="item_number_<?= $i; ?>" value="<?= $product['model']; ?>" />
		<input type="hidden" name="amount_<?= $i; ?>" value="<?= $product['price']; ?>" />
		<input type="hidden" name="quantity_<?= $i; ?>" value="<?= $product['quantity']; ?>" />
		<input type="hidden" name="weight_<?= $i; ?>" value="<?= $product['weight']; ?>" />
		<? if (!empty($product['selected_options'])) { ?>
			<? $j = 0; ?>
			<? foreach ($product['selected_options'] as $selected_option) { ?>
				<input type="hidden" name="on<?= $j; ?>_<?= $i; ?>" value="<?= $selected_option['product_option']['name']; ?>" />
				<input type="hidden" name="os<?= $j; ?>_<?= $i; ?>" value="<?= $selected_option['value']; ?>" />
				<? $j++; ?>
			<? } ?>
		<? } ?>
		<? $i++; ?>
	<? } ?>
	<? if ($discount_amount_cart) { ?>
	<input type="hidden" name="discount_amount_cart" value="<?= $discount_amount_cart; ?>" />
	<? } ?>
	<input type="hidden" name="currency_code" value="<?= $currency_code; ?>" />
	<input type="hidden" name="first_name" value="<?= $first_name; ?>" />
	<input type="hidden" name="last_name" value="<?= $last_name; ?>" />
	<input type="hidden" name="address1" value="<?= $address1; ?>" />
	<input type="hidden" name="address2" value="<?= $address2; ?>" />
	<input type="hidden" name="city" value="<?= $city; ?>" />
	<input type="hidden" name="zip" value="<?= $zip; ?>" />
	<input type="hidden" name="country" value="<?= $country; ?>" />
	<input type="hidden" name="address_override" value="0" />
	<input type="hidden" name="email" value="<?= $email; ?>" />
	<input type="hidden" name="invoice" value="<?= $invoice; ?>" />
	<input type="hidden" name="lc" value="<?= $lc; ?>" />
	<input type="hidden" name="rm" value="2" />
	<input type="hidden" name="no_note" value="1" />
	<input type="hidden" name="charset" value="utf-8" />
	<? if (!empty($return)) { ?>
		<input type="hidden" name="return" value="<?= $return; ?>" />
	<? } ?>
	<input type="hidden" name="notify_url" value="<?= $notify_url; ?>" />
	<input type="hidden" name="cancel_return" value="<?= $cancel_return; ?>" />
	<input type="hidden" name="paymentaction" value="<?= $paymentaction; ?>" />
	<input type="hidden" name="custom" value="<?= $custom; ?>" />
	<input type="hidden" name="image_url" value="<?= $image_url; ?>" />
	<? if($page_style){?>
	<input type="hidden" name="page_style" value="<?= $page_style; ?>" />
	<? }?>

	<div class="buttons">
		<div class="right">
			<div id="submit_pp_button"><div id="submit_payment"><?= _l("Submit Payment"); ?></div><input type="submit" value="<?= _l("Confirm"); ?>" class="button" /></div>
			<div id="processing_payment">
				<img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>" alt="" /><span><?= _l("Processing Payment"); ?></span><br />
				<input type="submit" value="<?= _l("Try Again"); ?>" class="button" />
			</div>
		</div>
	</div>
</form>
-->

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