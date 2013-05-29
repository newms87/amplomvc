<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'product.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a><a onclick="$('#form').attr('action', '<?= $copy; ?>'); $('#form').submit();" class="button"><?= $button_copy; ?></a><a onclick="$('form').submit();" class="button"><?= $button_delete; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="list">
					<thead>
						<tr>
							<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
							<td class="right"><?= $column_action; ?></td>
							<td class="center"><?= $column_image; ?></td>
							<td class="left"><? if ($sort == 'pd.name') { ?>
								<a href="<?= $sort_name; ?>" class="<?= strtolower($order); ?>"><?= $column_name; ?></a>
								<? } else { ?>
								<a href="<?= $sort_name; ?>"><?= $column_name; ?></a>
								<? } ?></td>
							<!--<td class="left"><? if ($sort == 'p.model') { ?>
								<a href="<?= $sort_model; ?>" class="<?= strtolower($order); ?>"><?= $column_model; ?></a>
								<? } else { ?>
								<a href="<?= $sort_model; ?>"><?= $column_model; ?></a>
								<? } ?></td>-->
							<td class="left"><? if ($sort == 'p.price') { ?>
								<a href="<?= $sort_price; ?>" class="<?= strtolower($order); ?>"><?= $column_price; ?></a>
								<? } else { ?>
								<a href="<?= $sort_price; ?>"><?= $column_price; ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'p.is_final') { ?>
								<a href="<?= $sort_is_final; ?>" class="<?= strtolower($order); ?>"><?= $column_is_final; ?></a>
								<? } else { ?>
								<a href="<?= $sort_is_final; ?>"><?= $column_is_final; ?></a>
								<? } ?></td>
							<td class="right"><? if ($sort == 'p.quantity') { ?>
								<a href="<?= $sort_quantity; ?>" class="<?= strtolower($order); ?>"><?= $column_quantity; ?></a>
								<? } else { ?>
								<a href="<?= $sort_quantity; ?>"><?= $column_quantity; ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'p.status') { ?>
								<a href="<?= $sort_status; ?>" class="<?= strtolower($order); ?>"><?= $column_status; ?></a>
								<? } else { ?>
								<a href="<?= $sort_status; ?>"><?= $column_status; ?></a>
								<? } ?></td>
							<td class="right"><?= $column_action; ?></td>
						</tr>
					</thead>
					<tbody>
						<tr class="filter">
							<td></td>
							<td align="right"><a onclick="filter();" class="button"><?= $button_filter; ?></a></td>
							<td></td>
							<td><input type="text" name="filter_name" value="<?= $filter_name; ?>" /></td>
							<!--<td><input type="text" name="filter_model" value="<?= $filter_model; ?>" /></td>-->
							<td align="left"><input type="text" name="filter_price" value="<?= $filter_price; ?>" size="8"/></td>
							<td align="left"><?= $this->builder->build('select',$data_yes_no_blank,'filter_is_final',is_null($filter_is_final)?'':(int)$filter_is_final); ?></td>
							<td align="right"><input type="text" name="filter_quantity" value="<?= $filter_quantity; ?>" style="text-align: right;" /></td>
							<td><?= $this->builder->build('select', $data_statuses_blank, "filter_status", $filter_status); ?></td>
							<td align="right"><a onclick="filter();" class="button"><?= $button_filter; ?></a></td>
						</tr>
						<? if ($products) { ?>
						<? foreach ($products as $product) { ?>
						<tr>
							<td style="text-align: center;"><? if ($product['selected']) { ?>
								<input type="checkbox" name="selected[]" value="<?= $product['product_id']; ?>" checked="checked" />
								<? } else { ?>
								<input type="checkbox" name="selected[]" value="<?= $product['product_id']; ?>" />
								<? } ?></td>
							<td class="right"><? foreach ($product['action'] as $action) { ?>
								[ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
								<? } ?></td>
							<td class="center"><img src="<?= $product['image']; ?>" alt="<?= $product['name']; ?>" style="padding: 1px; border: 1px solid #DDDDDD;" /></td>
							<td class="left"><?= $product['name']; ?></td>
							<!--<td class="left"><?= $product['model']; ?></td>-->
							<td class="left"><? if ($product['special']) { ?>
								<span style="text-decoration: line-through;"><?= $product['price']; ?></span><br/>
								<span style="color: #b00;"><?= $product['special']; ?></span>
								<? } else { ?>
								<?= $product['price']; ?>
								<? } ?></td>
							<td class="left"><?= $product['is_final']?'Yes':'No'; ?></td>
							<td class="right"><? if ($product['quantity'] <= 0) { ?>
								<span style="color: #FF0000;"><?= $product['quantity']; ?></span>
								<? } elseif ($product['quantity'] <= 5) { ?>
								<span style="color: #FFA500;"><?= $product['quantity']; ?></span>
								<? } else { ?>
								<span style="color: #008000;"><?= $product['quantity']; ?></span>
								<? } ?></td>
							<td class="left"><?= $product['status']; ?></td>
							<td class="right"><? foreach ($product['action'] as $action) { ?>
								[ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
								<? } ?></td>
						</tr>
						<? } ?>
						<? } else { ?>
						<tr>
							<td class="center" colspan="13"><?= $text_no_results; ?></td>
						</tr>
						<? } ?>
					</tbody>
				</table>
			</form>
			<div class="pagination"><?= $pagination; ?></div>
		</div>
	</div>
</div>
<script type="text/javascript">//<!--
function filter() {
	url = "<?= HTTP_ADMIN . "index.php?route=catalog/product"; ?>";
	
	var filters = ['filter_name','filter_model','filter_price','filter_cost','filter_is_final',
								'filter_manufacturer_id','filter_category_id','filter_date_expires','filter_quantity','filter_status'];
	for(var i=0;i<filters.length;i++){
			val = $('[name=\''+filters[i]+'\']').val();
			if(val)
				url += '&'+filters[i]+'=' + encodeURIComponent(val);
	}
	
	location = url;
}
//--></script>
<script type="text/javascript">//<!--
$('#form input').keydown(function(e) {
	if (e.keyCode == 13) {
		filter();
	}
});
//--></script>
<script type="text/javascript">//<!--
$('input[name=\'filter_name\']').autocomplete({
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
		$('input[name=\'filter_name\']').val(ui.item.label);
						
		return false;
	}
});

$('input[name=\'filter_model\']').autocomplete({
	delay: 0,
	source: function(request, response) {
		$.ajax({
			url: "<?= HTTP_ADMIN . "index.php?route=catalog/product/autocomplete"; ?>" + '&filter_model=' +	encodeURIComponent(request.term),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item.model,
						value: item.product_id
					}
				}));
			}
		});
	},
	select: function(event, ui) {
		$('input[name=\'filter_model\']').val(ui.item.label);
						
		return false;
	}
});
//--></script>
<?= $this->builder->js('datepicker'); ?>
<script type='text/javascript'>//<!--
$('#update_action').change(function(){
	$('.action_value').removeClass('active');
	$('#for-' + $(this).val()).addClass('active');
})
--></script>
<?= $footer; ?>