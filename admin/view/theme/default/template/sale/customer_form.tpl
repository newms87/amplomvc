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
      <div id="htabs" class="htabs"><a href="#tab-general"><?= $tab_general; ?></a>
        <? if ($customer_id) { ?>
        <a href="#tab-transaction"><?= $tab_transaction; ?></a><a href="#tab-reward"><?= $tab_reward; ?></a>
        <? } ?>
        <a href="#tab-ip"><?= $tab_ip; ?></a></div>
      <form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <div id="tab-general">
          <div id="vtabs" class="vtabs"><a href="#tab-customer"><?= $tab_general; ?></a>
            <? $address_row = 1; ?>
            <? foreach ($addresses as $address) { ?>
            <a href="#tab-address-<?= $address_row; ?>" id="address-<?= $address_row; ?>"><?= $tab_address . ' ' . $address_row; ?>&nbsp;<img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" alt="" onclick="$('#vtabs a:first').trigger('click'); $('#address-<?= $address_row; ?>').remove(); $('#tab-address-<?= $address_row; ?>').remove(); return false;" /></a>
            <? $address_row++; ?>
            <? } ?>
            <span id="address-add"><?= $button_add_address; ?>&nbsp;<img src="<?= HTTP_THEME_IMAGE . 'add.png'; ?>" alt="" onclick="addAddress();" /></span></div>
          <div id="tab-customer" class="vtabs-content">
            <table class="form">
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
              <tr>
                <td><?= $entry_fax; ?></td>
                <td><input type="text" name="fax" value="<?= $fax; ?>" /></td>
              </tr>
              <tr>
                <td><?= $entry_password; ?></td>
                <td><input type="password" autocomplete='off' name="password" value="<?= $password; ?>"  />
                  <br />
                  <? if ($error_password) { ?>
                  <span class="error"><?= $error_password; ?></span>
                  <?  } ?></td>
              </tr>
              <tr>
                <td><?= $entry_confirm; ?></td>
                <td><input type="password" autocomplete='off' name="confirm" value="<?= $confirm; ?>" />
                  <? if ($error_confirm) { ?>
                  <span class="error"><?= $error_confirm; ?></span>
                  <?  } ?></td>
              </tr>
              <tr>
                <td><?= $entry_newsletter; ?></td>
                <td><select name="newsletter">
                    <? if ($newsletter) { ?>
                    <option value="1" selected="selected"><?= $text_enabled; ?></option>
                    <option value="0"><?= $text_disabled; ?></option>
                    <? } else { ?>
                    <option value="1"><?= $text_enabled; ?></option>
                    <option value="0" selected="selected"><?= $text_disabled; ?></option>
                    <? } ?>
                  </select></td>
              </tr>
              <tr>
                <td><?= $entry_customer_group; ?></td>
                <td><select name="customer_group_id">
                    <? foreach ($customer_groups as $customer_group) { ?>
                    <? if ($customer_group['customer_group_id'] == $customer_group_id) { ?>
                    <option value="<?= $customer_group['customer_group_id']; ?>" selected="selected"><?= $customer_group['name']; ?></option>
                    <? } else { ?>
                    <option value="<?= $customer_group['customer_group_id']; ?>"><?= $customer_group['name']; ?></option>
                    <? } ?>
                    <? } ?>
                  </select></td>
              </tr>
              <tr>
                <td><?= $entry_status; ?></td>
                <td><select name="status">
                    <? if ($status) { ?>
                    <option value="1" selected="selected"><?= $text_enabled; ?></option>
                    <option value="0"><?= $text_disabled; ?></option>
                    <? } else { ?>
                    <option value="1"><?= $text_enabled; ?></option>
                    <option value="0" selected="selected"><?= $text_disabled; ?></option>
                    <? } ?>
                  </select></td>
              </tr>
            </table>
          </div>
          <? $address_row = 1; ?>
          <? foreach ($addresses as $address) { ?>
          <div id="tab-address-<?= $address_row; ?>" class="vtabs-content">
            <input type="hidden" name="address[<?= $address_row; ?>][address_id]" value="<?= $address['address_id']; ?>" />
            <table class="form">
              <tr>
                <td><span class="required"></span> <?= $entry_firstname; ?></td>
                <td><input type="text" name="address[<?= $address_row; ?>][firstname]" value="<?= $address['firstname']; ?>" />
                  <? if (isset($error_address_firstname[$address_row])) { ?>
                  <span class="error"><?= $error_address_firstname[$address_row]; ?></span>
                  <? } ?></td>
              </tr>
              <tr>
                <td><span class="required"></span> <?= $entry_lastname; ?></td>
                <td><input type="text" name="address[<?= $address_row; ?>][lastname]" value="<?= $address['lastname']; ?>" />
                  <? if (isset($error_address_lastname[$address_row])) { ?>
                  <span class="error"><?= $error_address_lastname[$address_row]; ?></span>
                  <? } ?></td>
              </tr>
              <tr>
                <td><?= $entry_company; ?></td>
                <td><input type="text" name="address[<?= $address_row; ?>][company]" value="<?= $address['company']; ?>" /></td>
              </tr>
              <tr>
                <td><span class="required"></span> <?= $entry_address_1; ?></td>
                <td><input type="text" name="address[<?= $address_row; ?>][address_1]" value="<?= $address['address_1']; ?>" />
                  <? if (isset($error_address_address_1[$address_row])) { ?>
                  <span class="error"><?= $error_address_address_1[$address_row]; ?></span>
                  <? } ?></td>
              </tr>
              <tr>
                <td><?= $entry_address_2; ?></td>
                <td><input type="text" name="address[<?= $address_row; ?>][address_2]" value="<?= $address['address_2']; ?>" /></td>
              </tr>
              <tr>
                <td><span class="required"></span> <?= $entry_city; ?></td>
                <td><input type="text" name="address[<?= $address_row; ?>][city]" value="<?= $address['city']; ?>" />
                  <? if (isset($error_address_city[$address_row])) { ?>
                  <span class="error"><?= $error_address_city[$address_row]; ?></span>
                  <? } ?></td>
              </tr>
              <tr>
                <td><span class="required"></span> <?= $entry_postcode; ?></td>
                <td><input type="text" name="address[<?= $address_row; ?>][postcode]" value="<?= $address['postcode']; ?>" /></td>
              </tr>
              <tr>
                <td><span class="required"></span> <?= $entry_country; ?></td>
                <td>
                   <?= $this->builder->set_config('country_id', 'name');?>
                   <?= $this->builder->build('select', $countries, "address[$address_row][country_id]", $address['country_id'], array('class'=>'country_select'));?>
                  <? if (isset($error_address_country[$address_row])) { ?>
                  <span class="error"><?= $error_address_country[$address_row]; ?></span>
                  <? } ?></td>
              </tr>
              <tr>
                <td><span class="required"></span> <?= $entry_zone; ?></td>
                <td><select name="address[<?= $address_row; ?>][zone_id]" zone_id="<?= $address['zone_id'];?>" class="zone_select"></select>
                  <? if (isset($error_address_zone[$address_row])) { ?>
                  <span class="error"><?= $error_address_zone[$address_row]; ?></span>
                  <? } ?></td>
              </tr>
              <tr>
                <td><?= $entry_default; ?></td>
                <td><? if (($address['address_id'] == $address_id) || !$addresses) { ?>
                  <input type="radio" name="address[<?= $address_row; ?>][default]" value="<?= $address_row; ?>" checked="checked" /></td>
                <? } else { ?>
                <input type="radio" name="address[<?= $address_row; ?>][default]" value="<?= $address_row; ?>" />
                  </td>
                <? } ?>
              </tr>
            </table>
          </div>
          <? $address_row++; ?>
          <? } ?>
        </div>
        <? if ($customer_id) { ?>
        <div id="tab-transaction">
          <table class="form">
            <tr>
              <td><?= $entry_description; ?></td>
              <td><input type="text" name="description" value="" /></td>
            </tr>
            <tr>
              <td><?= $entry_amount; ?></td>
              <td><input type="text" name="amount" value="" /></td>
            </tr>
            <tr>
              <td colspan="2" style="text-align: right;"><a id="button-reward" class="button" onclick="addTransaction();"><span><?= $button_add_transaction; ?></span></a></td>
            </tr>
          </table>
          <div id="transaction"></div>
        </div>
        <div id="tab-reward">
          <table class="form">
            <tr>
              <td><?= $entry_description; ?></td>
              <td><input type="text" name="description" value="" /></td>
            </tr>
            <tr>
              <td><?= $entry_points; ?></td>
              <td><input type="text" name="points" value="" /></td>
            </tr>
            <tr>
              <td colspan="2" style="text-align: right;"><a id="button-reward" class="button" onclick="addRewardPoints();"><span><?= $button_add_reward; ?></span></a></td>
            </tr>
          </table>
          <div id="reward"></div>
        </div>
        <? } ?>
        <div id="tab-ip">
          <table class="list">
            <thead>
              <tr>
                <td class="left"><?= $column_ip; ?></td>
                <td class="right"><?= $column_total; ?></td>
                <td class="left"><?= $column_date_added; ?></td>
                <td class="right"><?= $column_action; ?></td>
              </tr>
            </thead>
            <tbody>
              <? if ($ips) { ?>
              <? foreach ($ips as $ip) { ?>
              <tr>
                <td class="left"><a onclick="window.open('http://www.geoiptool.com/en/?IP=<?= $ip['ip']; ?>');"><?= $ip['ip']; ?></a></td>
                <td class="right"><a onclick="window.open('<?= $ip['filter_ip']; ?>');"><?= $ip['total']; ?></a></td>
                <td class="left"><?= $ip['date_added']; ?></td>
                <td class="right"><? if ($ip['blacklist']) { ?>
                  <b>[</b> <a id="<?= str_replace('.', '-', $ip['ip']); ?>" onclick="removeBlacklist('<?= $ip['ip']; ?>');"><?= $text_remove_blacklist; ?></a> <b>]</b>
                  <? } else { ?>
                  <b>[</b> <a id="<?= str_replace('.', '-', $ip['ip']); ?>" onclick="addBlacklist('<?= $ip['ip']; ?>');"><?= $text_add_blacklist; ?></a> <b>]</b>
                  <? } ?></td>
              </tr>
              <? } ?>
              <? } else { ?>
              <tr>
                <td class="center" colspan="3"><?= $text_no_results; ?></td>
              </tr>
              <? } ?>
            </tbody>
          </table>
        </div>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
