<div id="customer_information_box">
   <? if(isset($no_address)){ ?>
      <div id='new_address_form' class='info_item' route='checkout/block/new_address' validate='checkout/block/new_address/validate'>
         <h2 class='info_heading'><?= $text_new_address;?></h2>
         <div class='info_content'><?= $new_address;?></div>
         <div class='validation_status'></div>
      </div>
   <? } else { ?>
   
   <? if($guest_checkout){ ?>
      <div id='guest_information' class='info_item' route='checkout/block/guest_information' validate='checkout/block/guest_information/validate'>
         <div class='info_content'><?= $guest_information;?></div>
         <div class='validation_status'></div>
      </div>
   <? } else { ?>
      <? if(isset($shipping_address)) {?>
      <div id='shipping_address' class='info_item' route='checkout/block/shipping_address' validate='checkout/block/shipping_address/validate'>
         <h2 class='info_heading'><?= $text_shipping_information;?></h2>
         <div class='info_content'><?= $shipping_address;?></div>
         <div class='validation_status'></div>
      </div>
      <? }?>
      <div id='payment_address' class='info_item' route='checkout/block/payment_address' validate='checkout/block/payment_address/validate'>
         <h2 class='info_heading'><?= $text_payment_address;?></h2>
         <div class='info_content'><?= $payment_address;?></div>
         <div class='validation_status'></div>
      </div>
   <? }?>
   <? if(isset($shipping_method)) {?>
   <div id='shipping_method' class='info_item' route='checkout/block/shipping_method' validate='checkout/block/shipping_method/validate'>
      <h2 class='info_heading'><?= $guest_checkout ? $text_shipping_method : '';?></h2>
      <div class='info_content'><?= $shipping_method;?></div>
      <div class='validation_status'></div>
   </div>
   <? }?>
   <div id='payment_method' class='info_item' route='checkout/block/payment_method' validate='checkout/block/payment_method/validate'>
      <h2 class='info_heading'><?= $text_payment_method;?></h2>
      <div class='info_content'><?= $payment_method;?></div>
      <div class='validation_status'></div>
   </div>
   
   <div class="buttons">
     <div class="right"><input type="button" value="<?= $button_continue; ?>" onclick="check_statuses(true)" id="button-customer-information" class="button" /></div>
   </div>
   
   <? } //end if else no address ?>
</div>


<script type="text/javascript">//<!--
function check_statuses(button_click, attempts){
   not_valid = $('.info_item').not('.valid');
   
   fresh = $('.info_item').not('.valid, .invalid, .loading, .validating');
   
   if(fresh.length){
      validate_info_item(fresh.first(), '', check_statuses);
      return;
   }
      
   if(not_valid.length){
      if(button_click){
         reload = not_valid.not('.loading, .validating');
         
         if(reload.length){
            validate_info_item(reload.first(), '', check_statuses);
         }
      }
      else{
         attempts = attempts || 0;
         
         if($('.info_item.invalid').length > 0 && attempts < 1){
            validate_info_item($('.info_item.invalid').first(), '', function(){check_statuses(false, attempts + 1)});
         }
         else if($('.info_item').not('.valid, .invalid').length > 0){
            setTimeout(check_statuses, 400);
         }
      }
      
      
   }
   else{
      submit_checkout_item($('#button-customer-information'));
   }
}

$('.info_item').keyup(change_focus_box).click(change_focus_box);

$('#shipping_method input:radio[name="shipping_method"], #payment_method input:radio[name="payment_method"]').live('change', function(){
   info_item = $(this).closest('.info_item');
   
   if(info_item.find(':checked') && info_item.find(':checked').val()){
      validate_info_item(info_item);
   }
});

function change_focus_box(){
   if($(this).attr('id') == $('.active_info_item').attr('id')) return;
   
   if($('.active_info_item').length && !$('.active_info_item').hasClass('valid')){
      validate_info_item($('.active_info_item'));
   }
   
   $('.active_info_item').removeClass('active_info_item');
   
   $(this).addClass("active_info_item");
}

function set_validation_status(info_item, status, msg){
   if(typeof info_item == 'string'){
      info_item = $('#' + info_item);
   }
   
   info_item.removeClass('validating invalid valid loading').addClass(status)
     .find('.validation_status').html(msg);
}

