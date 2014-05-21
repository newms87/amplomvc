<div class="braintree-form">
	<noscript>
		<div class="message-box error"><?= _l("You must enable javascript to checkout using this payment method!"); ?></div>
	</noscript>

	<div class="braintree-checkout">
		<h1><?= _l("Existing Credit Card"); ?></h1>

		<? $settings = array(
			'payment_code' => 'braintree',
			'new_card'     => 'register_fields',
			'payment_key'  => $payment_key,
		); ?>

		<?= call('extension/payment/braintree/select_card', $settings); ?>
	</div>
</div>
