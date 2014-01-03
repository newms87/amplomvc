<table class="form">
	<tr>
		<td><?= _l("Price Sets:"); ?></td>
		<td>
			<table id="pricesets" class="list">
				<thead>
					<tr>
						<td class="left"><?= _l("Total Price Range"); ?></td>
						<td class="left"><?= _l("Total in Cart"); ?></td>
						<td class="left"><?= _l("Shipping Cost"); ?></td>
						<td class="left"><?= _l("Cost Type"); ?></td>
						<td></td>
					</tr>
				</thead>
				<? $set_row = 0; ?>
				<? foreach ($amount_priceset as $set) { ?>
					<tbody id="set-row<?= $set_row; ?>">
						<tr>
							<td class="left"><?= $this->builder->build('select', $priceset_ranges, "amount_priceset[$set_row][range]", $set['range'], array('onclick' => 'range_values($(this))')); ?></td>
							<td class="left pricetotal">
								<span class="total" <?= $set['range'] == 'range' ? "style=\"display:none\"" : ''; ?>>
									<input type="text" name="amount_priceset[<?= $set_row; ?>][total]" value="<?= $set['total']; ?>"/>
								</span>
								<span class="pricerange" <?= $set['range'] != 'range' ? "style=\"display:none\"" : ''; ?>>
										<?= _l("Min Price"); ?>
									<input type="text" name="amount_priceset[<?= $set_row; ?>][from]" value="<?= $set['from']; ?>"/>
									<?= _l("Max Price"); ?><input type="text" name="amount_priceset[<?= $set_row; ?>][to]" value="<?= $set['to']; ?>"/>
								</span>
							</td>
							<td class="left"><input type="text" name="amount_priceset[<?= $set_row; ?>][cost]" value="<?= $set['cost']; ?>"/></td>
							<td
								class="left"><?= $this->builder->build('select', $priceset_types, "amount_priceset[$set_row][type]", $set['type']); ?></td>
							<td class="left"><a onclick="$('#set-row<?= $set_row; ?>').remove();"
									class="button"><?= _l("Remove"); ?></a></td>
						</tr>
					</tbody>
					<? $set_row++; ?>
				<? } ?>
				<tfoot>
					<tr>
						<td colspan="6"></td>
						<td class="left"><a onclick="addPriceSet();" class="button"><?= _l("Add Price Set"); ?></a>
						</td>
					</tr>
				</tfoot>
			</table>
		</td>
	</tr>
	<tr>
		<td><?= _l("Zone Rules"); ?></td>
		<td>
			<table id="zonerules" class="list">
				<thead>
					<tr>
						<td class="left"><?= _l("Zone"); ?></td>
						<td class="left"><?= _l("Modifier"); ?></td>
						<td class="left"><?= _l("Modified Cost"); ?></td>
						<td class="left"><?= _l("Cost Type"); ?></td>
						<td></td>
					</tr>
				</thead>
				<? $rule_row = 0; ?>
				<? foreach ($amount_zonerule as $rule) { ?>
					<tbody id="rule-row<?= $rule_row; ?>">
						<tr>
							<td class="left">
								<? $this->builder->setConfig('country_id', 'name'); ?>
								<?= $this->builder->build('select', $countries, "amount_zonerule[$rule_row][country_id]", $rule['country_id'], array('class' => 'country_select')); ?>
								<select id="zone_id-<?= $rule_row; ?>" name="amount_zonerule[<?= $rule_row; ?>][zone_id]" class="zone_select"
									zone_id="<?= $rule['zone_id']; ?>"></select>
							</td>
							<td
								class="left"><?= $this->builder->build('select', $rule_mods, "amount_zonerule[$rule_row][mod]", $rule['mod'], array('id' => "zone_id-$rule_row")); ?></td>
							<td class="left"><input type="text" name="amount_zonerule[<?= $rule_row; ?>][cost]" value="<?= $rule['cost']; ?>"/></td>
							<td
								class="left"><?= $this->builder->build('select', $priceset_types, "amount_zonerule[$rule_row][type]", $rule['type']); ?></td>
							<td class="left"><a onclick="$('#rule-row<?= $rule_row; ?>').remove();"
									class="button"><?= _l("Remove"); ?></a></td>
						</tr>
					</tbody>
					<? $rule_row++; ?>
				<? } ?>
				<tfoot>
					<tr>
						<td colspan="6"></td>
						<td class="left"><a onclick="addZoneRule();" class="button"><?= _l("Add Zone Rule"); ?></a>
						</td>
					</tr>
				</tfoot>
			</table>
		</td>
	</tr>
</table>

<script type="text/javascript">
	var set_row = <?= $set_row; ?>;
	function addPriceSet() {
		html = '<tbody id="set-row%set_row%">';
		html += '	<tr>';
		html += '		<td class="left">' + "<?= $this->builder->build('select',$priceset_ranges,"amount_priceset[%set_row%][range]",'',array('onclick'=>'range_values($(this))'),true); ?>" + '</td>';
		html += '		<td class="left pricetotal"><span class="total"><input type="text" name="amount_priceset[%set_row%][total]" value="" /></span><span class="pricerange" style="display:none;"><?= _l("Min Price"); ?> <input type="text" name="amount_priceset[%set_row%][from]" value="" /> <?= _l("Max Price"); ?> <input type="text" name="amount_priceset[%set_row%][to]" value="" /></span></td>';
		html += '		<td class="left"><input type="text" name="amount_priceset[%set_row%][cost]" value="" /></td>';
		html += '		<td class="left">' + "<?= $this->builder->build('select',$priceset_types,"amount_priceset[%set_row%][type]"); ?>" + '</td>';
		html += '		<td class="left"><a onclick="$(\'#set-row%set_row%\').remove();" class="button"><?= _l("Remove"); ?></a></td>';
		html += '	</tr>';
		html += '</tbody>';

		$('#pricesets').append(html.replace(/%set_row%/g, set_row));
		set_row++;
	}

	function range_values(context) {
		pricetotal = context.closest('tbody').find('.pricetotal');
		if (context.val() == 'range') {
			pricetotal.find('.pricerange').show();
			pricetotal.find('.total').hide();
		}
		else {
			pricetotal.find('.pricerange').hide();
			pricetotal.find('.total').show();
		}
	}

	var rule_row = <?= $rule_row; ?>;
	function addZoneRule() {
		html = '<tbody id="rule-row%rule_row%">';
		html += '	<tr>';
		<? $this->builder->setConfig('country_id','name');?>
		html += '		<td class="left">' + "<?= $this->builder->build('select',$countries,"amount_zonerule[%rule_row%][country_id]",'',array('class'=>'country_select'),true); ?>"
			+ '<select id="zone_id-%rule_row%" name="amount_zonerule[%rule_row%][zone_id]" class="zone_select"></select></td>';
		html += '		<td class="left">' + "<?= $this->builder->build('select',$rule_mods,"amount_zonerule[%rule_row%][mod]"); ?>" + '</td>';
		html += '		<td class="left"><input type="text" name="amount_zonerule[%rule_row%][cost]" value="" /></td>';
		html += '		<td class="left">' + "<?= $this->builder->build('select',$priceset_types,"amount_zonerule[%rule_row%][type]"); ?>" + '</td>';
		html += '		<td class="left"><a onclick="$(\'#rule-row%rule_row%\').remove();" class="button"><?= _l("Remove"); ?> </a></td>';
		html += '	</tr>';
		html += '</tbody>';

		zr = $(html.replace(/%rule_row%/g, rule_row));
		$('#zonerules').append(zr);
		zr.find('.country_select').trigger('change');
		rule_row++;
	}
</script>
