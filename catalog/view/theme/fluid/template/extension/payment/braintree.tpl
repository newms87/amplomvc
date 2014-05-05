<div class="braintree-form">

	<noscript>
		<div class="message-box error"><?= _l("You must enable javascript to checkout using this payment method!"); ?></div>
	</noscript>

	<form action="<?= $confirm; ?>" method="post" id="braintree-payment-form">

		<div class="braintree-checkout left">
			<h1><?= _l("Existing Credit Card"); ?></h1>

			<input type="radio" name="existing_payment_card" value="1" <?= !empty($card_select) ? 'checked="checked"' : ''; ?> />

			<?= _block('account/select_card', null, array('payment_code' => 'braintree')); ?>
		</div>

		<div class="braintree-checkout right">
			<h1><?= _l("New Credit Card"); ?></h1>

			<input type="radio" name="existing_payment_card" value="0" <?= empty($card_select) ? 'checked="checked"' : ''; ?> />

			<p>
				<label><?= _l("Credit Card Number"); ?></label>
				<input type="text" size="20" autocomplete="off" data-encrypted-name="number"/>
			</p>

			<p>
				<label><?= _l("CVV"); ?></label>
				<input type="text" size="4" autocomplete="off" data-encrypted-name="cvv"/>
			</p>

			<p>
				<label><?= _l("Expiration (MM/YYYY)"); ?></label>
				<input type="text" class="center" size="2" maxlength="2" data-encrypted-name="month"/> / <input type="text" class="center" size="4" maxlength="4" data-encrypted-name="year"/>
			</p>
			<? if ($user_logged) { ?>
				<p>
					<input id="save-to-account" type="checkbox" name="save_to_account" value="1"/>
					<label for="save_to_account"><?= _l("Save this Credit Card to your account?"); ?></label>
				</p>
			<? } ?>
		</div>

		<div class="clear center buttons">
			<input type="submit" id="submit" value="<?= _l("Confirm Payment"); ?>" class="button subscribe"/>
		</div>

	</form>
</div>

<script src="https://js.braintreegateway.com/v1/braintree.js"></script>
<script>
	var count = 0;
	function init_braintree() {
		if (typeof Braintree === 'undefined') {
			if (count++ > 3) {
				$('#braintree_payment_form').ac_msg('warning', "<?= _l("There was a problem loading the Braintree Credit Card Payment Method. Please choose a different method."); ?>", true);
				setTimeout(function () {
					location.reload()
				}, 5000);
			} else {
				setTimeout(init_braintree, 500);
			}
		} else {
			var braintree = Braintree.create("<?= $encryption_key; ?>");
			braintree.onSubmitEncryptForm('braintree_payment_form');
		}
	}
	init_braintree();

	<? if (!empty($card_select)) { ?>
	$('.braintree_checkout').click(select_card_method);
	$('[name=existing_payment_card]').hide();

	function select_card_method() {
		$('.braintree_checkout').removeClass('active');
		$(this).addClass('active').find('[name=existing_payment_card]').prop('checked', true);
	}
	<? } ?>
</script>
