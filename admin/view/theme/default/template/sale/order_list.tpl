<?= $header; ?>
<div class="content">
  <?= $this->builder->display_breadcrumbs();?>
  <div class="box">
    <div class="heading">
      <h1><img src="<?= HTTP_THEME_IMAGE . 'order.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').attr('action', '<?= $invoice; ?>'); $('#form').attr('target', '_blank'); $('#form').submit();" class="button"><?= $button_invoice; ?></a><a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a><a onclick="$('#form').attr('action', '<?= $delete; ?>'); $('#form').attr('target', '_self'); $('#form').submit();" class="button"><?= $button_delete; ?></a></div>
    </div>
    <div class="content">
      <form action="" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="right"><? if ($sort == 'o.order_id') { ?>
                <a href="<?= $sort_order; ?>" class="<?= strtolower($order); ?>"><?= $column_order_id; ?></a>
                <? } else { ?>
                <a href="<?= $sort_order; ?>"><?= $column_order_id; ?></a>
                <? } ?></td>
              <td class="left"><? if ($sort == 'customer') { ?>
                <a href="<?= $sort_customer; ?>" class="<?= strtolower($order); ?>"><?= $column_customer; ?></a>
                <? } else { ?>
                <a href="<?= $sort_customer; ?>"><?= $column_customer; ?></a>
                <? } ?></td>
              <td class="left"><? if ($sort == 'status') { ?>
                <a href="<?= $sort_status; ?>" class="<?= strtolower($order); ?>"><?= $column_status; ?></a>
                <? } else { ?>
                <a href="<?= $sort_status; ?>"><?= $column_status; ?></a>
                <? } ?></td>
              <td class="right"><? if ($sort == 'o.total') { ?>
                <a href="<?= $sort_total; ?>" class="<?= strtolower($order); ?>"><?= $column_total; ?></a>
                <? } else { ?>
                <a href="<?= $sort_total; ?>"><?= $column_total; ?></a>
                <? } ?></td>
              <td class="left"><? if ($sort == 'o.date_added') { ?>
                <a href="<?= $sort_date_added; ?>" class="<?= strtolower($order); ?>"><?= $column_date_added; ?></a>
                <? } else { ?>
                <a href="<?= $sort_date_added; ?>"><?= $column_date_added; ?></a>
                <? } ?></td>
              <td class="left"><? if ($sort == 'o.date_modified') { ?>
                <a href="<?= $sort_date_modified; ?>" class="<?= strtolower($order); ?>"><?= $column_date_modified; ?></a>
                <? } else { ?>
                <a href="<?= $sort_date_modified; ?>"><?= $column_date_modified; ?></a>
                <? } ?></td>
              <td class="right"><?= $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <tr class="filter">
              <td></td>
              <td align="right"><input type="text" name="filter_order_id" value="<?= $filter_order_id; ?>" size="4" style="text-align: right;" /></td>
              <td><input type="text" name="filter_customer" value="<?= $filter_customer; ?>" /></td>
              <td><select name="filter_order_status_id">
                  <option value="*"></option>
                  <? if ($filter_order_status_id == '0') { ?>
                  <option value="0" selected="selected"><?= $text_missing; ?></option>
                  <? } else { ?>
                  <option value="0"><?= $text_missing; ?></option>
                  <? } ?>
                  <? foreach ($order_statuses as $order_status) { ?>
                  <? if ($order_status['order_status_id'] == $filter_order_status_id) { ?>
                  <option value="<?= $order_status['order_status_id']; ?>" selected="selected"><?= $order_status['name']; ?></option>
                  <? } else { ?>
                  <option value="<?= $order_status['order_status_id']; ?>"><?= $order_status['name']; ?></option>
                  <? } ?>
                  <? } ?>
                </select></td>
              <td align="right"><input type="text" name="filter_total" value="<?= $filter_total; ?>" size="4" style="text-align: right;" /></td>
              <td><input type="text" name="filter_date_added" value="<?= $filter_date_added; ?>" size="12" class="date" /></td>
              <td><input type="text" name="filter_date_modified" value="<?= $filter_date_modified; ?>" size="12" class="date" /></td>
              <td align="right"><a onclick="filter();" class="button"><?= $button_filter; ?></a></td>
            </tr>
            <? if ($orders) { ?>
            <? foreach ($orders as $order) { ?>
            <tr>
              <td style="text-align: center;"><? if ($order['selected']) { ?>
                <input type="checkbox" name="selected[]" value="<?= $order['order_id']; ?>" checked="checked" />
                <? } else { ?>
                <input type="checkbox" name="selected[]" value="<?= $order['order_id']; ?>" />
                <? } ?></td>
              <td class="right"><?= $order['order_id']; ?></td>
              <td class="left"><?= $order['customer']; ?></td>
              <td class="left"><?= $order['status']; ?></td>
              <td class="right"><?= $order['total']; ?></td>
              <td class="left"><?= $order['date_added']; ?></td>
              <td class="left"><?= $order['date_modified']; ?></td>
              <td class="right"><? foreach ($order['action'] as $action) { ?>
                [ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
                <? } ?></td>
            </tr>
            <? } ?>
            <? } else { ?>
            <tr>
              <td class="center" colspan="8"><?= $text_no_results; ?></td>
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
	url = 'index.php?route=sale/order';
	
	var filter_order_id = $('input[name=\'filter_order_id\']').attr('value');
	
	if (filter_order_id) {
		url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
	}
	
	var filter_customer = $('input[name=\'filter_customer\']').attr('value');
	
	if (filter_customer) {
		url += '&filter_customer=' + encodeURIComponent(filter_customer);
	}
	
	var filter_order_status_id = $('select[name=\'filter_order_status_id\']').attr('value');
	
	if (filter_order_status_id != '*') {
		url += '&filter_order_status_id=' + encodeURIComponent(filter_order_status_id);
	}	

	var filter_total = $('input[name=\'filter_total\']').attr('value');

	if (filter_total) {
		url += '&filter_total=' + encodeURIComponent(filter_total);
	}	
	
	var filter_date_added = $('input[name=\'filter_date_added\']').attr('value');
	
	if (filter_date_added) {
		url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
	}
	
	var filter_date_modified = $('input[name=\'filter_date_modified\']').attr('value');
	
	if (filter_date_modified) {
		url += '&filter_date_modified=' + encodeURIComponent(filter_date_modified);
	}
				
	location = url;
}
//--></script>  
<script type="text/javascript"><!--
$(document).ready(function() {
	$('.date').datepicker({dateFormat: 'yy-mm-dd'});
});
//--></script> 
<script type="text/javascript"><!--
$('#form input').keydown(function(e) {
	if (e.keyCode == 13) {
		filter();
	}
});
//--></script> 
<script type="text/javascript"><!--
$.widget('custom.catcomplete', $.ui.autocomplete, {
	_renderMenu: function(ul, items) {
		var self = this, currentCategory = '';
		
		$.each(items, function(index, item) {
			if (item.category != currentCategory) {
				ul.append('<li class="ui-autocomplete-category">' + item.category + '</li>');
				
				currentCategory = item.category;
			}
			
			self._renderItem(ul, item);
		});
	}
});

$('input[name=\'filter_customer\']').catcomplete({
	delay: 0,
	source: function(request, response) {
		$.ajax({
			url: 'index.php?route=sale/customer/autocomplete&filter_name=' +  encodeURIComponent(request.term),
			dataType: 'json',
			success: function(json) {		
				response($.map(json, function(item) {
					return {
						category: item.customer_group,
						label: item.name,
						value: item.customer_id
					}
				}));
			}
		});
	}, 
	select: function(event, ui) {
		$('input[name=\'filter_customer\']').val(ui.item.label);
						
		return false;
	}
});
//--></script> 
<?= $footer; ?>