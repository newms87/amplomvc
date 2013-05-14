<?= $header; ?><?= $column_left; ?><?= $column_right; ?>
<div id="content" style='padding-top:0'><?= $content_top; ?>
  <?= $this->builder->display_breadcrumbs();?>
  <h1><?= $heading_title; ?></h1>
  <? $step = 1;?>
  <div id='checkout_process' class="checkout">
    <div id="login" class='checkout_item' route='checkout/block/login'>
      <div class="checkout-heading"><?= $text_step . ' ' . $step++;?>. <?= $text_checkout_option; ?></div>
      <div class="checkout-content"></div>
    </div>
    <div id="customer_information" class='checkout_item' route='checkout/block/customer_information' validate='checkout/block/customer_information/validate'>
      <div class="checkout-heading"><?= $text_step . ' ' . $step++;?>. <?= $text_checkout_information; ?></div>
      <div class="checkout-content"></div>
    </div>
    <div id="confirm" class='checkout_item' route='checkout/block/confirm' validate='checkout/block/confirm/validate'>
      <div class="checkout-heading"><?= $text_step . ' ' . $step++;?>. <?= $text_checkout_confirm; ?></div>
      <div class="checkout-content"></div>
    </div>
  </div>
  <?= $content_bottom; ?>
</div>

<script type="text/javascript">
//<!--
$('.checkout-heading a').live('click', function() {
   load_checkout_item($(this).closest('.checkout_item'));
});

$(document).ready(function() {
   <? if($logged){ ?>
      load_checkout_item('customer_information');
   <? } else{?>
      load_checkout_item('login');
   <? }?>
});

function load_checkout_item(c_item, route){
   if(typeof c_item == 'string'){
      c_item = $('#' + c_item);
   }

   route = route || c_item.attr('route');
   
   if(!c_item || !route) return;
   
   $.ajax({
      url: 'index.php?route=' + route,
      dataType: 'html',
      beforeSend: page_loading,
      complete: page_received,
      success: function(html) {
         c_content = c_item.find('.checkout-content');
         
         c_content.html(html);
         
         if($('.active_checkout_item').length){
            scroll_to = $('.active_checkout_item').position().top;
            $('body,html').animate({scrollTop: scroll_to}, 400);
         }
         
         $('.active_checkout_item .checkout-content').slideUp('slow')
         
         $('.active_checkout_item').removeClass('active_checkout_item');
         
         c_content.slideDown('slow');
         
         c_item.addClass('active_checkout_item');
         
         $('.checkout-heading a').remove();
         
         headings = <?= $logged ? 'c_item.prevUntil("#login")' : 'c_item.prevAll()'; ?>;
          
         headings.each(function(i,e){
            $(e).find('.checkout-heading').append('<a><?= $text_modify; ?></a>');
         });
      },
      error: function(xhr, ajaxOptions, thrownError) {
         c_item.find('.message_box').remove();
         html = '<div class="message_box warning" style="display: none;"><div><?= $error_page_load;?></div></div>';
         c_item.find('.checkout-content').prepend(html);
         c_item.find('.warning').fadeIn('fast');
      }
   });
}

function load_next_checkout_item(){
   load_checkout_item($('.active_checkout_item').next());
}

function validate_checkout_item(c_item, validate){
   if(typeof c_item == 'string'){
      c_item = $('#' + c_item);
   }
   
   validate = validate || c_item.attr('validate');
   
   if(!c_item.length) return;
   
   if(!validate){
      load_next_checkout_item();
   }
   else{
      $.ajax({
         url: 'index.php?route=' + validate,
         type: 'post',
         data: c_item.find('select, input:checked, input[type="text"], input[type="password"], input[type="hidden"], textarea'),
         dataType: 'json',
         beforeSend: page_loading,
         complete: page_received,
         success: function(json) {
            c_item.find('.error, .warning').remove();
            
            if (json['redirect']) {
               location = json['redirect'];
            }
            
            if (json['error']) {
               msgs = '';
               for(var e in json['error']){
                  msg = '<span class="error">' + json['error'][e] + '</span>';
                  c_item.find('[name="'+e+'"]').after(msg);
                  msgs += msg;
               }
               if(msgs){
                  c_item.find('.checkout-content').prepend('<div class="message_box warning" style="display: none;">' + msgs + '</div>');
                  c_item.find('.warning').fadeIn('fast');
               }
            } else {
               load_next_checkout_item();
            }
         },
         error: function(xhr, ajaxOptions, thrownError) {
            c_item.find('.message_box').remove();
            html = '<div class="message_box warning" style="display: none;"><div><?= $error_page_load;?></div></div>';
            c_item.find('.checkout-content').prepend(html);
            c_item.find('.warning').fadeIn('fast');
         }
      });
   }
}

function page_loading(){
   $('#checkout_process .button').attr('disabled', true);
   $('#checkout_process .button').after('<span class="wait">&nbsp;<img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>" alt="" /></span>');
}

function page_received(){
   $('#checkout_process .button').attr('disabled', false);
   $('.wait').remove();
}


function submit_checkout_item(context){
   id = context.attr('id');
   if(id == 'button-account'){
      if($('[name=account]:checked').val() == 'register'){
         load_checkout_item('customer_information', 'checkout/block/register');
      }
      else{
         load_next_checkout_item();
      }
   }
   else if(id == 'button-register'){
      validate_checkout_item(context.closest('.checkout_item'), 'checkout/block/register/validate');
   }
   else{
      validate_checkout_item(context.closest('.checkout_item'));
   }
}
//--></script> 
<?= $footer; ?>