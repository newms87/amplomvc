<?= $header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<? if ($error_warning) { ?>
		<div class="message_box warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'tax.png'; ?>" alt=""/> <?= _l("Tax Class"); ?></h1>

			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a><a
					href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a></div>
		</div>
		<div class="section">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= _l("Tax Class Title:"); ?></td>
						<td><input type="text" name="title" value="<?= $title; ?>"/>
							<? if (_l("Tax Class Title must be between 3 and 32 characters!")) { ?>
								<span class="error"><?= _l("Tax Class Title must be between 3 and 32 characters!"); ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td class="required"> <?= _l("Description:"); ?></td>
						<td><input type="text" name="description" value="<?= $description; ?>"/>
							<? if (_l("Description must be between 3 and 255 characters!")) { ?>
								<br/>
								<span class="error"><?= _l("Description must be between 3 and 255 characters!"); ?></span>
							<? } ?></td>
					</tr>
				</table>
				<br/>
				<table id="tax-rule" class="list">
					<thead>
						<tr>
							<td class="left"><?= _l("Tax Rate:"); ?></td>
							<td class="left"><?= _l("Based On:"); ?></td>
							<td class="left"><?= _l("Priority:"); ?></td>
							<td></td>
						</tr>
					</thead>
					<? $tax_rule_row = 0; ?>
					<? foreach ($tax_rules as $tax_rule) { ?>
						<tbody id="tax-rule-row<?= $tax_rule_row; ?>">
							<tr>
								<td class="left"><select name="tax_rule[<?= $tax_rule_row; ?>][tax_rate_id]">
										<? foreach ($tax_rates as $tax_rate) { ?>
											<? if ($tax_rate['tax_rate_id'] == $tax_rule['tax_rate_id']) { ?>
												<option value="<?= $tax_rate['tax_rate_id']; ?>"
													selected="selected"><?= $tax_rate['name']; ?></option>
											<? } else { ?>
												<option value="<?= $tax_rate['tax_rate_id']; ?>"><?= $tax_rate['name']; ?></option>
											<? } ?>
										<? } ?>
									</select></td>
								<td class="left"><select name="tax_rule[<?= $tax_rule_row; ?>][based]">
										<? if ($tax_rule['based'] == 'shipping') { ?>
											<option value="shipping" selected="selected"><?= _l("Shipping Address"); ?></option>
										<? } else { ?>
											<option value="shipping"><?= _l("Shipping Address"); ?></option>
										<? } ?>
										<? if ($tax_rule['based'] == 'payment') { ?>
											<option value="payment" selected="selected"><?= _l("Payment Address"); ?></option>
										<? } else { ?>
											<option value="payment"><?= _l("Payment Address"); ?></option>
										<? } ?>
										<? if ($tax_rule['based'] == 'store') { ?>
											<option value="store" selected="selected"><?= _l("Store Address"); ?></option>
										<? } else { ?>
											<option value="store"><?= _l("Store Address"); ?></option>
										<? } ?>
									</select></td>
								<td class="left"><input type="text" name="tax_rule[<?= $tax_rule_row; ?>][priority]" value="<?= $tax_rule['priority']; ?>" size="1"/></td>
								<td class="left"><a onclick="$('#tax-rule-row<?= $tax_rule_row; ?>').remove();"
										class="button"><?= _l("Remove"); ?></a></td>
							</tr>
						</tbody>
						<? $tax_rule_row++; ?>
					<? } ?>
					<tfoot>
						<tr>
							<td colspan="3"></td>
							<td class="left"><a onclick="addRule();" class="button"><?= _l("Add Rule"); ?></a></td>
						</tr>
					</tfoot>
				</table>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript"><
	!--
	var tax_rule_row = <?= $tax_rule_row; ?>;

	function addRule() {
		html = '<tbody id="tax-rule-row' + tax_rule_row + '">';
		html += '	<tr>';
		html += '		<td class="left"><select name="tax_rule[' + tax_rule_row + '][tax_rate_id]">';
		<? foreach ($tax_rates as $tax_rate) { ?>
		html += '			<option value="<?= $tax_rate['tax_rate_id']; ?>"><?= addslashes($tax_rate['name']); ?></option>';
		<? } ?>
		html += '		</select></td>';
		html += '		<td class="left"><select name="tax_rule[' + tax_rule_row + '][based]">';
		html += '			<option value="shipping"><?= _l("Shipping Address"); ?></option>';
		html += '			<option value="payment"><?= _l("Payment Address"); ?></option>';
		html += '			<option value="store"><?= _l("Store Address"); ?></option>';
		html += '		</select></td>';
		html += '		<td class="left"><input type="text" name="tax_rule[' + tax_rule_row + '][priority]" value="" size="1" /></td>';
		html += '		<td class="left"><a onclick="$(\'#tax-rule-row' + tax_rule_row + '\').remove();" class="button"><?= _l("Remove"); ?></a></td>';
		html += '	</tr>';
		html += '</tbody>';

		$('#tax-rule > tfoot').before(html);

		tax_rule_row++;
	}
</script>
<?= $footer; ?>
