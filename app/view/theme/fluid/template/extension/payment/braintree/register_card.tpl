<section class="braintree-register-card content">
	<header class="row top-row">
		<div class="wrap">
		<h1><?= _l("Register a New Card"); ?></h1>
		</div>
	</header>

	<div class="braintree-form-row row">
		<div class="wrap">
			<form id="braintree-payment-form" class="form" action="<?= $submit; ?>" method="post">
				<div class="form-item card-number">
					<input type="text" placeholder="<?= _l("Card Number"); ?>" data-encrypted-name="number" />
				</div>

				<div class="form-item cvv-code">
					<input type="text" size="4" placeholder="<?= _l("CVV"); ?>" data-encrypted-name="cvv" />
				</div>

				<div class="form-item expiration">
					<input type="text" class="center" placeholder="<?= _l("Month"); ?>" data-encrypted-name="month" size="2" maxlength="2" /> /
					<input type="text" class="center" placeholder="<?= _l("Year"); ?>" data-encrypted-name="year" size="4" maxlength="4"  />
				</div>

				<div class="form-item submit">
					<button class="register-card" data-loading="Submitting..."><?= _l("Register Card"); ?></button>
				</div>
			</form>
		</div>
	</div>
</section>

<script>
	var $form = $('#braintree-payment-form');;
	var $submit = $form.find('button.register-card');

	var ajax_submit = function (e) {
		$submit.loading();

		$.post($form.attr('action'), $form.serialize(),function (json) {
			$submit.loading('stop');

			if (typeof json === 'string') {
				$form.ac_msg('error', json);
			}
			else if (json['error']) {
				$form.ac_errors(json['error']);
			} else if (json['success']) {
				$form.ac_msg('success', json['success']);
				location.reload();
			}

		}, 'json').fail(function (jqXHR) {
			$form.ac_msg('error', jqXHR.responseText);
		});

		return false;
	}

	var braintree = function () {
		if (typeof Braintree === 'undefined') {
			setTimeout(bt, 5);
		} else {
			var braintree = Braintree.create("<?= $encryption_key; ?>");
			braintree.onSubmitEncryptForm("braintree-payment-form", ajax_submit);
		}
	};

	$.getScript("https://js.braintreegateway.com/v1/braintree.js", braintree);
</script>
