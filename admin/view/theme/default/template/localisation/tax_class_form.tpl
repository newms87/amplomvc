<?= $header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<? if ($error_warning) { ?>
			<div class="message_box warning"><?= $error_warning; ?></div>
		<? } ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'tax.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a
						href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
			</div>
			<div class="section">
				<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
					<table class="form">
						<tr>
							<td class="required"> <?= $entry_title; ?></td>
							<td><input type="text" name="title" value="<?= $title; ?>"/>
								<? if ($error_title) { ?>
									<span class="error"><?= $error_title; ?></span>
								<? } ?></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_description; ?></td>
							<td><input type="text" name="description" value="<?= $description; ?>"/>
								<? if ($error_description) { ?>
									<br/>
									<span class="error"><?= $error_description; ?></span>
								<? } ?></td>
						</tr>
					</table>
					<br/>
					<table id="tax-rule" class="list">
						<thead>
						<tr>
							<td class="left"><?= $entry_rate; ?></td>
							<td class="left"><?= $entry_based; ?></td>
							<td class="left"><?= $entry_priority; ?></td>
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
											<option value="shipping" selected="selected"><?= $text_shipping; ?></option>
										<? } else { ?>
											<option value="shipping"><?= $text_shipping; ?></option>
										<? } ?>
										<? if ($tax_rule['based'] == 'payment') { ?>
											<option value="payment" selected="selected"><?= $text_payment; ?></option>
										<? } else { ?>
											<option value="payment"><?= $text_payment; ?></option>
										<? } ?>
										<? if ($tax_rule['based'] == 'store') { ?>
											<option value="store" selected="selected"><?= $text_store; ?></option>
										<? } else { ?>
											<option value="store"><?= $text_store; ?></option>
										<? } ?>
									</select></td>
								<td class="left"><input type="text" name="tax_rule[<?= $tax_rule_row; ?>][priority]" value="<?= $tax_rule['priority']; ?>" size="1"/></td>
								<td class="left"><a onclick="$('#tax-rule-row<?= $tax_rule_row; ?>').remove();"
								                    class="button"><?= $button_remove; ?></a></td>
							</tr>
							</tbody>
							<? $tax_rule_row++; ?>
						<? } ?>
						<tfoot>
						<tr>
							<td colspan="3"></td>
							<td class="left"><a onclick="addRule();" class="button"><?= $button_add_rule; ?></a></td>
						</tr>
						</tfoot>
					</table>
				</form>
			</div>
		</div>
	</div>
	<script type="text/javascript"><!--
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
			html += '			<option value="shipping"><?= $text_shipping; ?></option>';
			html += '			<option value="payment"><?= $text_payment; ?></option>';
			html += '			<option value="store"><?= $text_store; ?></option>';
			html += '		</select></td>';
			html += '		<td class="left"><input type="text" name="tax_rule[' + tax_rule_row + '][priority]" value="" size="1" /></td>';
			html += '		<td class="left"><a onclick="$(\'#tax-rule-row' + tax_rule_row + '\').remove();" class="button"><?= $button_remove; ?></a></td>';
			html += '	</tr>';
			html += '</tbody>';

			$('#tax-rule > tfoot').before(html);

			tax_rule_row++;
		}
//--></script>
<?= $footer; ?>