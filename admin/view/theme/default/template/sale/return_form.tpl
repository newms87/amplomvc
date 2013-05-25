<?= $header; ?>
<div class="content">
  <?= $this->builder->display_breadcrumbs();?>
  <? if ($error_warning) { ?>
  <div class="message_box warning"><?= $error_warning; ?></div>
  <? } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="<?= HTTP_THEME_IMAGE . 'customer.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <div class="htabs"><a href="#tab-return"><?= $tab_return; ?></a><a href="#tab-product"><?= $tab_product; ?></a></div>
      <form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <div id="tab-return">
          <table class="form">
            <tr>
              <td><span class="required"></span> <?= $entry_order_id; ?></td>
              <td><input type="text" name="order_id" value="<?= $order_id; ?>" />
                <? if ($error_order_id) { ?>
                <span class="error"><?= $error_order_id; ?></span>
                <? } ?></td>
            </tr>
            <tr>
              <td><?= $entry_date_ordered; ?></td>
              <td><input type="text" name="date_ordered" value="<?= $date_ordered; ?>" class="date" /></td>
            </tr>
            <tr>
              <td><?= $entry_customer; ?></td>
              <td><input type="text" name="customer" value="<?= $customer; ?>" />
                <input type="hidden" name="customer_id" value="<?= $customer_id; ?>" /></td>
            </tr>
            <tr>
              <td><span class="required"></span> <?= $entry_firstname; ?></td>
              <td><input type="text" name="firstname" value="<?= $firstname; ?>" />
                <? if ($error_firstname) { ?>
                <span class="error"><?= $error_firstname; ?></span>
                <? } ?></td>
            </tr>
            <tr>
              <td><span class="required"></span> <?= $entry_lastname; ?></td>
              <td><input type="text" name="lastname" value="<?= $lastname; ?>" />
                <? if ($error_lastname) { ?>
                <span class="error"><?= $error_lastname; ?></span>
                <? } ?></td>
            </tr>
            <tr>
              <td><span class="required"></span> <?= $entry_email; ?></td>
              <td><input type="text" name="email" value="<?= $email; ?>" />
                <? if ($error_email) { ?>
                <span class="error"><?= $error_email; ?></span>
                <?  } ?></td>
            </tr>
            <tr>
              <td><span class="required"></span> <?= $entry_telephone; ?></td>
              <td><input type="text" name="telephone" value="<?= $telephone; ?>" />
                <? if ($error_telephone) { ?>
                <span class="error"><?= $error_telephone; ?></span>
                <?  } ?></td>
            </tr>
          </table>
        </div>
        <div id="tab-product">
          <table class="form">
            <tr>
              <td><span class="required"></span> <?= $entry_product; ?></td>
              <td><input type="text" name="product" value="<?= $product; ?>" />
                <input type="hidden" name="product_id" value="<?= $product_id; ?>" />
                <? if ($error_product) { ?>
                <span class="error"><?= $error_product; ?></span>
                <?  } ?></td>
            </tr>
            <tr>
              <td><?= $entry_model; ?></td>
              <td><input type="text" name="model" value="<?= $model; ?>" /></td>
            </tr>
            <tr>
              <td><?= $entry_quantity; ?></td>
              <td><input type="text" name="quantity" value="<?= $quantity; ?>" size="3" /></td>
            </tr>
            <tr>
              <td><?= $entry_reason; ?></td>
              <td><select name="return_reason_id">
                  <? foreach ($return_reasons as $return_reason) { ?>
                  <? if ($return_reason['return_reason_id'] == $return_reason_id) { ?>
                  <option value="<?= $return_reason['return_reason_id']; ?>" selected="selected"><?= $return_reason['name']; ?></option>
                  <? } else { ?>
                  <option value="<?= $return_reason['return_reason_id']; ?>"><?= $return_reason['name']; ?></option>
                  <? } ?>
                  <? } ?>
                </select></td>
            </tr>
            <tr>
              <td><?= $entry_opened; ?></td>
              <td><select name="opened">
                  <? if ($opened) { ?>
                  <option value="1" selected="selected"><?= $text_opened; ?></option>
                  <option value="0"><?= $text_unopened; ?></option>
                  <? } else { ?>
                  <option value="1"><?= $text_opened; ?></option>
                  <option value="0" selected="selected"><?= $text_unopened; ?></option>
                  <? } ?>
                </select></td>
            </tr>
            <tr>
              <td><?= $entry_comment; ?></td>
              <td><textarea name="comment" cols="40" rows="5"><?= $comment; ?></textarea></td>
            </tr>
            <tr>
              <td><?= $entry_action; ?></td>
              <td><select name="return_action_id">
                  <option value="0"></option>
                  <? foreach ($return_actions as $return_action) { ?>
                  <? if ($return_action['return_action_id'] == $return_action_id) { ?>
                  <option value="<?= $return_action['return_action_id']; ?>" selected="selected"> <?= $return_action['name']; ?></option>
                  <? } else { ?>
                  <option value="<?= $return_action['return_action_id']; ?>"><?= $return_action['name']; ?></option>
                  <? } ?>
                  <? } ?>
                </select></td>
            </tr>
            <tr>
              <td><?= $entry_return_status; ?></td>
              <td><select name="return_status_id">
                  <? foreach ($return_statuses as $return_status) { ?>
                  <? if ($return_status['return_status_id'] == $return_status_id) { ?>
                  <option value="<?= $return_status['return_status_id']; ?>" selected="selected"><?= $return_status['name']; ?></option>
                  <? } else { ?>
                  <option value="<?= $return_status['return_status_id']; ?>"><?= $return_status['name']; ?></option>
                  <? } ?>
                  <? } ?>
                </select></td>
            </tr>
          </table>
        </div>
      </form>
    </div>
  </div>
