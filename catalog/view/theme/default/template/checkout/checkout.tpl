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
    <div id="customer_information" class='checkout_item' route='checkout/block/customer_information'>
      <div class="checkout-heading"><?= $text_step . ' ' . $step++;?>. <?= $text_checkout_information; ?></div>
      <div class="checkout-content"></div>
    </div>
    <div id="confirm" class='checkout_item' route='checkout/block/confirm'>
      <div class="checkout-heading"><?= $text_step . ' ' . $step++;?>. <?= $text_checkout_confirm; ?></div>
      <div class="checkout-content"></div>
    </div>
  </div>
  <?= $content_bottom; ?>
</div>

<script type="text/javascript">//<!--

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
            $(e).find('.checkout-heading').append("<a class=\"modify\" onclick=\"load_checkout_item($(this).closest('.checkout_item'))\"><?= $text_modify; ?></a>");
         });
      },
      error: handle_ajax_error
   });
}

function load_next_checkout_item(){
   load_checkout_item($('.active_checkout_item').next());
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
   
   if(id && id == 'button-account' && $('[name=account]:checked').val() == 'register'){
      load_checkout_item('customer_information', 'checkout/block/register');
      return;
   }
   
   load_next_checkout_item();
}


function validate_form(form, callback){
	if(!form.attr('action')) return;
	
	data = form.serialize();
	
	//Add Submit name attribute to query
	form_submit = form.find('input[type=submit]');
	
	if(form_submit){
		data += '&' + form_submit.attr('name') + '=' + form_submit.val();
	}
	
	//Add asynchronous ajax call flag
	data += '&async=1';
	
	$.ajax({
		url: form.attr('action'),
		type: 'post',
		data: data,
		dataType: 'json',
		success: function(json) {
			handle_validation_response(form, json);
			
			if(typeof callback == 'function'){
				callback(form, json);
			}
		},
		error: function(jqXHR, status){
			handle_ajax_error(jqXHR, status);
			
			handle_validation_response(form, {});
		}
	});
}

function handle_validation_response(form, json){
	json = json || {};
	
	if (json['redirect']) {
		location = json['redirect'];
	}
	
	form.find('.message_box, .error').remove();
	
	if (json['error']) {
		msgs = '';
		for(var e in json['error']){
			msg = '<span class="error">' + json['error'][e] + '</span>';
			form.find('[name="'+e+'"]').after(msg);
			msgs += msg;
		}
		if(msgs){
			form.prepend('<div class="message_box warning" style="display: none;">' + msgs + '</div>');
			$('.warning').fadeIn('fast');
		}
	}
}
//--></script> 
<?= $footer; ?>