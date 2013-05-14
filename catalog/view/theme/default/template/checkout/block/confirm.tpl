<? if (!isset($redirect)) { ?>

<? if ($details_only) {?>
<div class='checkout_totals'>
  <?= $block_totals;?>
</div>
   
<div class="payment">
  <?= $payment; ?>
</div>
<? } else { ?>
<div class="checkout-template">
  <? if(isset($block_confirm_address)){ ?>
    <?= $block_confirm_address;?>
  <? } ?>
  
  <? if(isset($block_cart)) { ?>
  <div class='checkout_cart'>
     <?= $block_cart;?>
  </div>
  <? }?>
  
  <? if(isset($block_coupon)) { ?>
  <div class='checkout_coupon'>
     <?= $block_coupon;?>
  </div>
  <? }?>
  
  <div id='checkout_details'>
     <div class='checkout_totals'>
        <?= $block_totals;?>
     </div>
      
     <div class="payment">
        <?= $payment; ?>
     </div>
  </div>
</div>

<script type="text/javascript">//<!--
$('body').bind('coupon_success', function(event, json){
   load_block($('#checkout_details .checkout_totals'), 'cart/block/total');
});

function handle_ajax_cart_preload(action, data){
   $('#checkout_details').html($('#loading_details').show().height($('#checkout_details').height()));
}

var retry_count = 3;
function handle_ajax_cart_load(action, data){
   $('#checkout_details').load("<?= $load_details;?>",{},
      function(){
         if($('#checkout_details .payment').length < 1){
            if(retry_count <= 0){
               location = '<?= $checkout_url;?>';
            }
            retry_count--;
            handle_ajax_cart_load(action, data);
         }
      });
}
//--></script>
<? }?>

<div id='loading_details' style='display:none'>
   <img src="<?= HTTP_THEME_IMAGE . 'loading.gif';?>" />
   <span class='loading_message'><?= $text_loading_details;?></span>
</div>

<? } else { ?>
<script type="text/javascript">//<!--
location = "<?= $redirect; ?>";
//--></script>
<? } ?>
