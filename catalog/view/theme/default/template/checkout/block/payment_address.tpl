<? if ($addresses) { ?>
<input type="radio" name="payment_address" value="existing" id="payment-address-existing" checked="checked" />
<label for="payment-address-existing"><?= $text_address_existing; ?></label>
<div id="payment-existing">
  <select name="address_id" style="width: 100%; margin-bottom: 15px;" size="5">
    <? foreach ($addresses as $address) { ?>
     <option value="<?= $address['address_id']; ?>" <?= $address['address_id'] == $address_id ? 'selected="selected"' : ''; ?>>
        <?= $address['firstname']; ?> <?= $address['lastname']; ?>, <?= $address['address_1']; ?>, <?= $address['city']; ?>, <?= $address['zone']; ?>, <?= $address['country']; ?>
     </option>
    <? } ?>
  </select>
</div>
<? } ?>
<p>
  <input type="radio" name="payment_address" value="new" <?=$addresses?'':'checked="checked"';?> id="payment-address-new" />
  <label for="payment-address-new"><?= $text_address_new; ?></label>
</p>
<div id="payment-new" class="address_form" style="display:none">
  <form action='' method="post">
     <?= $form_payment_address;?>
      
     <div class="set_default_address">
         <div><?= $entry_set_default;?></div>
         <?= $this->builder->build('radio', $data_yes_no, 'default', $set_default);?>
     </div>
     <div class='submit_address_button'>
        <input type='submit' value='<?= $button_add_address;?>' class='button' onclick='return false'/>
     </div>
  </form>
</div>

<?=$this->builder->js('load_zones', 'table.form', '.country_select', '.zone_select');?>

<script type="text/javascript">//<!--
$('input[name=payment_address]').live('change', function() {
   if ($('input[name=payment_address]:checked').val() == 'new') {
		$('#payment-existing').hide()
		$('#payment-new').show();
	} else {
		$('#payment-existing').show();
		$('#payment-new').hide();
	}
}).trigger('change');
//--></script> 