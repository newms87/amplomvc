<? if (isset($no_payment_address)) { ?>
	<h2><?= _l("Please select a payment address."); ?></h2>

<? } elseif (!empty($payment_methods)) { ?>
	<form action="<?= $validate_payment_method; ?>" method="post">
		<p><?= _l("Payment Method"); ?></p>
		<table class="radio">
			<? foreach ($payment_methods as $payment_method) { ?>
				<tr class="payment_method checkout_method highlight">
					<td class="method_id">
						<input type="radio" name="payment_method" value="<?= $payment_method['code']; ?>" id="<?= $payment_method['code']; ?>" <?= $payment_method['code'] == $code ? 'checked="checked"' : ''; ?> />
					</td>
					<td class="method_title"><label for="<?= $payment_method['code']; ?>"><?= $payment_method['title']; ?></label></td>
				</tr>
			<? } ?>
		</table>
		<br/>

		<div id="add_comment">
			<div><?= _l("Order Comments"); ?></div>
			<textarea name="comment" rows="8"><?= $comment; ?></textarea>
		</div>

		<? if (!empty($checkout_terms)) { ?>
			<div class="buttons">
				<div class="right">
					<span>
						<?= _l("I have read and agree to the"); ?>
						<a class="colorbox" onclick="return colorbox($(this))" href="<?= $checkout_terms; ?>" target="_blank" alt="<?= $checkout_terms_title; ?>"><b><?= $checkout_terms_title; ?></b></a>
					</span>
					<input type="checkbox" name="agree" value="1" <?= $agree ? 'checked="checked"' : ''; ?> />
				</div>
			</div>
		<? } ?>
	</form>

	<script type="text/javascript">
		$('#add_comment div').click(function () {
			$('#add_comment textarea').slideToggle()
		});
	</script>

<? } else { ?>
	<h2><?= _l("There are no payment methods available for your billing address."); ?></h2>
<? } ?>
