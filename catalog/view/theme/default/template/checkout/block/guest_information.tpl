<div id='guest-checkout'>
 <form action="" method="post">
   <div class="left general_form">
     <h2><?= $text_your_details; ?></h2>
     <?= $form_general;?>
   </div>
   <div class="right payment_address">
     <h2><?= $text_payment_address; ?></h2>
     <?= $form_payment_address;?>
   </div>
   <? if ($shipping_required) { ?>
   <div style="clear:both">
     <input type="checkbox" name="same_shipping_address" value="1" id="shipping" <?= $same_shipping_address ? 'checked="checked"' : '';?> />
     <label for="shipping"><?= $entry_shipping; ?></label>
   </div>
   <div class="left shipping_address">
      <h2><?= $text_shipping_address;?></h2>
      <?= $form_shipping_address;?>
   </div>
   <? } ?>
 </form>
</div>

<?=$this->builder->js('load_zones', '#guest-checkout .shipping_address, #guest-checkout .payment_address', '.country_select', '.zone_select');?>

<script type="text/javascript">//<!--
$('#guest-checkout input[name=same_shipping_address]').change(function(){
   shipping_form = $('#guest-checkout .shipping_address');
   
   if($(this).is(':checked')){
      shipping_form.hide();
   }
   else{
      shipping_form.slideDown('fast');
   }
}).trigger('change');
//--></script>