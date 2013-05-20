<? if(isset($no_payment_address)) {?>
	<h2><?= $text_no_payment_address;?></h2>
	
<? } elseif(!empty($payment_methods)) { ?>
<form action="<?= $validate_payment_method; ?>" method="post">
	<p><?= $text_payment_method; ?></p>
	<table class="radio">
		<? foreach ($payment_methods as $payment_method) { ?>
		<tr class="payment_method checkout_method highlight">
			<td class="method_id">
				<input type="radio" name="payment_method" value="<?= $payment_method['code']; ?>" id="<?= $payment_method['code']; ?>" <?= $payment_method['code'] == $code ? 'checked="checked"' : '';?> />
			</td>
			<td class="method_title"><label for="<?= $payment_method['code']; ?>"><?= $payment_method['title']; ?></label></td>
		</tr>
		<? } ?>
	</table>
	<br />

	<div id='add_comment'>
		<div><?= $text_comments; ?></div>
		<textarea name="comment" rows="8"><?= $comment; ?></textarea>
	</div>
	
	<? if (!empty($agree_to_payment)) { ?>
	<div class="buttons">
		<div class="right">
			<span><?= $text_agree; ?></span>
			<input type="checkbox" name="agree" value="1" <?= $agree ? 'checked="checked"' : '';?> />
		</div>
	</div>
	<? } ?>
</form>

<script type="text/javascript">//<!--
$('#add_comment div').click(function(){$('#add_comment textarea').slideToggle()});
//--></script>

<? } ?> 