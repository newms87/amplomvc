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
				<tbody id="priceset_list">
					<? foreach ($priceset as $row => $set) { ?>
						<tr class="priceset" data-row="<?= $row; ?>">
							<td class="left">
								<?= $this->builder->build('select', $data_ranges, "settings[priceset][$row][range]", $set['range'], array('onchange' => 'range_values($(this))')); ?>
							</td>
							<td class="left pricetotal">
								<span class="total" <?= $set['range'] == 'range' ? "style=\"display:none\"" : ''; ?>>
									<input type="text" name="settings[priceset][<?= $row; ?>][total]" value="<?= $set['total']; ?>"/>
								</span>
								<span class="pricerange" <?= $set['range'] != 'range' ? "style=\"display:none\"" : ''; ?>>
									<?= _l("Min Price"); ?>
									<input type="text" name="settings[priceset][<?= $row; ?>][from]" value="<?= $set['from']; ?>"/>
									<?= _l("Max Price"); ?><input type="text" name="settings[priceset][<?= $row; ?>][to]" value="<?= $set['to']; ?>"/>
								</span>
							</td>
							<td class="left"><input type="text" name="settings[priceset][<?= $row; ?>][cost]" value="<?= $set['cost']; ?>"/></td>
							<td class="left">
								<?= $this->builder->build('select', $data_types, "settings[priceset][$row][type]", $set['type']); ?>
							</td>
							<td class="left"><a onclick="$(this).closest('.priceset').remove();" class="button remove"><?= _l("X"); ?></a></td>
						</tr>
					<? } ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="4"></td>
						<td class="left"><a onclick="add_price_set()" class="button"><?= _l("Add Price Set"); ?></a>
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
				<tbody id="zonerule_list">
					<? foreach ($zonerule as $row => $rule) { ?>
						<tr class="zonerule" data-row="<?= $row; ?>">
							<td class="left">
								<? $this->builder->setConfig('country_id', 'name'); ?>
								<?= $this->builder->build('select', $data_countries, "settings[zonerule][$row][country_id]", $rule['country_id'], array('class' => 'country_select')); ?>
								<select name="settings[zonerule][<?= $row; ?>][zone_id]" class="zone_select" data-zone_id="<?= $rule['zone_id']; ?>"></select>
							</td>
							<td class="left"><?= $this->builder->build('select', $data_mods, "settings[zonerule][$row][mod]", $rule['mod']); ?></td>
							<td class="left"><input type="text" name="settings[zonerule][<?= $row; ?>][cost]" value="<?= $rule['cost']; ?>"/></td>
							<td class="left"><?= $this->builder->build('select', $data_types, "settings[zonerule][$row][type]", $rule['type']); ?></td>
							<td class="left"><a onclick="$(this).closest('.zonerule').remove();" class="button remove"><?= _l("X"); ?></a></td>
						</tr>
					<? } ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="4"></td>
						<td class="left"><a onclick="add_zone_rule();" class="button"><?= _l("Add Zone Rule"); ?></a>
						</td>
					</tr>
				</tfoot>
			</table>
		</td>
	</tr>
</table>

<?= $this->builder->js('load_zones', '.zonerule', '.country_select', '.zone_select', true); ?>

<script type="text/javascript">
	/* Flat Pricing List */
	var ps_list = $('#priceset_list');
	ps_list.ac_template('ps_list', {defaults: <?= json_encode($priceset['__ac_template__']); ?>});

	function add_price_set() {
		$.ac_template('ps_list', 'add');
	}

	ps_list.sortable({cursor: 'move'});

	function range_values(context) {
		pricetotal = context.closest('.priceset').find('.pricetotal');
		if (context.val() == 'range') {
			pricetotal.find('.pricerange').show();
			pricetotal.find('.total').hide();
		}
		else {
			pricetotal.find('.pricerange').hide();
			pricetotal.find('.total').show();
		}
	}

	/* Zone Rules */
	var zr_list = $('#zonerule_list');
	zr_list.ac_template('zr_list', {defaults: <?= json_encode($zonerule['__ac_template__']); ?>});

	zr_list.sortable({cursor: 'move'});

	function add_zone_rule() {
		var zr = $.ac_template('zr_list', 'add');
		zr.find('.country_select').trigger('change');
	}
</script>