var address_row = <?= $address_row; ?>;

function addAddress() {	
	html  = '<div id="tab-address-' + address_row + '" class="vtabs-content" style="display: none;">';
	html += '  <input type="hidden" name="address[' + address_row + '][address_id]" value="" />';
	html += '  <table class="form">'; 
	html += '    <tr>';
    html += '	   <td><?= $entry_firstname; ?></td>';
    html += '	   <td><input type="text" name="address[' + address_row + '][firstname]" value="" /></td>';
    html += '    </tr>';
    html += '    <tr>';
    html += '      <td><?= $entry_lastname; ?></td>';
    html += '      <td><input type="text" name="address[' + address_row + '][lastname]" value="" /></td>';
    html += '    </tr>';
    html += '    <tr>';
    html += '      <td><?= $entry_company; ?></td>';
    html += '      <td><input type="text" name="address[' + address_row + '][company]" value="" /></td>';
    html += '    </tr>';
    html += '    <tr>';
    html += '      <td><?= $entry_address_1; ?></td>';
    html += '      <td><input type="text" name="address[' + address_row + '][address_1]" value="" /></td>';
    html += '    </tr>';
    html += '    <tr>';
    html += '      <td><?= $entry_address_2; ?></td>';
    html += '      <td><input type="text" name="address[' + address_row + '][address_2]" value="" /></td>';
    html += '    </tr>';
    html += '    <tr>';
    html += '      <td><?= $entry_city; ?></td>';
    html += '      <td><input type="text" name="address[' + address_row + '][city]" value="" /></td>';
    html += '    </tr>';
    html += '    <tr>';
    html += '      <td><?= $entry_postcode; ?></td>';
    html += '      <td><input type="text" name="address[' + address_row + '][postcode]" value="" /></td>';
    html += '    </tr>';
    html += '      <td><?= $entry_country; ?></td>';
    html += '      <td><select name="address[' + address_row + '][country_id]" class="country_select">';
    html += '         <option value=""><?= $text_select; ?></option>';
    <? foreach ($countries as $country) { ?>
    html += '         <option value="<?= $country['country_id']; ?>"><?= addslashes($country['name']); ?></option>';
    <? } ?>
    html += '      </select></td>';
    html += '    </tr>';
    html += '    <tr>';
    html += '      <td><?= $entry_zone; ?></td>';
    html += '      <td><select name="address[' + address_row + '][zone_id]" class="zone_select"><option value="false"><?= $text_none; ?></option></select></td>';
    html += '    </tr>';
	html += '    <tr>';
    html += '      <td><?= $entry_default; ?></td>';
    html += '      <td><input type="radio" name="address[' + address_row + '][default]" value="1" /></td>';
    html += '    </tr>';
    html += '  </table>';
    html += '</div>';
	
	$('#tab-general').append(html);
	
	$('#address-add').before('<a href="#tab-address-' + address_row + '" id="address-' + address_row + '"><?= $tab_address; ?> ' + address_row + '&nbsp;<img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" alt="" onclick="$(\'#vtabs a:first\').trigger(\'click\'); $(\'#address-' + address_row + '\').remove(); $(\'#tab-address-' + address_row + '\').remove(); return false;" /></a>');
		 
	$('.vtabs a').tabs();
	
	$('#address-' + address_row).trigger('click');
	
	address_row++;
}
//--></script>

