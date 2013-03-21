<?= $header; ?>
<div class="content">
  <?= $this->builder->display_breadcrumbs();?>
  <div class="box">
    <div class="heading">
      <div class="actions">
         <?=$this->builder->build_batch_actions($text_batch_action,$batch_actions,$batch_action_values, html_entity_decode($batch_action_go));?>
      </div>
      <h1><img src="view/image/product.png" alt="" /> <?= $heading_title; ?></h1>
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
                <a href="<?= $sort_model; ?>"><?= $column_model; ?></a>-->
                <? } ?></td>
              <td class="left"><? if ($sort == 'm.name') { ?>
                <a href="<?= $sort_manufacturer; ?>" class="<?= strtolower($order); ?>"><?= $column_manufacturer; ?></a>
                <? } else { ?>
                <a href="<?= $sort_manufacturer; ?>"><?= $column_manufacturer; ?></a>
                <? } ?></td>
              <td class="left">
                <a ><?= $column_category; ?></a>
              </td>
              <td class="left"><? if ($sort == 'p.price') { ?>
                <a href="<?= $sort_price; ?>" class="<?= strtolower($order); ?>"><?= $column_price; ?></a>
                <? } else { ?>
                <a href="<?= $sort_price; ?>"><?= $column_price; ?></a>
                <? } ?></td>
              <td class="left"><? if ($sort == 'p.cost') { ?>
                <a href="<?= $sort_cost; ?>" class="<?= strtolower($order); ?>"><?= $column_cost; ?></a>
                <? } else { ?>
                <a href="<?= $sort_cost; ?>"><?= $column_cost; ?></a>
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
              <td class="center"><? if ($sort == 'p.date_expires') { ?>
                <a href="<?= $sort_date_expires; ?>" class="<?= strtolower($order); ?>"><?= $column_date_expires; ?></a>
                <? } else { ?>
                <a href="<?= $sort_date_expires; ?>"><?= $column_date_expires; ?></a>
                <? } ?></td>
              <td class="left"><? if ($sort == 'p.editable') { ?>
                <a href="<?= $sort_editable; ?>" class="<?= strtolower($order); ?>"><?= $column_editable; ?></a>
                <? } else { ?>
                <a href="<?= $sort_editable; ?>"><?= $column_editable; ?></a>
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
              <!--<td><input type="text" name="filter_model" value="<?= $filter_model; ?>" /></td> -->
              <td><?=$this->builder->build('select',$manufacturer_list,'filter_manufacturer_id',(int)$filter_manufacturer_id);?></td>
              <td><?=$this->builder->build('select',$category_list, 'filter_category_id',(int)$filter_category_id);?></td>
              <td align="left"><input type="text" name="filter_price" value="<?= $filter_price; ?>" size="2"/></td>
              <td align="left"><input type="text" name="filter_cost" value="<?= $filter_cost; ?>" size="2"/></td>
              <td align="left"><?=$this->builder->build('select',$yes_no_blank,'filter_is_final',is_null($filter_is_final)?'':(int)$filter_is_final);?></td>
              <td align="right"><input size='3' maxlength='5' type="text" name="filter_quantity" value="<?= $filter_quantity; ?>" style="text-align: right;" /></td>
              <td align="center"><input type="text" class='datetime' size='6' name="filter_date_expires" value="<?= $filter_date_expires; ?>" style="text-align: right;" /></td>
              <td><?=$this->builder->build('select',$yes_no_blank,"filter_editable",$filter_editable);?></td>
              <td><?=$this->builder->build('select',$statuses_blank,"filter_status",$filter_status);?></td>
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
                <? } ?>
              </td>
              <td class="center"><img src="<?= $product['image']; ?>" alt="<?= $product['name']; ?>" style="padding: 1px; border: 1px solid #DDDDDD;" /></td>
              <td class="left"><?= $product['name']; ?></td>
              <!--<td class="left"><?= $product['model']; ?></td>-->
              <td class="left"><?= $manufacturer_list[$product['manufacturer_id']]; ?></td>
              <td class='left'><ul class='category_list'>
                 <? foreach($product['categories'] as $cat){?>
                    <li><?=$category_list[$cat];?></li>
                 <? }?>
                 </ul>
              </td>
              <td class="left"><? if ($product['special']) { ?>
                <span style="text-decoration: line-through;"><?= $product['price']; ?></span><br/>
                <span style="color: #b00;"><?= $product['special']; ?></span>
                <? } else { ?>
                <?= $product['price']; ?>
                <? } ?></td>
              <td class="left"><?= $product['cost']; ?></td>
              <td class="left"><?= $product['is_final']?'Yes':'No'; ?></td>
              <td class="right"><? if ($product['quantity'] <= 0) { ?>
                <span style="color: #FF0000;"><?= $product['quantity']; ?></span>
                <? } elseif ($product['quantity'] <= 5) { ?>
                <span style="color: #FFA500;"><?= $product['quantity']; ?></span>
                <? } else { ?>
                <span style="color: #008000;"><?= $product['quantity']; ?></span>
                <? } ?></td>
              <td class="center"><?= $product['date_expires']; ?></td>
              <td class="left"><?= $product['editable']; ?></td>
              <td class="left"><?= $product['status']; ?></td>
              <td class="right"><? foreach ($product['action'] as $action) { ?>
                [ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
                <? } ?>
              </td>
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
<script type="text/javascript"><!--
function filter() {
	url = 'index.php?route=catalog/product';
	
	var filters = ['filter_name','filter_model','filter_price','filter_cost','filter_is_final',
	               'filter_manufacturer_id','filter_category_id','filter_date_expires','filter_quantity','filter_editable','filter_status'];
   for(var i=0;i<filters.length;i++){
      val = $('[name=\''+filters[i]+'\']').val();
      if(val)
         url += '&'+filters[i]+'=' + encodeURIComponent(val);
   }
   
	location = url;
}
//--></script> 
<script type="text/javascript"><!--
$('#form input').keydown(function(e) {
	if (e.keyCode == 13) {
		filter();
	}
});
//--></script> 
<script type="text/javascript"><!--
$('input[name=\'filter_name\']').autocomplete({
	delay: 0,
	source: function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/product/autocomplete&filter_name=' +  encodeURIComponent(request.term),
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
			url: 'index.php?route=catalog/product/autocomplete&filter_model=' +  encodeURIComponent(request.term),
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
<?=$this->builder->js('datepicker');?>
<?=$this->builder->js('ckeditor');?>

<?= $footer; ?>