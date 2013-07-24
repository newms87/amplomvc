<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'shipping.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td><?= $entry_tax_class; ?></td>
						<td>
							<? $this->builder->set_config('tax_class_id','title');?>
							<?= $this->builder->build('select',$tax_classes, 'amount_tax_class_id',$amount_tax_class_id); ?>
						</td>
					</tr>
					<tr>
						<td><?= $entry_geo_zone; ?></td>
						<td>
							<? $this->builder->set_config('geo_zone_id','name');?>
							<?= $this->builder->build('select',$geo_zones, 'amount_geo_zone_id',$amount_geo_zone_id); ?>
						</td>
					</tr>
					<tr>
						<td><?= $entry_status; ?></td>
						<td><?= $this->builder->build('select',$statuses,'amount_status',(int)$amount_status); ?></td>
					</tr>
					<tr>
						<td><?= $entry_sort_order; ?></td>
						<td><input type="text" name="amount_sort_order" value="<?= $amount_sort_order; ?>" size="1" /></td>
					</tr>
					<tr>
						<td><?= $entry_priceset; ?></td>
						<td>
							<table id="pricesets" class="list">
								<thead>
									<tr>
										<td class="left"><?= $entry_priceset_range; ?></td>
										<td class="left"><?= $entry_priceset_total; ?></td>
										<td class="left"><?= $entry_priceset_cost; ?></td>
										<td class="left"><?= $entry_priceset_type; ?></td>
										<td></td>
									</tr>
								</thead>
							<? $set_row = 0;?>
							<? foreach($amount_priceset as $set){?>
									<tbody id="set-row<?= $set_row; ?>">
										<tr>
												<td class="left"><?= $this->builder->build('select',$priceset_ranges,"amount_priceset[$set_row][range]",$set['range'], array('onclick'=>'range_values($(this))')); ?></td>
												<td class="left pricetotal">
													<span class='total' <?= $set['range']=='range'?"style='display:none'":''; ?>><input type="text" name="amount_priceset[<?= $set_row; ?>][total]" value="<?= $set['total']; ?>" /></span>
													<span class='pricerange' <?= $set['range']!='range'?"style='display:none'":''; ?>>
															<?= $entry_priceset_min; ?><input type="text" name="amount_priceset[<?= $set_row; ?>][from]" value="<?= $set['from']; ?>" />
															<?= $entry_priceset_max; ?><input type="text" name="amount_priceset[<?= $set_row; ?>][to]" value="<?= $set['to']; ?>" />
													</span>
												</td>
												<td class="left"><input type="text" name="amount_priceset[<?= $set_row; ?>][cost]" value="<?= $set['cost']; ?>" /></td>
												<td class="left"><?= $this->builder->build('select',$priceset_types,"amount_priceset[$set_row][type]",$set['type']); ?></td>
												<td class="left"><a onclick="$('#set-row<?= $set_row; ?>').remove();" class="button"><?= $button_remove; ?></a></td>
										</tr>
									</tbody>
									<? $set_row++;?>
							<? }?>
									<tfoot>
									<tr>
										<td colspan="6"></td>
										<td class="left"><a onclick="addPriceSet();" class="button"><?= $button_add_priceset; ?></a></td>
									</tr>
									</tfoot>
							</table>
						</td>
					</tr>
					<tr>
						<td><?= $entry_zonerule; ?></td>
						<td>
							<table id="zonerules" class="list">
								<thead>
									<tr>
										<td class="left"><?= $entry_zonerule_zone; ?></td>
										<td class="left"><?= $entry_zonerule_mod; ?></td>
										<td class="left"><?= $entry_zonerule_cost; ?></td>
										<td class="left"><?= $entry_zonerule_type; ?></td>
										<td></td>
									</tr>
								</thead>
							<? $rule_row = 0;?>
							<? foreach($amount_zonerule as $rule){?>
									<tbody id="rule-row<?= $rule_row; ?>">
										<tr>
												<td class="left">
													<? $this->builder->set_config('country_id','name');?>
													<?= $this->builder->build('select',$countries, "amount_zonerule[$rule_row][country_id]", $rule['country_id'], array('class'=>'country_select')); ?>
													<select id="zone_id-<?= $rule_row; ?>" name="amount_zonerule[<?= $rule_row; ?>][zone_id]" class="zone_select" zone_id="<?= $rule['zone_id']; ?>"></select>
												</td>
												<td class="left"><?= $this->builder->build('select',$rule_mods,"amount_zonerule[$rule_row][mod]",$rule['mod'],array('id'=>"zone_id-$rule_row")); ?></td>
												<td class="left"><input type="text" name="amount_zonerule[<?= $rule_row; ?>][cost]" value="<?= $rule['cost']; ?>" /></td>
												<td class="left"><?= $this->builder->build('select',$priceset_types,"amount_zonerule[$rule_row][type]",$rule['type']); ?></td>
												<td class="left"><a onclick="$('#rule-row<?= $rule_row; ?>').remove();" class="button"><?= $button_remove; ?></a></td>
										</tr>
									</tbody>
									<? $rule_row++;?>
							<? }?>
									<tfoot>
									<tr>
										<td colspan="6"></td>
										<td class="left"><a onclick="addZoneRule();" class="button"><?= $button_add_zonerule; ?></a></td>
									</tr>
									</tfoot>
							</table>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>