<?= $this->builder->js('load_zones', 'table.form', '.country_select', '.zone_select');?>

<script type="text/javascript"><!--
$('#transaction .pagination a').live('click', function() {
	$('#transaction').load(this.href);
	
	return false;
});			

$('#transaction').load("<?= HTTP_ADMIN . "index.php?route=sale/customer/transaction"; ?>" + '&customer_id=<?= $customer_id; ?>');

function addTransaction() {
	$.ajax({
		url: "<?= HTTP_ADMIN . "index.php?route=sale/customer/transaction"; ?>" + '&customer_id=<?= $customer_id; ?>',
		type: 'post',
		dataType: 'html',
		data: 'description=' + encodeURIComponent($('#tab-transaction input[name=\'description\']').val()) + '&amount=' + encodeURIComponent($('#tab-transaction input[name=\'amount\']').val()),
		beforeSend: function() {
			$('.success, .warning').remove();
			$('#button-transaction').attr('disabled', true);
			$('#transaction').before('<div class="attention"><img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>" alt="" /> <?= $text_wait; ?></div>');
		},
		complete: function() {
			$('#button-transaction').attr('disabled', false);
			$('.attention').remove();
		},
		success: function(html) {
			$('#transaction').html(html);
			
			$('#tab-transaction input[name=\'amount\']').val('');
			$('#tab-transaction input[name=\'description\']').val('');
		}
	});
}
//--></script> 
<script type="text/javascript"><!--
$('#reward .pagination a').live('click', function() {
	$('#reward').load(this.href);
	
	return false;
});			