function info_page_loading(info_item){
   set_validation_status(info_item, 'validating', "<?= $text_info_validating;?> <img src='catalog/view/theme/default/image/loading.gif' alt='' />");
}

function info_page_received(info_item){
}

function not_ready(info_item){
   if(info_item.hasClass('loading') || info_item.hasClass('validating')){
      return true;
   }
   
   false;
}

function load_info_item(info_item, route, callback){
   if(typeof info_item == 'string'){
      info_item = $('#' + info_item);
   }
   
   if(not_ready(info_item)) return;
   
   route = route || info_item.attr('route');
   
   set_validation_status(info_item, 'loading', '<?= $text_info_loading;?>');
   
   info_item.find('.info_content').load('index.php?route=' + route, {},
      function(){ 
         set_validation_status(info_item, '', '');
         if(typeof callback == 'function'){
            callback();
         }
         
         info_item.trigger('loaded');
      }
   );
}

function validate_info_item(info_item, route, callback){
   if(typeof info_item == 'string'){
      info_item = $('#' + info_item);
   }
   
   if(not_ready(info_item)) return;
   
   route = route || info_item.attr('validate');
   
   if(!route || !info_item) return;
   
   console.log('validating ' + info_item.attr('id'));
   $.ajax({
      url: 'index.php?route=' + route,
      type: 'post',
      data: info_item.find('select, input:checked, input[type="text"], input[type="password"], input[type="hidden"], textarea'),
      dataType: 'json',
      beforeSend: function(){ info_page_loading(info_item); },
      complete: function(json){info_page_received(info_item); if(typeof callback == 'function') callback();},
      success: function(json) {
      	info_item.find('.error, .warning').remove();
         
         if (json['redirect']) {
            location = json['redirect'];
         }
         
         if (json['error']) {
            msgs = '';
            for(var e in json['error']){
               msg = '<span class="error">' + json['error'][e] + '</span>';
               info_item.find('[name="'+e+'"]').after(msg);
               msgs += msg;
            }
            if(msgs){
               info_item.find('.info_heading').after('<div class="message_box warning" style="display: none;">' + msgs + '</div>');
               $('.warning').fadeIn('fast');
            }
            
            set_validation_status(info_item, 'invalid', '<?= $text_info_error;?>');
            
         } else {
            set_validation_status(info_item, 'valid', '');
         }
         
         if (json['reload']){
            for(var r in  json['reload']){
               load_info_item(json['reload'][r]);
            }
         }
      },
      error: function(xhr, ajaxOptions, thrownError) {
         info_item.find('.message_box').remove();
         html = '<div class="message_box warning" style="display: none;"><div><?= $error_page_load;?></div></div>';
         info_item.find('.checkout-content').prepend(html);
         info_item.find('.warning').fadeIn('fast');
      }
   });
}
//--></script>

<script type="text/javascript">//<!--
<? if($guest_checkout) {?>
$('[name="payment[zone_id]"], [name="shipping[zone_id]"]').live('change', function(){
   if($('[name="payment[zone_id]"]').val() && $('[name="shipping[zone_id]"]').val()){
      validate_info_item('guest_information');
   }
});

$('#guest_information').find('input, select, textarea').live('keyup change', function(){
   if($(this).closest('.info_item').hasClass('valid')){
      set_validation_status($(this).closest('.info_item'), '', '');
   }
});

<? } else { ?>
$('select[name="address_id"]').live('change', function(e){
   if($(this).val()){
      info_item = $(this).closest('.info_item');
      validate_info_item(info_item);
   }
}).trigger('change');

$('#shipping_address, #payment_address').find('input, textarea, select').live('change', function(){
   info_item = $(this).closest('.info_item');
   
   if(info_item.find(':checked')){
      if(info_item.find(':checked').val() == 'existing'){
         validate_info_item(info_item);
      }
      else{
         set_validation_status(info_item, '','');
      }
   }
});

$('#shipping-new input[type=submit], #payment-new input[type=submit], #add_new_address input[type=submit]').live('click', function(){
   validate_info_item($(this).closest('.info_item'));
});
<? } ?>

$('#add_comment textarea').live('keyup', function(){
   if($(this).closest('.info_item').hasClass('valid')){
      set_validation_status($(this).closest('.info_item'), '', '');
   }
});

$('.info_item').trigger('loaded');
//--></script>