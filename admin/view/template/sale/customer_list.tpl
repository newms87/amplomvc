<?= $header; ?>
<div class="content">
  <?= $this->builder->display_breadcrumbs();?>
  <? if ($error_warning) { ?>
  <div class="message_box warning"><?= $error_warning; ?></div>
  <? } ?>
  <? if ($success) { ?>
  <div class="message_box success"><?= $success; ?></div>
  <? } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/customer.png" alt="" /> <?= $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('form').attr('action', '<?= $approve; ?>'); $('form').submit();" class="button"><?= $button_approve; ?></a><a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a><a onclick="$('form').attr('action', '<?= $delete; ?>'); $('form').submit();" class="button"><?= $button_delete; ?></a></div>
    </div>
    <div class="content">
      <form action="" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="left"><? if ($sort == 'name') { ?>
                <a href="<?= $sort_name; ?>" class="<?= strtolower($order); ?>"><?= $column_name; ?></a>
                <? } else { ?>
                <a href="<?= $sort_name; ?>"><?= $column_name; ?></a>
                <? } ?></td>
              <td class="left"><? if ($sort == 'c.email') { ?>
                <a href="<?= $sort_email; ?>" class="<?= strtolower($order); ?>"><?= $column_email; ?></a>
                <? } else { ?>
                <a href="<?= $sort_email; ?>"><?= $column_email; ?></a>
                <? } ?></td>
              <td class="left"><? if ($sort == 'customer_group') { ?>
                <a href="<?= $sort_customer_group; ?>" class="<?= strtolower($order); ?>"><?= $column_customer_group; ?></a>
                <? } else { ?>
                <a href="<?= $sort_customer_group; ?>"><?= $column_customer_group; ?></a>
                <? } ?></td>
              <td class="left"><? if ($sort == 'c.status') { ?>
                <a href="<?= $sort_status; ?>" class="<?= strtolower($order); ?>"><?= $column_status; ?></a>
                <? } else { ?>
                <a href="<?= $sort_status; ?>"><?= $column_status; ?></a>
                <? } ?></td>
              <td class="left"><? if ($sort == 'c.approved') { ?>
                <a href="<?= $sort_approved; ?>" class="<?= strtolower($order); ?>"><?= $column_approved; ?></a>
                <? } else { ?>
                <a href="<?= $sort_approved; ?>"><?= $column_approved; ?></a>
                <? } ?></td>
              <td class="left"><? if ($sort == 'c.ip') { ?>
                <a href="<?= $sort_ip; ?>" class="<?= strtolower($order); ?>"><?= $column_ip; ?></a>
                <? } else { ?>
                <a href="<?= $sort_ip; ?>"><?= $column_ip; ?></a>
                <? } ?></td>
              <td class="left"><? if ($sort == 'c.date_added') { ?>
                <a href="<?= $sort_date_added; ?>" class="<?= strtolower($order); ?>"><?= $column_date_added; ?></a>
                <? } else { ?>
                <a href="<?= $sort_date_added; ?>"><?= $column_date_added; ?></a>
                <? } ?></td>
              <td class="left"><?= $column_login; ?></td>
              <td class="right"><?= $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <tr class="filter">
              <td></td>
              <td><input type="text" name="filter_name" value="<?= $filter_name; ?>" /></td>
              <td><input type="text" name="filter_email" value="<?= $filter_email; ?>" /></td>
              <td><select name="filter_customer_group_id">
                  <option value="*"></option>
                  <? foreach ($customer_groups as $customer_group) { ?>
                  <? if ($customer_group['customer_group_id'] == $filter_customer_group_id) { ?>
                  <option value="<?= $customer_group['customer_group_id']; ?>" selected="selected"><?= $customer_group['name']; ?></option>
                  <? } else { ?>
                  <option value="<?= $customer_group['customer_group_id']; ?>"><?= $customer_group['name']; ?></option>
                  <? } ?>
                  <? } ?>
                </select></td>
              <td><select name="filter_status">
                  <option value="*"></option>
                  <? if ($filter_status) { ?>
                  <option value="1" selected="selected"><?= $text_enabled; ?></option>
                  <? } else { ?>
                  <option value="1"><?= $text_enabled; ?></option>
                  <? } ?>
                  <? if (!is_null($filter_status) && !$filter_status) { ?>
                  <option value="0" selected="selected"><?= $text_disabled; ?></option>
                  <? } else { ?>
                  <option value="0"><?= $text_disabled; ?></option>
                  <? } ?>
                </select></td>
              <td><select name="filter_approved">
                  <option value="*"></option>
                  <? if ($filter_approved) { ?>
                  <option value="1" selected="selected"><?= $text_yes; ?></option>
                  <? } else { ?>
                  <option value="1"><?= $text_yes; ?></option>
                  <? } ?>
                  <? if (!is_null($filter_approved) && !$filter_approved) { ?>
                  <option value="0" selected="selected"><?= $text_no; ?></option>
                  <? } else { ?>
                  <option value="0"><?= $text_no; ?></option>
                  <? } ?>
                </select></td>
              <td><input type="text" name="filter_ip" value="<?= $filter_ip; ?>" /></td>
              <td><input type="text" name="filter_date_added" value="<?= $filter_date_added; ?>" size="12" id="date" /></td>
              <td></td>
              <td align="right"><a onclick="filter();" class="button"><?= $button_filter; ?></a></td>
            </tr>
            <? if ($customers) { ?>
            <? foreach ($customers as $customer) { ?>
            <tr>
              <td style="text-align: center;"><? if ($customer['selected']) { ?>
                <input type="checkbox" name="selected[]" value="<?= $customer['customer_id']; ?>" checked="checked" />
                <? } else { ?>
                <input type="checkbox" name="selected[]" value="<?= $customer['customer_id']; ?>" />
                <? } ?></td>
              <td class="left"><?= $customer['name']; ?></td>
              <td class="left"><?= $customer['email']; ?></td>
              <td class="left"><?= $customer['customer_group']; ?></td>
              <td class="left"><?= $customer['status']; ?></td>
              <td class="left"><?= $customer['approved']; ?></td>
              <td class="left"><?= $customer['ip']; ?></td>
              <td class="left"><?= $customer['date_added']; ?></td>
              <td class="left">
					<? $this->builder->set_builder_config('store_id', 'name');?>
					<?= $this->builder->build('select', $data_stores, "store_login", '', array('onchange' => "if(this.value !== '')window.open('index.php?route=sale/customer/login&customer_id=$customer[customer_id]&store_id=' + this.value);"));?>
				  </td>
              <td class="right"><? foreach ($customer['action'] as $action) { ?>
                [ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
                <? } ?></td>
            </tr>
            <? } ?>
            <? } else { ?>
            <tr>
              <td class="center" colspan="10"><?= $text_no_results; ?></td>
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
	url = 'index.php?route=sale/customer';
	
	var filter_name = $('input[name=\'filter_name\']').attr('value');
	
	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}
	
	var filter_email = $('input[name=\'filter_email\']').attr('value');
	
	if (filter_email) {
		url += '&filter_email=' + encodeURIComponent(filter_email);
	}
	
	var filter_customer_group_id = $('select[name=\'filter_customer_group_id\']').attr('value');
	
	if (filter_customer_group_id != '*') {
		url += '&filter_customer_group_id=' + encodeURIComponent(filter_customer_group_id);
	}	
	
	var filter_status = $('select[name=\'filter_status\']').attr('value');
	
	if (filter_status != '*') {
		url += '&filter_status=' + encodeURIComponent(filter_status); 
	}	
	
	var filter_approved = $('select[name=\'filter_approved\']').attr('value');
	
	if (filter_approved != '*') {
		url += '&filter_approved=' + encodeURIComponent(filter_approved);
	}	
	
	var filter_ip = $('input[name=\'filter_ip\']').attr('value');
	
	if (filter_ip) {
		url += '&filter_ip=' + encodeURIComponent(filter_ip);
	}
		
	var filter_date_added = $('input[name=\'filter_date_added\']').attr('value');
	
	if (filter_date_added) {
		url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
	}
	
	location = url;
}
//--></script>
<script type="text/javascript"><!--
$(document).ready(function() {
	$('#date').datepicker({dateFormat: 'yy-mm-dd'});
});
//--></script>
<?= $footer; ?> 