$('#reward').load("<?= HTTP_ADMIN . "index.php?route=sale/customer/reward"; ?>" + '&customer_id=<?= $customer_id; ?>');

function addRewardPoints() {
	$.ajax({
		url: "<?= HTTP_ADMIN . "index.php?route=sale/customer/reward"; ?>" + '&customer_id=<?= $customer_id; ?>',
		type: 'post',
		dataType: 'html',
		data: 'description=' + encodeURIComponent($('#tab-reward input[name=\'description\']').val()) + '&points=' + encodeURIComponent($('#tab-reward input[name=\'points\']').val()),
		beforeSend: function() {
			$('.success, .warning').remove();
			$('#button-reward').attr('disabled', true);
			$('#reward').before('<div class="attention"><img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>" alt="" /> <?= $text_wait; ?></div>');
		},
		complete: function() {
			$('#button-reward').attr('disabled', false);
			$('.attention').remove();
		},
		success: function(html) {
			$('#reward').html(html);
								
			$('#tab-reward input[name=\'points\']').val('');
			$('#tab-reward input[name=\'description\']').val('');
		}
	});
}

function addBlacklist(ip) {
	$.ajax({
		url: "<?= HTTP_ADMIN . "index.php?route=sale/customer/addblacklist"; ?>",
		type: 'post',
		dataType: 'json',
		data: 'ip=' + encodeURIComponent(ip),
		beforeSend: function() {
			$('.success, .warning').remove();
			
			$('.box').before('<div class="attention"><img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>" alt="" /> Please wait!</div>');			
		},
		complete: function() {
			$('.attention').remove();
		},			
		success: function(json) {
			if (json['error']) {
				 $('.box').before('<div class="message_box warning" style="display: none;">' + json['error'] + '</div>');
				
				$('.warning').fadeIn('slow');
			}
						
			if (json['success']) {
                $('.box').before('<div class="message_box success" style="display: none;">' + json['success'] + '</div>');
				
				$('.success').fadeIn('slow');
				
				$('#' + ip.replace(/\./g, '-')).replaceWith('<a id="' + ip.replace(/\./g, '-') + '" onclick="removeBlacklist(\'' + ip + '\');"><?= $text_remove_blacklist; ?></a>');
			}
		}
	});	
}

function removeBlacklist(ip) {
	$.ajax({
		url: "<?= HTTP_ADMIN . "index.php?route=sale/customer/removeblacklist"; ?>",
		type: 'post',
		dataType: 'json',
		data: 'ip=' + encodeURIComponent(ip),
		beforeSend: function() {
			$('.success, .warning').remove();
			
			$('.box').before('<div class="attention"><img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>" alt="" /> Please wait!</div>');				
		},
		complete: function() {
			$('.attention').remove();
		},			
		success: function(json) {
			if (json['error']) {
				 $('.box').before('<div class="message_box warning" style="display: none;">' + json['error'] + '</div>');
				
				$('.warning').fadeIn('slow');
			}
			
			if (json['success']) {
				 $('.box').before('<div class="message_box success" style="display: none;">' + json['success'] + '</div>');
				
				$('.success').fadeIn('slow');
				
				$('#' + ip.replace(/\./g, '-')).replaceWith('<a id="' + ip.replace(/\./g, '-') + '" onclick="addBlacklist(\'' + ip + '\');"><?= $text_add_blacklist; ?></a>');
			}
		}
	});	
};
//--></script> 
<script type="text/javascript"><!--
$('.htabs a').tabs();
$('.vtabs a').tabs();
//--></script> 
<?= $footer; ?>