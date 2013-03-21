<div id="confirm_address_block">
   <div class="address_view">
      <h2><?= $text_shipping_address;?></h2>
      <div class="address_item shipping">
         <?= $shipping_address;?>
      </div>
      <a onclick="load_checkout_item('customer_information')"><?= $text_modify_address;?></a>
   </div>
   <div class="address_view">
      <h2><?= $text_payment_address;?></h2>
      <div class="address_item payment">
         <?= $payment_address;?>
      </div>
      <a onclick="load_checkout_item('customer_information')"><?= $text_modify_address;?></a>
   </div>
</div>