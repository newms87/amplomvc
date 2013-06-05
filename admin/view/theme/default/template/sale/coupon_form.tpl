<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs(); ?>
	<?= $this->builder->display_errors($errors); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'customer.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<div id="tabs" class="htabs"><a href="#tab-general"><?= $tab_general; ?></a>
				<? if ($coupon_id) { ?>
				<a href="#tab-history"><?= $tab_coupon_history; ?></a>
				<? } ?>
			</div>
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<div id="tab-general">
					<table class="form">
						<tr>
							<td class="required"> <?= $entry_name; ?></td>
							<td><input name="name" value="<?= $name; ?>" /></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_code; ?></td>
							<td><input type="text" name="code" value="<?= $code; ?>" /></td>
						</tr>
						<tr>
							<td><?= $entry_type; ?></td>
							<td><?= $this->builder->build('select',$data_types, 'type', $type); ?></td>
						</tr>
						<tr>
							<td><?= $entry_discount; ?></td>
							<td><input type="text" name="discount" value="<?= $discount; ?>" /></td>
						</tr>
						<tr>
							<td><?= $entry_total; ?></td>
							<td><input type="text" name="total" value="<?= $total; ?>" /></td>
						</tr>
						<tr>
							<td><?= $entry_logged; ?></td>
							<td><?= $this->builder->build('radio',$yes_no,'logged',(int)$logged); ?></td>
						</tr>
						<tr>
							<td><?= $entry_shipping; ?></td>
							<td>
								<div><?= $this->builder->build('radio',$yes_no,'shipping',(int)$shipping, array('onclick'=>"if($(this).find(':checked').val() == '1')$('#coupon_ship_geozone').show(); else $('#coupon_ship_geozone').hide();")); ?></div>
								<div <?= (int)$shipping ? '' : "style='display:none'"; ?> id='coupon_ship_geozone'>
										<? $this->builder->set_config('geo_zone_id', 'name');?>
										<?= $this->builder->build('select', $data_geo_zones, 'shipping_geozone', (int)$shipping_geozone); ?>
								</div>
							</td>
						</tr>
						<tr>
							<td><?= $entry_coupon_customer; ?></td>
							<td>
									<? foreach($coupon_customers as $cc){?>
										<div><?= $cc['lastname'] . ', ' . $cc['firstname'] . ' - ' . $cc['email']; ?></div>
										<input type='hidden' name='coupon_customers[]' value='<?= $cc['customer_id']; ?>' />
									<? }?>
							</td>
						</tr>
						<tr>
							<td><?= $entry_category; ?></td>
							<td><div class="scrollbox">
									<? $class = 'odd'; ?>
									<? foreach ($categories as $category) { ?>
									<? $class = ($class == 'even' ? 'odd' : 'even'); ?>
									<div class="<?= $class; ?>">
										<input type="checkbox" name="categories[]" value="<?= $category['category_id']; ?>" />
										<?= $category['name']; ?> </div>
									<? } ?>
								</div></td>
						</tr>
						<tr>
							<td><?= $entry_product; ?></td>
							<td><input type="text" name="product" value="" /></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<div id="coupon-product" class="scrollbox">
										<? foreach ($coupon_products as $coupon_product) { ?>
										<div id="coupon-product<?= $coupon_product['product_id']; ?>">
											<?= $coupon_product['name']; ?>
											<img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" />
											<input type="hidden" name="coupon_products[]" value="<?= $coupon_product['product_id']; ?>" />
										</div>
										<? } ?>
								</div>
							</td>
						</tr>
						<tr>
							<td><?= $entry_date_start; ?></td>
							<td><input type="text" name="date_start" class='datetime' value="<?= $date_start; ?>" size="12" id="date-start" /></td>
						</tr>
						<tr>
							<td><?= $entry_date_end; ?></td>
							<td><input type="text" name="date_end" class='datetime' value="<?= $date_end; ?>" size="12" id="date-end" /></td>
						</tr>
						<tr>
							<td><?= $entry_uses_total; ?></td>
							<td><input type="text" name="uses_total" value="<?= $uses_total; ?>" /></td>
						</tr>
						<tr>
							<td><?= $entry_uses_customer; ?></td>
							<td><input type="text" name="uses_customer" value="<?= $uses_customer; ?>" /></td>
						</tr>
						<tr>
							<td><?= $entry_status; ?></td>
							<td><?= $this->builder->build('select',$statuses,'status',$status); ?></td>
						</tr>
					</table>
				</div>
				<? if ($coupon_id) { ?>
				<div id="tab-history">
					<div id="history"></div>
				</div>
				<? } ?>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript"><!--
$('input[name=\'category[]\']').bind('change', function() {
	var filter_category_id = this;
	
	$.ajax({
		url: "<?= HTTP_ADMIN . "index.php?route=catalog/product/autocomplete"; ?>" + '&filter_category_id=' +	filter_category_id.value + '&limit=10000',
		dataType: 'json',
		success: function(json) {
			for (i = 0; i < json.length; i++) {
				if ($(filter_category_id).attr('checked') == 'checked') {
					$('#coupon-product' + json[i]['product_id']).remove();
					
					$('#coupon-product').append('<div id="coupon-product' + json[i]['product_id'] + '">' + json[i]['name'] + '<img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" /><input type="hidden" name="coupon_product[]" value="' + json[i]['product_id'] + '" /></div>');
				} else {
					$('#coupon-product' + json[i]['product_id']).remove();
				}
			}
			
			$('#coupon-product div:odd').attr('class', 'odd');
			$('#coupon-product div:even').attr('class', 'even');
		}
	});
});

$('input[name=\'product\']').autocomplete({
	delay: 0,
	source: function(request, response) {
		$.ajax({
			url: "<?= HTTP_ADMIN . "index.php?route=catalog/product/autocomplete"; ?>" + '&filter_name=' +	encodeURIComponent(request.term),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item.name,
						value: item.product_id
					}
				}));
			}
		});
	},
	select: function(event, ui) {
		$('#coupon-product' + ui.item.value).remove();
		
		$('#coupon-product').append('<div id="coupon-product' + ui.item.value + '">' + ui.item.label + '<img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" /><input type="hidden" name="coupon_product[]" value="' + ui.item.value + '" /></div>');

		$('#coupon-product div:odd').attr('class', 'odd');
		$('#coupon-product div:even').attr('class', 'even');
		
		$('input[name=\'product\']').val('');
		
		return false;
	}
});

$('#coupon-product div img').live('click', function() {
	$(this).parent().remove();
	
	$('#coupon-product div:odd').attr('class', 'odd');
	$('#coupon-product div:even').attr('class', 'even');
});
//--></script>

<?= $this->builder->js('datepicker'); ?>

<? if ($coupon_id) { ?>
<script type="text/javascript"><!--
$('#history .pagination a').live('click', function() {
	$('#history').load(this.href);
	
	return false;
});

$('#history').load("<?= HTTP_ADMIN . "index.php?route=sale/coupon/history"; ?>" + '&coupon_id=<?= $coupon_id; ?>');
//--></script>
<? } ?>
<script type="text/javascript"><!--
$('#tabs a').tabs();
//--></script>

<?= $this->builder->js('errors', $errors); ?>
<?= $footer; ?>