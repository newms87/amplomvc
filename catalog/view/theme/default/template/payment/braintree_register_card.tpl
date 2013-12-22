<?= $header; ?>
<div id="braintree_register_card" class="content">
	<div class="box">
		<div class="box_heading"><?= $register_card_title; ?></div>

		<div class="section">
			<form action="<?= $submit; ?>" method="POST" id="braintree-payment-form">
				<h2><?= $text_customer_information; ?></h2>
				<p>
					<label for="firstname"><?= $entry_firstname; ?></label>
					<input type="text" name="firstname" id="firstname" value="<?= $firstname; ?>" />
				</p>
				<p>
					<label for="lastname"><?= $entry_lastname; ?></label>
					<input type="text" name="lastname" id="lastname" value="<?= $lastname; ?>" />
				</p>
				<p>
					<label for="postcode"><?= $entry_postcode; ?></label>
					<input type="text" name="postcode" id="postcode" value="<?= $postcode; ?>"/>
				</p>
				<h2><?= $text_credit_card; ?></h2>
				<p>
					<label for="card_number"><?= $entry_card_number; ?></label>
					<input id="card_number" type="text" size="20" autocomplete="off" data-encrypted-name="number" />
				</p>
				<p>
					<label for="cvv"><?= $entry_cvv; ?></label>
					<input id="cvv" type="text" size="4" autocomplete="off" data-encrypted-name="cvv" />
				</p>
				<p>
					<label for="expiration"><?= $entry_expiration; ?></label>
					<input id="expiration" type="text" class="center" size="2" maxlength="2" data-encrypted-name="month" /> / <input type="text" class="center" size="4" maxlength="4" data-encrypted-name="year" />
				</p>
				<input class="button subscribe" id="register_card_submit" type="submit" value="<?= $button_submit; ?>" />
			</form>
		</div>
	</div>
</div>

<script>
	var submit = $('#register_card_submit');

	function loadingToggle(start){
		if (start) {
			submit.parent().loading();
			submit.attr("disabled", "disabled");
		} else {
			submit.parent().loading('stop');
			submit.removeAttr("disabled");
		}
	};

	loadingToggle(true);

	var ajax_submit = function (e) {
		loadingToggle(true);

		form = $('#braintree-payment-form');
		e.preventDefault();
		$.post(form.attr('action'), form.serialize(), function (json) {
			loadingToggle(false);

			if (typeof json === 'string') {
				show_msg('warning', json);
			}
			else if(json['error']) {
				show_msgs(json['error'], 'error');
				show_errors(json['error'], $('#braintree-payment-form'));
			} else if (json['success']) {
				show_msg('success', json['success']);
			}

			if (json['redirect']) {
				location = json['redirect'];
			}
		}, 'json').fail(function(jqXHR){show_msg('error', jqXHR.responseText);});

		return false;
	}

	var braintree = function(){
		if (typeof Braintree === 'undefined') {
			setTimeout(bt, 5);
		} else {
			loadingToggle(false);

			var braintree = Braintree.create("<?= $encryption_key; ?>");
			braintree.onSubmitEncryptForm("braintree-payment-form", ajax_submit);
		}
	};

	$.getScript("https://js.braintreegateway.com/v1/braintree.js", braintree);
</script>

<?= $footer; ?>
