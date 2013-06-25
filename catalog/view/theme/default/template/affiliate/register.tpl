<?= $header; ?>
<? if ($error_warning) { ?>
<div class="message_box warning"><?= $error_warning; ?></div>
<? } ?>
<?= $column_left; ?><?= $column_right; ?>
<div id="content"><?= $content_top; ?>
	<?= $breadcrumbs; ?>
	<h1><?= $heading_title; ?></h1>
	<p><?= $text_account_already; ?></p>
	<p><?= $text_signup; ?></p>
	<form action="<?= $action; ?>" method="post" enctype="multipart/form-data">
		<h2><?= $text_your_details; ?></h2>
		<div class="content">
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
						<? } ?></td>
				</tr>
				<tr>
					<td class="required"> <?= $entry_telephone; ?></td>
					<td><input type="text" name="telephone" value="<?= $telephone; ?>" />
						<? if ($error_telephone) { ?>
						<span class="error"><?= $error_telephone; ?></span>
						<? } ?></td>
				</tr>
				<tr>
					<td><?= $entry_fax; ?></td>
					<td><input type="text" name="fax" value="<?= $fax; ?>" /></td>
				</tr>
			</table>
		</div>
		<h2><?= $text_your_address; ?></h2>
		<div class="content">
			<table class="form">
				<tr>
					<td><?= $entry_company; ?></td>
					<td><input type="text" name="company" value="<?= $company; ?>" /></td>
				</tr>
				<tr>
					<td><?= $entry_website; ?></td>
					<td><input type="text" name="website" value="<?= $website; ?>" /></td>
				</tr>
				<tr>
					<td class="required"> <?= $entry_address_1; ?></td>
					<td><input type="text" name="address_1" value="<?= $address_1; ?>" />
						<? if ($error_address_1) { ?>
						<span class="error"><?= $error_address_1; ?></span>
						<? } ?></td>
				</tr>
				<tr>
					<td><?= $entry_address_2; ?></td>
					<td><input type="text" name="address_2" value="<?= $address_2; ?>" /></td>
				</tr>
				<tr>
					<td class="required"> <?= $entry_city; ?></td>
					<td><input type="text" name="city" value="<?= $city; ?>" />
						<? if ($error_city) { ?>
						<span class="error"><?= $error_city; ?></span>
						<? } ?></td>
				</tr>
				<tr>
					<td class="required"> <?= $entry_postcode; ?></td>
					<td><input type="text" name="postcode" value="<?= $postcode; ?>" />
						<? if ($error_postcode) { ?>
						<span class="error"><?= $error_postcode; ?></span>
						<? } ?></td>
				</tr>
				<tr>
					<td class="required"> <?= $entry_country; ?></td>
					<td>
						<?= $this->builder->set_config('country_id', 'name'); ?>
						<?= $this->builder->build('select', $countries, "country_id", $country_id, array('class'=>"country_select")); ?>
					</td>
				</tr>
				<tr>
					<td class="required"> <?= $entry_zone; ?></td>
					<td><select name="zone_id" class="zone_select" zone_id="<?= $zone_id; ?>"></select></td>
				</tr>
			</table>
		</div>
		<h2><?= $text_payment; ?></h2>
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
		<h2><?= $text_your_password; ?></h2>
		<div class="content">
			<table class="form">
				<tr>
					<td class="required"> <?= $entry_password; ?></td>
					<td><input type="password" autocomplete='off' name="password" value="<?= $password; ?>" />
						<? if ($error_password) { ?>
						<span class="error"><?= $error_password; ?></span>
						<? } ?></td>
				</tr>
				<tr>
					<td class="required"> <?= $entry_confirm; ?></td>
					<td><input type="password" autocomplete='off' name="confirm" value="<?= $confirm; ?>" />
						<? if ($error_confirm) { ?>
						<span class="error"><?= $error_confirm; ?></span>
						<? } ?></td>
				</tr>
			</table>
		</div>
		<? if ($text_agree) { ?>
		<div class="buttons">
			<div class="right"><?= $text_agree; ?>
				<? if ($agree) { ?>
				<input type="checkbox" name="agree" value="1" checked="checked" />
				<? } else { ?>
				<input type="checkbox" name="agree" value="1" />
				<? } ?>
				<input type="submit" value="<?= $button_continue; ?>" class="button" />
			</div>
		</div>
		<? } else { ?>
		<div class="buttons">
			<div class="right">
				<input type="submit" value="<?= $button_continue; ?>" class="button" />
			</div>
		</div>
		<? } ?>
	</form>
	<?= $content_bottom; ?></div>

<?= $this->builder->js('load_zones', 'table.form', '.country_select', '.zone_select'); ?>
 
<script type="text/javascript">
//<!--
$('input[name=\'payment\']').bind('change', function() {
	$('.payment').hide();
	
	$('#payment-' + this.value).show();
});

$('input[name=\'payment\']:checked').trigger('change');
//--></script>
<script type="text/javascript">
//<!--
$('.colorbox').colorbox({
	width: 560,
	height: 560
});
//--></script>
<?= $footer; ?>