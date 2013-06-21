<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs(); ?>
	<? if ($error_warning) { ?>
	<div class="message_box warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'customer.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<div id="htabs" class="htabs"><a href="#tab-general"><?= $tab_general; ?></a> <a href="#tab-payment"><?= $tab_payment; ?></a>
				<? if ($affiliate_id) { ?>
				<a href="#tab-transaction"><?= $tab_transaction; ?></a>
				<? } ?>
			</div>
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<div id="tab-general">
					<table class="form">
						<tr>
							<td class="required"> <?= $entry_firstname; ?></td>
							<td><input type="text" name="firstname" value="<?= $firstname; ?>" />
								<? if ($error_firstname) { ?>
								<span class="error"><?= $error_firstname; ?></span>
								<? } ?></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_lastname; ?></td>
							<td><input type="text" name="lastname" value="<?= $lastname; ?>" />
								<? if ($error_lastname) { ?>
								<span class="error"><?= $error_lastname; ?></span>
								<? } ?></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_email; ?></td>
							<td><input type="text" name="email" value="<?= $email; ?>" />
								<? if ($error_email) { ?>
								<span class="error"><?= $error_email; ?></span>
								<?	} ?></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_telephone; ?></td>
							<td><input type="text" name="telephone" value="<?= $telephone; ?>" />
								<? if ($error_telephone) { ?>
								<span class="error"><?= $error_telephone; ?></span>
								<?	} ?></td>
						</tr>
						<tr>
							<td><?= $entry_fax; ?></td>
							<td><input type="text" name="fax" value="<?= $fax; ?>" /></td>
						</tr>
						<tr>
							<td><?= $entry_company; ?></td>
							<td><input type="text" name="company" value="<?= $company; ?>" /></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_address_1; ?></td>
							<td><input type="text" name="address_1" value="<?= $address_1; ?>" />
								<? if ($error_address_1) { ?>
								<span class="error"><?= $error_address_1; ?></span>
								<?	} ?></td>
						</tr>
						<tr>
							<td><?= $entry_address_2; ?></td>
							<td><input type="text" name="address_2" value="<?= $address_2; ?>" /></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_city; ?></td>
							<td><input type="text" name="city" value="<?= $city; ?>" />
								<? if ($error_city) { ?>
								<span class="error"><?= $error_city ?></span>
								<?	} ?></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_postcode; ?></td>
							<td><input type="text" name="postcode" value="<?= $postcode; ?>" />
								<? if ($error_postcode) { ?>
								<span class="error"><?= $error_postcode ?></span>
								<?	} ?></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_country; ?></td>
							<td>
								<?= $this->builder->set_config('country_id', 'name'); ?>
								<?= $this->builder->build('select', $countries, 'country_id', $country_id); ?>
								<? if ($error_country) { ?>
								<span class="error"><?= $error_country; ?></span>
								<? } ?></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_zone; ?></td>
							<td><select zone_id='<?= $zone_id; ?>' name="zone_id"></select>
								<? if ($error_zone) { ?>
								<span class="error"><?= $error_zone; ?></span>
								<? } ?></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_code; ?></td>
							<td><input type="code" name="code" value="<?= $code; ?>"	/>
								<? if ($error_code) { ?>
								<span class="error"><?= $error_code; ?></span>
								<? } ?></td>
						</tr>
						<tr>
							<td><?= $entry_password; ?></td>
							<td><input type="password" autocomplete='off' name="password" value="<?= $password; ?>"	/>
								<? if ($error_password) { ?>
								<span class="error"><?= $error_password; ?></span>
								<?	} ?></td>
						</tr>
						<tr>
							<td><?= $entry_confirm; ?></td>
							<td><input type="password" autocomplete='off' name="confirm" value="<?= $confirm; ?>" />
								<? if ($error_confirm) { ?>
								<span class="error"><?= $error_confirm; ?></span>
								<?	} ?></td>
						</tr>
						<tr>
							<td><?= $entry_status; ?></td>
							<td><select name="status">
									<? if ($status) { ?>
									<option value="1" selected="selected"><?= $text_enabled; ?></option>
									<option value="0"><?= $text_disabled; ?></option>
									<? } else { ?>
									<option value="1"><?= $text_enabled; ?></option>
									<option value="0" selected="selected"><?= $text_disabled; ?></option>
									<? } ?>
								</select></td>
						</tr>
					</table>
				</div>
				<div id="tab-payment">
					<table class="form">
						<tbody>
							<tr>
								<td><?= $entry_commission; ?></td>
								<td><input type="text" name="commission" value="<?= $commission; ?>" /></td>
							</tr>
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
						<tbody id="payment-paypal" class="payment">
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
								<td class="required"> <?= $entry_bank_account_name; ?></td>
								<td><input type="text" name="bank_account_name" value="<?= $bank_account_name; ?>" /></td>
							</tr>
							<tr>
								<td class="required"> <?= $entry_bank_account_number; ?></td>
								<td><input type="text" name="bank_account_number" value="<?= $bank_account_number; ?>" /></td>
							</tr>
						</tbody>
					</table>
				</div>
				<? if ($affiliate_id) { ?>
				<div id="tab-transaction">
					<table class="form">
						<tr>
							<td><?= $entry_description; ?></td>
							<td><input type="text" name="description" value="" /></td>
						</tr>
						<tr>
							<td><?= $entry_amount; ?></td>
							<td><input type="text" name="amount" value="" /></td>
						</tr>
						<tr>
							<td colspan="2" style="text-align: right;"><a id="button-reward" class="button" onclick="addTransaction();"><span><?= $button_add_transaction; ?></span></a></td>
						</tr>
					</table>
					<div id="transaction"></div>
				</div>
				<? } ?>
			</form>
		</div>
	</div>
</div>

<?= $this->builder->js('load_zones', 'select[name=country_id]','select[name=zone_id]'); ?>
 
<script type="text/javascript"><!--
$('input[name=\'payment\']').bind('change', function() {
	$('.payment').hide();
	
	$('#payment-' + this.value).show();
});

$('input[name=\'payment\']:checked').trigger('change');
//--></script>
<script type="text/javascript"><!--
$('#transaction .pagination a').live('click', function() {
	$('#transaction').load(this.href);
	
	return false;
});

$('#transaction').load("<?= HTTP_ADMIN . "index.php?route=sale/affiliate/transaction"; ?>" + '&affiliate_id=<?= $affiliate_id; ?>');

function addTransaction() {
	$.ajax({
		url: "<?= HTTP_ADMIN . "index.php?route=sale/affiliate/transaction"; ?>" + '&affiliate_id=<?= $affiliate_id; ?>',
		type: 'post',
		dataType: 'html',
		data: 'description=' + encodeURIComponent($('#tab-transaction input[name=\'description\']').val()) + '&amount=' + encodeURIComponent($('#tab-transaction input[name=\'amount\']').val()),
		beforeSend: function() {
			$('.success, .warning').remove();
			$('#button-transaction').attr('disabled', true);
			$('#transaction').before('<div class="attention"><img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>" alt="" /> <?= $text_wait; ?></div>');
		},
		complete: function() {
			$('#button-transaction').attr('disabled', false);
			$('.attention').remove();
		},
		success: function(html) {
			$('#transaction').html(html);
			
			$('#tab-transaction input[name=\'amount\']').val('');
			$('#tab-transaction input[name=\'description\']').val('');
		}
	});
}
//--></script>
<script type="text/javascript"><!--
$('.htabs a').tabs();
//--></script>
<?= $footer; ?>