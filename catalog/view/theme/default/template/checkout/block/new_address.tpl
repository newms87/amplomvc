<div id='add_new_address' class="address_form">
  
  <?= $form_new_address;?>
  
  <div class='submit_address_button'>
     <input type='submit' value='<?= $button_add_address;?>' class='button' onclick='return false'/>
  </div>
</div>

<?= $this->builder->js('load_zones', '#add_new_address', '.country_select','.zone_select');?> 