<?= $this->builder->js('load_zones', 'tbody', '.country_select', '.zone_select', true); ?>

<script type='text/javascript'>//<!--
var set_row = <?= $set_row; ?>;
function addPriceSet(){
	html = '<tbody id="set-row%set_row%">';
	html+= '	<tr>';
	html+= '		<td class="left">' + "<?= $this->builder->build('select',$priceset_ranges,"amount_priceset[%set_row%][range]",'',array('onclick'=>'range_values($(this))'),true); ?>" + '</td>';
	html+= '		<td class="left pricetotal"><span class="total"><input type="text" name="amount_priceset[%set_row%][total]" value="" /></span><span class="pricerange" style="display:none;"><?= $entry_priceset_min; ?> <input type="text" name="amount_priceset[%set_row%][from]" value="" /> <?= $entry_priceset_max; ?> <input type="text" name="amount_priceset[%set_row%][to]" value="" /></span></td>';
	html+= '		<td class="left"><input type="text" name="amount_priceset[%set_row%][cost]" value="" /></td>';
	html+= '		<td class="left">' + "<?= $this->builder->build('select',$priceset_types,"amount_priceset[%set_row%][type]"); ?>" + '</td>';
	html+= '		<td class="left"><a onclick="$(\'#set-row%set_row%\').remove();" class="button"><?= $button_remove; ?></a></td>';
	html+= '	</tr>';
	html+= '</tbody>';
	
	$('#pricesets').append(html.replace(/%set_row%/g,set_row));
	set_row++;
}

function range_values(context){
	pricetotal = context.closest('tbody').find('.pricetotal');
	if(context.val() == 'range'){
			pricetotal.find('.pricerange').show();
			pricetotal.find('.total').hide();
	}
	else{
			pricetotal.find('.pricerange').hide();
			pricetotal.find('.total').show();
	}
}

var rule_row = <?= $rule_row; ?>;
function addZoneRule(){
	html = '<tbody id="rule-row%rule_row%">';
	html+= '	<tr>';
						<? $this->builder->set_config('country_id','name');?>
	html+= '		<td class="left">' + "<?= $this->builder->build('select',$countries,"amount_zonerule[%rule_row%][country_id]",'',array('class'=>'country_select'),true); ?>"
								+ '<select id="zone_id-%rule_row%" name="amount_zonerule[%rule_row%][zone_id]" class="zone_select"></select></td>';
	html+= '		<td class="left">' + "<?= $this->builder->build('select',$rule_mods,"amount_zonerule[%rule_row%][mod]"); ?>" + '</td>';
	html+= '		<td class="left"><input type="text" name="amount_zonerule[%rule_row%][cost]" value="" /></td>';
	html+= '		<td class="left">' + "<?= $this->builder->build('select',$priceset_types,"amount_zonerule[%rule_row%][type]"); ?>" + '</td>';
	html+= '		<td class="left"><a onclick="$(\'#rule-row%rule_row%\').remove();" class="button"><?= $button_remove; ?> </a></td>';
	html+= '	</tr>';
	html+= '</tbody>';
	
	zr = $(html.replace(/%rule_row%/g,rule_row));
	$('#zonerules').append(zr);
	zr.find('.country_select').trigger('change');
	rule_row++;
}
//--></script>

<?= $this->builder->js('errors',$errors); ?>

<?= $footer; ?> 