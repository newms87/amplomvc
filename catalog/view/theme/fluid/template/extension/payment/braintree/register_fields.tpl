<section class="braintree-register-card braintree-fields content">
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
	var $form = $('.braintree-register-card').closest('form');

	$('.ac-radio').click(function () {
		$(this).closest('label').children('input[type=radio]').prop('checked', true);
	});

	function braintree() {
		$form.submit(function () {
			if ($('[data-encrypted-name]').val()) {
				var bt = Braintree.create("<?= $encryption_key; ?>");
				bt.encryptForm($form[0]);
			}
		});

		//Hack to reverse the jQuery event queue. Always encrypt before submitting!
		$._data($form[0], 'events').submit.reverse();
	}

	$.getScript("https://js.braintreegateway.com/v1/braintree.js", braintree);
</script>
