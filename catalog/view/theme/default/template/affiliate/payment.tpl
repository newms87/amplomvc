<?= $header; ?><?= $column_left; ?><?= $column_right; ?>
<div id="content"><?= $content_top; ?>
	<?= $this->builder->display_breadcrumbs(); ?>
	<h1><?= $heading_title; ?></h1>
	<form action="<?= $action; ?>" method="post" enctype="multipart/form-data">
		<h2><?= $text_your_payment; ?></h2>
		<div class="content">
			<table class="form">
				<tbody>
					<tr>
						<td><?= $entry_tax; ?></td>
						<td><input type="text" name="tax" value="<?= $tax; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_payment; ?></td>
						<td><? if ($payment == 'cheque') { ?>
							<input type="radio" name="payment" value="cheque" id="cheque" checked="checked" />
							<? } else { ?>
							<input type="radio" name="payment" value="cheque" id="cheque" />
							<? } ?>
							<label for="cheque"><?= $text_cheque; ?></label>
							<? if ($payment == 'paypal') { ?>
							<input type="radio" name="payment" value="paypal" id="paypal" checked="checked" />
							<? } else { ?>
							<input type="radio" name="payment" value="paypal" id="paypal" />
							<? } ?>
							<label for="paypal"><?= $text_paypal; ?></label>
							<? if ($payment == 'bank') { ?>
							<input type="radio" name="payment" value="bank" id="bank" checked="checked" />
							<? } else { ?>
							<input type="radio" name="payment" value="bank" id="bank" />
							<? } ?>
							<label for="bank"><?= $text_bank; ?></label></td>
					</tr>
				</tbody>
				<tbody id="payment-cheque" class="payment">
					<tr>
						<td><?= $entry_cheque; ?></td>
						<td><input type="text" name="cheque" value="<?= $cheque; ?>" /></td>
					</tr>
				</tbody>
				<tbody class="payment" id="payment-paypal">
					<tr>
						<td><?= $entry_paypal; ?></td>
						<td><input type="text" name="paypal" value="<?= $paypal; ?>" /></td>
					</tr>
				</tbody>
				<tbody id="payment-bank" class="payment">
					<tr>
						<td><?= $entry_bank_name; ?></td>
						<td><input type="text" name="bank_name" value="<?= $bank_name; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_bank_branch_number; ?></td>
						<td><input type="text" name="bank_branch_number" value="<?= $bank_branch_number; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_bank_swift_code; ?></td>
						<td><input type="text" name="bank_swift_code" value="<?= $bank_swift_code; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_bank_account_name; ?></td>
						<td><input type="text" name="bank_account_name" value="<?= $bank_account_name; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_bank_account_number; ?></td>
						<td><input type="text" name="bank_account_number" value="<?= $bank_account_number; ?>" /></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="buttons">
			<div class="left"><a href="<?= $back; ?>" class="button"><?= $button_back; ?></a></div>
			<div class="right"><input type="submit" value="<?= $button_continue; ?>" class="button" /></div>
		</div>
	</form>
	<?= $content_bottom; ?></div>
<script type="text/javascript">
//<!--
$('input[name=\'payment\']').bind('change', function() {
	$('.payment').hide();
	
	$('#payment-' + this.value).show();
});

$('input[name=\'payment\']:checked').trigger('change');
//--></script>
<?= $footer; ?> 