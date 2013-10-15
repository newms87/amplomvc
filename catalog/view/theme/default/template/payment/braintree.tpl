<div class="braintree_form"
	<h1>Braintree Credit Card Transaction Form</h1>
	<div>
		<form action="<?= $confirm; ?>" method="POST" id="braintree-payment-form">
			<p>
				<label>Card Number</label>
				<input type="text" size="20" autocomplete="off" data-encrypted-name="number" />
			</p>
			<p>
				<label>CVV</label>
				<input type="text" size="4" autocomplete="off" data-encrypted-name="cvv" />
			</p>
			<p>
				<label>Expiration (MM/YYYY)</label>
				<input type="text" class="center" size="2" maxlength="2" name="month" /> / <input type="text" class="center" size="4" maxlength="4" name="year" />
			</p>
			<input type="submit" id="submit" value="<?= $button_submit; ?>" class="button subscribe"/>
		</form>
	</div>
</div>

<script src="https://js.braintreegateway.com/v1/braintree.js"></script>
<script>
	var braintree = Braintree.create("<?= $encryption_key; ?>");
	braintree.onSubmitEncryptForm('braintree-payment-form');
</script>
