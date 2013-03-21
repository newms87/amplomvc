<? if(!empty($allowed_geo_zones)){?>
   <h2><?= $text_zone_allowed;?></h2>
   <div class='allowed_zone_list'>
   <? foreach($allowed_geo_zones as $i=>$geo_zone){ ?>
      <span class='allowed_zone_item'><?= $geo_zone['country']['name'] . (($i==count($allowed_geo_zones)-1) ? '' : $text_zone_separator);?></span>
   <? } ?>
   </div>
<? }?>
<? if ($addresses) { ?>
<input type="radio" name="shipping_address" value="existing" id="shipping-address-existing" checked="checked" />
<label for="shipping-address-existing"><?= $text_address_existing; ?></label>
<div id="shipping-existing">
  <select name="address_id" style="width: 100%; margin-bottom: 15px;" size="5">
    <? foreach ($addresses as $address) { ?>
      <option value="<?= $address['address_id']; ?>" <?= $address['address_id'] == $address_id ? 'selected="selected"' : '';?>><?= $address['firstname']; ?> <?= $address['lastname']; ?>, <?= $address['address_1']; ?>, <?= $address['city']; ?>, <?= $address['zone']; ?>, <?= $address['country']; ?></option>
    <? } ?>
  </select>
</div>
<? } ?>
<p>
  <input type="radio" name="shipping_address" value="new" <?=$addresses?'':'checked="checked"';?> id="shipping-address-new" />
  <label for="shipping-address-new"><?= $text_address_new; ?></label>
</p>
<div id="shipping-new" class="address_form" style="display: none;">
  
  <?= $form_shipping_address;?>
  
  <div class="set_default_address">
      <div><?= $entry_set_default;?></div>
      <?= $this->builder->build('radio', $data_yes_no, 'default', $set_default);?>
  </div>
  <div class='submit_address_button'>
     <input type='submit' value='<?= $button_add_address;?>' class='button' onclick='return false'/>
  </div>
</div>

<?= $this->builder->js('load_zones', '#shipping-new', '.country_select','.zone_select');?>

<script type="text/javascript">//<!--
$('input[name=shipping_address]').live('change', function() {
	if ($('input[name=shipping_address]:checked').val() == 'new') {
		$('#shipping-existing').hide();
		$('#shipping-new').show();
	} else {
		$('#shipping-existing').show();
		$('#shipping-new').hide();
	}
}).trigger('change');
//--></script> 