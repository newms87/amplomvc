<div id="customer_information_box">
	<? if(!empty($guest_checkout)) { ?>
		<div id='guest_information' class='info_item' route='block/checkout/guest_information'>
			<div class='info_content'><?= $block_guest_information; ?></div>
			<div class='validation_status'></div>
		</div>
	<? } else { ?>
		<? if(!empty($block_shipping_address)) {?>
			<div id='shipping_address' class='info_item' route='block/checkout/shipping_address'>
				<h2 class='info_heading'><?= $text_shipping_information; ?></h2>
				<div class='info_content'><?= $block_shipping_address; ?></div>
				<div class='validation_status'></div>
			</div>
		<? }?>
		
		<div id='payment_address' class='info_item' route='block/checkout/payment_address'>
			<h2 class='info_heading'><?= $text_payment_address; ?></h2>
			<div class='info_content'><?= $block_payment_address; ?></div>
			<div class='validation_status'></div>
		</div>
	<? }?>
	
	<? if(!empty($block_shipping_method)) {?>
		<div id='shipping_method' class='info_item' route='block/checkout/shipping_method'>
			<h2 class='info_heading'><?= !empty($guest_checkout) ? $text_shipping_method : ''; ?></h2>
			<div class='info_content'><?= $block_shipping_method; ?></div>
			<div class='validation_status'></div>
		</div>
	<? }?>
	
	<div id='payment_method' class='info_item' route='block/checkout/payment_method'>
		<h2 class='info_heading'><?= $text_payment_method; ?></h2>
		<div class='info_content'><?= $block_payment_method; ?></div>
		<div class='validation_status'></div>
	</div>
	
	<div id="customer_checkout_submit" class="buttons">
		<div class="right"><input type="button" value="<?= $button_continue; ?>" onclick="validate_submit($(this))" class="button" /></div>
	</div>
</div>


<script type="text/javascript">//<!--
//invalidate an info item if any changes are detected
$('.info_item [name]').change(function(){
	info_item = $(this).closest('.info_item');
	
	set_validation_status(info_item, '');
});

function set_validation_status(info_item, status, msg){
	if(typeof info_item == 'string'){
		info_item = $('#' + info_item);
	}
	
	info_item.removeClass('validating invalid valid loading').addClass(status)
	.find('.validation_status').html(msg);
	
	handle_dependencies(info_item);
}

function info_page_loading(info_item){
	set_validation_status(info_item, 'validating', "<?= $text_info_validating; ?> <img src='<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>' alt='' />");
}

function info_page_received(info_item){
}

function load_info_item(info_item, route, callback){
	//Still loading..
	if(info_item.hasClass('loading') || info_item.hasClass('validating')) return;
	
	route = route || info_item.attr('route');
	
	set_validation_status(info_item, 'loading', '<?= $text_info_loading; ?>');
	
	info_item.find('.info_content').load("<?= HTTP_ROOT; ?>" + route, {},
		function(){
			set_validation_status(info_item, '', '');
			if(typeof callback == 'function'){
				callback();
			}
			
			info_item.trigger('loaded');
		}
	);
}

function ci_validate_form(form, reload){
	reload = reload || false;
	
	info_page_loading(form.closest('.info_item'));
	
	validate_form(form, function(form, json){
		info_item = form.closest('.info_item');
		
		if (!json || json['error']) {
			set_validation_status(info_item, 'invalid', '<?= $text_info_error; ?>');
			
		} else {
			set_validation_status(info_item, 'valid', '');
			
			if(reload){
				load_info_item(info_item);
			}
		}
	});
}

function handle_dependencies(info_item){
	switch(info_item.attr('id')){
		case 'shipping_address':
			if(info_item.hasClass('valid')){
				load_info_item($('#shipping_method'));
			}
			break;
		case 'payment_address':
			if(info_item.hasClass('valid')){
				load_info_item($('#payment_method'));
			}
			break
		case 'guest_information':
			if(info_item.hasClass('valid')){
				load_info_item($('#shipping_method'));
				load_info_item($('#payment_method'));
				$('#shipping_method, #payment_method, #customer_checkout_submit').slideDown('fast');
				$('#guest_checkout_submit').hide();
			}
			else{
				$('#shipping_method, #payment_method, #customer_checkout_submit').hide();
				$('#guest_checkout_submit').show();
			}
			break;
		default:
			break;
	}
}

