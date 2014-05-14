<section class="braintree-register-card content">
	<header class="row top-row">
		<div class="wrap">
			<h1><?= _l("Register a New Card"); ?></h1>
		</div>
	</header>

	<div class="braintree-form-row form row">
		<div class="wrap">
			<div class="form-item card-number">
				<input type="text" placeholder="<?= _l("Card Number"); ?>" data-encrypted-name="number"/>
			</div>

			<div class="form-item cvv-code">
				<input type="text" size="4" placeholder="<?= _l("CVV"); ?>" data-encrypted-name="cvv"/>
			</div>

			<div class="form-item expiration">
				<input type="text" class="center small" placeholder="<?= _l("Month"); ?>" data-encrypted-name="month" size="2" maxlength="2"/> /
				<input type="text" class="center small" placeholder="<?= _l("Year"); ?>" data-encrypted-name="year" size="4" maxlength="4"/>
			</div>
		</div>
	</div>
</section>

<script>
	var $form = $('.braintree-register-fields').closest('form');

	var ajax_submit = function (e) {
		console.log('submitting after bt');
		return false;
	}

	function braintree() {
		if (typeof Braintree == 'undefined') {
			setTimeout(braintree, 5);
		} else {
			if (!$form.attr('id')) {
				$form.attr('id', 'braintree-register-form');
			}
			var bt = Braintree.create("<?= $encryption_key; ?>");
			bt.onSubmitEncryptForm($form.attr('id'), ajax_submit);
		}
	};

	$.getScript("https://js.braintreegateway.com/v1/braintree.js", braintree);
</script>
