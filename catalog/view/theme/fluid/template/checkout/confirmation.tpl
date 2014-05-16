<div id="checkout-confirmation-form">
	<? if (option('coupon_status')) { ?>
		<div class="checkout-coupon">
			<?= block('cart/coupon'); ?>
		</div>
	<? } ?>

	<form action="<?= $action; ?>" class="form" method="post">
		<div class="checkout-totals">
			<?= block('cart/total'); ?>
		</div>

		<div class="checkout-payment">
			<? if ($has_confirmation) { ?>
				<?= call('extension/payment/' . $payment_code . '/confirmation'); ?>
			<? } else { ?>
				<button class="checkout-confirm"><?= _l("Confirm Order"); ?></button>
			<? } ?>
		</div>
	</form>
</div>