//Validate all the form items before submitting Customer Information
function validate_submit(submit, recheck){
	recheck = recheck || false;
	
	if (recheck) {
		if ($('.info_item.invalid').length) return;
		
		if ($('.info_item.validating').length) {
			setTimeout(function(){validate_submit(submit, true);}, 500 );
			return;
		}
	}
	
	if ($('.info_item').length === $('.info_item.valid').length) {
		$.get("<?= $validate_customer_checkout; ?>", {}, function(json){
			if(json && json.length){
				$('.info_item').each(function(i,e){
					set_validation_status($(e), 'valid', '');
				});
				
				if(json['error']){
					for(e in json['error']){
						ci_validate_form($('#' + e + ' form'));
					}
				}
				
				handle_validation_response($('#customer_information_box'), json);
			}
			else{
				submit_checkout_item(submit);
			}
		}, 'json')
		.error(handle_ajax_error);
	}
	else if($('#guest_information').length) {
		if($('#guest_information').length && !$('#guest_information').hasClass('valid')){
			ci_validate_form($('#guest_information form'));
		}
		else {
			if($('#shipping_method').length && !$('#shipping_method').hasClass('valid')){
				ci_validate_form($('#shipping_method form'));
			}
			
			if(!$('#payment_method').hasClass('valid')){
				ci_validate_form($('#payment_method form'));
			}
		}
		
		setTimeout(function(){validate_submit(submit, true);}, 500 );
	}
	else {
		if($('#shipping_address').length && !$('#shipping_address').hasClass('valid')){
			if($('[name=shipping_address]:checked').val() == 'existing'){
				ci_validate_form($('#shipping_existing form'));
			}
			else{
				ci_validate_form($('#shipping_new form'));
			}
		}
		else if($('#shipping_method').length && !$('#shipping_method').hasClass('valid')){
			ci_validate_form($('#shipping_method form'));
		}
		
		if(!$('#payment_address').hasClass('valid')){
			if($('[name=payment_address]:checked').val() == 'existing'){
				ci_validate_form($('#payment_existing form'));
			}
			else{
				ci_validate_form($('#payment_new form'));
			}
		}
		else if(!$('#payment_method').hasClass('valid')){
			ci_validate_form($('#payment_method form'));
		}
		
		setTimeout(function(){validate_submit(submit, true);}, 500 );
	}
	
	return false;
}
//--></script>

<script type="text/javascript">//<!--
<? if(!empty($guest_checkout)) { ?>
	
$('input[name=submit_guest_checkout]').live('click', function(){
	ci_validate_form($('#guest_checkout form'));
	
	return false;
});

$('#guest_checkout').on('loaded', function(){
	handle_dependencies($('#guest_information'));
}).trigger('loaded');

<? } else { ?>
	
$('#shipping_new input[type=submit], #payment_new input[type=submit]').live('click', function(){
	ci_validate_form($(this).closest('form'), true);
	
	return false;
});

$('#shipping_address').on('loaded', function(){
	sa_form = $('#shipping_existing form');
	if(sa_form.find('select[name=address_id]').val()){
		ci_validate_form(sa_form);
	}
});

$('#payment_address').on('loaded', function(){
	sa_form = $('#payment_existing form');
	if(sa_form.find('select[name=address_id]').val()){
		ci_validate_form(sa_form);
	}
});

<? } ?>


$('#add_comment textarea').live('keyup', function(){
	if($(this).closest('.info_item').hasClass('valid')){
		set_validation_status($(this).closest('.info_item'), '', '');
	}
});

$('#shipping_method, #payment_method').on('loaded', function(){
});

$('.info_item').trigger('loaded');
//--></script>