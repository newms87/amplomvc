<div id="customer_information_box">
	<? if(isset($no_address)){ ?>
		<div id='new_address_form' class='info_item' route='checkout/block/new_address'>
			<h2 class='info_heading'><?= $text_new_address;?></h2>
			<div class='info_content'><?= $new_address;?></div>
			<div class='validation_status'></div>
		</div>
	<? } else { ?>
	
		<? if($guest_checkout){ ?>
			<div id='guest_information' class='info_item' route='checkout/block/guest_information'>
				<div class='info_content'><?= $guest_information;?></div>
				<div class='validation_status'></div>
			</div>
		<? } else { ?>
			<? if(isset($shipping_address)) {?>
			<div id='shipping_address' class='info_item' route='checkout/block/shipping_address'>
				<h2 class='info_heading'><?= $text_shipping_information;?></h2>
				<div class='info_content'><?= $shipping_address;?></div>
				<div class='validation_status'></div>
			</div>
			<? }?>
			<div id='payment_address' class='info_item' route='checkout/block/payment_address'>
				<h2 class='info_heading'><?= $text_payment_address;?></h2>
				<div class='info_content'><?= $payment_address;?></div>
				<div class='validation_status'></div>
			</div>
		<? }?>
		<? if(isset($shipping_method)) {?>
		<div id='shipping_method' class='info_item' route='checkout/block/shipping_method'>
			<h2 class='info_heading'><?= $guest_checkout ? $text_shipping_method : '';?></h2>
			<div class='info_content'><?= $shipping_method;?></div>
			<div class='validation_status'></div>
		</div>
		<? }?>
		<div id='payment_method' class='info_item' route='checkout/block/payment_method'>
			<h2 class='info_heading'><?= $text_payment_method;?></h2>
			<div class='info_content'><?= $payment_method;?></div>
			<div class='validation_status'></div>
		</div>
		
		<div class="buttons">
		<div class="right"><input type="button" value="<?= $button_continue; ?>" onclick="validate_submit()" id="button-customer-information" class="button" /></div>
		</div>
	
	<? } ?>
</div>


<script type="text/javascript">//<!--
function validate_submit(){
	if(!is_valid($('#shipping_address'))){
		if($('[name=shipping_address]:checked').val() == 'existing'){
			validate_form($('#shipping_existing form'));
		}
		else{
			validate_form($('#shipping_new form'));
		}
	}
	else if(!is_valid($('#payment_address'))){
		if($('[name=payment_address]:checked').val() == 'existing'){
			validate_form($('#payment_existing form'));
		}
		else{
			validate_form($('#payment_new form'));
		}
	}
	else{
		submit_checkout_item($('#button-customer-information'));
	}
}

function is_valid(info_item){
	return info_item.hasClass('valid');
}

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
}

function info_page_loading(info_item){
	set_validation_status(info_item, 'validating', "<?= $text_info_validating;?> <img src='<?= HTTP_THEME_IMAGE . 'loading.gif';?>' alt='' />");
}

function info_page_received(info_item){
	handle_dependencies(info_item);
}

function not_ready(info_item){
	if(info_item.hasClass('loading') || info_item.hasClass('validating')){
		return true;
	}
	
	return false;
}

function load_info_item(info_item, route, callback){
	if(typeof info_item == 'string'){
		info_item = $('#' + info_item);
	}
	
	if(not_ready(info_item)) return;
	
	route = route || info_item.attr('route');
	
	set_validation_status(info_item, 'loading', '<?= $text_info_loading;?>');
	
	console.log('load_info_item ' + info_item.attr('id'));
	info_item.find('.info_content').load('index.php?route=' + route, {},
		function(){ 
			set_validation_status(info_item, '', '');
			if(typeof callback == 'function'){
				callback();
			}
			
			console.log('trigger loaded ' + info_item.attr('id'));
			info_item.trigger('loaded');
		}
	);
}

function validate_form(form, reload){
	var info_item = form.closest('.info_item');
	
	reload = reload || false;
	
	data = form.serialize();
	
	form_submit = form.find('input[type=submit]');
	
	if(form_submit){
		data += '&' + form_submit.attr('name') + '=' + form_submit.val();
	}
	
	data += '&async=1';
	
	$.ajax({
		url: form.attr('action'),
		type: 'post',
		data: data,
		dataType: 'json',
		beforeSend: function(){ info_page_loading(info_item); },
		success: function(json) {
			console.log('validated ' + info_item.attr('id'));
			if(handle_validation_response(info_item, json) && reload){
				console.log("loading from hre");
				load_info_item(info_item);
			}
		}
	});
}

function handle_validation_response(info_item, json){
	json = json || {};
	
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
		
		handle_dependencies(info_item);
		return true;
	}
	
	return false;
}

function handle_dependencies(info_item){
	switch(info_item.attr('id')){
		case 'shipping_address':
			load_info_item('shipping_method');
			break;
		case 'payment_address':
			load_info_item('payment_method');
			break
		default:
			break;
	}
}
//--></script>

<script type="text/javascript">//<!--
<? if($guest_checkout) {?>

$('#guest_information').find('input, select, textarea').live('keyup change', function(){
	if($(this).closest('.info_item').hasClass('valid')){
		set_validation_status($(this).closest('.info_item'), '', '');
	}
});

<? } else { ?>

$('#shipping_new input[type=submit], #payment_new input[type=submit], #add_new_address input[type=submit]').live('click', function(){
	validate_form($(this).closest('form'), true);
	
	return false;
});
<? } ?>

$('#add_comment textarea').live('keyup', function(){
	if($(this).closest('.info_item').hasClass('valid')){
		set_validation_status($(this).closest('.info_item'), '', '');
	}
});

$('#shipping_address').on('loaded', function(){
	sa_form = $('#shipping_existing form');
	if(sa_form.find('select[name=address_id]').val()){
		validate_form(sa_form);
	}
});

$('#payment_address').on('loaded', function(){
	sa_form = $('#payment_existing form');
	if(sa_form.find('select[name=address_id]').val()){
		validate_form(sa_form);
	}
});

$('#shipping_method, #payment_method').on('loaded', function(){
	console.log('loaded ' + $(this).attr('id'));
	sa_form = $(this).find('form');
	
	if(sa_form.find(':checked').val()){
		validate_form(sa_form);
	}
});

$('.info_item').trigger('loaded');
//--></script>