</div>
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

$('input[name=\'customer\']').catcomplete({
	delay: 0,
	source: function(request, response) {
		$.ajax({
			url: "<?= HTTP_ADMIN . "index.php?route=sale/customer/autocomplete"; ?>" + '&filter_name=' +  encodeURIComponent(request.term),
			dataType: 'json',
			success: function(json) {	
				response($.map(json, function(item) {
					return {
						category: item.customer_group,
						label: item.name,
						value: item.customer_id,
						firstname: item.firstname,
						lastname: item.lastname,
						email: item.email,
						telephone: item.telephone
					}
				}));
			}
		});
		
	}, 
	select: function(event, ui) {
		$('input[name=\'customer\']').attr('value', ui.item.label);
		$('input[name=\'customer_id\']').attr('value', ui.item.value);
		$('input[name=\'firstname\']').attr('value', ui.item.firstname);
		$('input[name=\'lastname\']').attr('value', ui.item.lastname);
		$('input[name=\'email\']').attr('value', ui.item.email);
		$('input[name=\'telephone\']').attr('value', ui.item.telephone);

		return false;
	}
});
//--></script> 
<script type="text/javascript"><!--
$('input[name=\'product\']').autocomplete({
	delay: 0,
	source: function(request, response) {
		$.ajax({
			url: "<?= HTTP_ADMIN . "index.php?route=catalog/product/autocomplete"; ?>" + '&filter_name=' +  encodeURIComponent(request.term),
			dataType: 'json',
			success: function(json) {	
				response($.map(json, function(item) {
					return {
						label: item.name,
						value: item.product_id,
						model: item.model
					}
				}));
			}
		});
	}, 
	select: function(event, ui) {
		$('input[name=\'product_id\']').attr('value', ui.item.value);
		$('input[name=\'product\']').attr('value', ui.item.label);
		$('input[name=\'model\']').attr('value', ui.item.model);
		
		return false;
	}
});
//--></script> 
<script type="text/javascript"><!--
$(document).ready(function() {
	$('.date').datepicker({dateFormat: 'yy-mm-dd'});
});
//--></script> 
<script type="text/javascript"><!--
$('.htabs a').tabs(); 
//--></script> 
<?= $footer; ?>