//mouse wheel enable for jquery
(function(a){function d(b){var c=b||window.event,d=[].slice.call(arguments,1),e=0,f=!0,g=0,h=0;return b=a.event.fix(c),b.type="mousewheel",c.wheelDelta&&(e=c.wheelDelta/120),c.detail&&(e=-c.detail/3),h=e,c.axis!==undefined&&c.axis===c.HORIZONTAL_AXIS&&(h=0,g=-1*e),c.wheelDeltaY!==undefined&&(h=c.wheelDeltaY/120),c.wheelDeltaX!==undefined&&(g=-1*c.wheelDeltaX/120),d.unshift(b,e,g,h),(a.event.dispatch||a.event.handle).apply(this,d)}var b=["DOMMouseScroll","mousewheel"];if(a.event.fixHooks)for(var c=b.length;c;)a.event.fixHooks[b[--c]]=a.event.mouseHooks;a.event.special.mousewheel={setup:function(){if(this.addEventListener)for(var a=b.length;a;)this.addEventListener(b[--a],d,!1);else this.onmousewheel=d},teardown:function(){if(this.removeEventListener)for(var a=b.length;a;)this.removeEventListener(b[--a],d,!1);else this.onmousewheel=null}},a.fn.extend({mousewheel:function(a){return a?this.bind("mousewheel",a):this.trigger("mousewheel")},unmousewheel:function(a){return this.unbind("mousewheel",a)}})})(jQuery)

function show_msg(type, html, append){
	append = append || false;
	
	if (!append) {
		$('.message_box, .warning, .success, .notify').remove();
	}
	
	var notify = $('#notification').show();

	notify.append('<div class="message_box ' + type + '" style="display: none;">' + html + '<span class="close"></span></div>');
	$('.message_box').fadeIn('slow');
	notify.appendTo($('body'));
	update_floating_window();
	$('.message_box .close').click(function(){$(this).parent().remove();});
	$(window).scroll(update_floating_window);
}

function show_msgs(data){
	clear_msgs();
	
	for (var m in data) {
		if (typeof data[m] == 'object') {
			msg = '';
			
			for (var m2 in data[m]) {
				msg += (msg ? '<br />' : '') + data[m][m2]; 
			}
			
			show_msg(m + ' ' + m2, msg, true);
		}
		else{
			show_msg(m, data[m], true);
		}
	}
}

function clear_msgs(){
	$('.message_box').remove();
}

function update_floating_window(){
	var notify = $('#notification');
	var b = $(window);
	var top = b.scrollTop() + 25;
	notify.css({top: top});
}

function addToCart(product_id, quantity) {
	quantity = typeof(quantity) != 'undefined' ? quantity : 1;

	$.ajax({
		url: 'cart/cart/add',
		type: 'post',
		data: 'product_id=' + product_id + '&quantity=' + quantity,
		dataType: 'json',
		success: function(json) {
			$('.success, .warning, .attention, .information, .error').remove();
			
			if (json['redirect']) {
				location = json['redirect'];
			}
			
			if (json['success']) {
				var notify = json['success'] + '<span class="close"></span>';
				show_msg('success', notify);
				
				$('#cart-total').html(json['total']);
			}
		}
	});
}
function addToWishList(product_id){
	$.ajax({
		url: 'account/wishlist/add',
		type: 'post',
		data: 'product_id=' + product_id,
		dataType: 'json',
		success: function(json) {
			$('.success, .warning, .attention, .information').remove();
			if (json['success']) {
				show_msg('success', json['success']);
				$('#wishlist-total').html(json['total']);
			}
		}
	});
}

function addToCompare(product_id) {
	$.ajax({
		url: 'product/compare/add',
		type: 'post',
		data: 'product_id=' + product_id,
		dataType: 'json',
		success: function(json) {
			$('.success, .warning, .attention, .information').remove();
						
			if (json['success']) {
				show_msg('success', notify);
				$('#compare-total').html(json['total']);
			}
		}
	});
}

function scroll_to(dest, duration, context){
	duration = duration === 0 ? 0 : (duration || 400);
	context = context || $('body');
	if(typeof dest == 'string') dest = $(dest);
	
	if(!dest.length) return;
	
	new_top = dest.offset().top;
	
	max = context.height() - $(window).height();
	
	if(new_top == context.scrollTop()) return;
	
	if(new_top > context.scrollTop()){
		do_scroll = context.scrollTop() < max;
	}
	else{
		do_scroll = context.scrollTop() > 0;
	}
	
	if(do_scroll){
		context.animate({scrollTop: new_top},duration);
	}
}

function submit_block(type, url, form){
	$.post(url, form.serialize(),
      function(json){
         if(json['error']){
            show_msg('warning', json['error']);
            $('body').trigger(type + '_error', json);
         }
         else if(json['success']){
            show_msg('success', json['success']);
            $('body').trigger(type + '_success', json);
         }
      }
      ,'json');
}

function load_block(context, route, data){
	data = data || {};
	
	context.load(route, data, function(){context.trigger('loaded')});
}

function handle_ajax_error(jqXHR, status){
	if(jqXHR.responseText.length < 1000){
		msg = jqXHR.responseText;
	}else{
		msg = '';
	}
	
	show_msg('warning', 'There was an error with the ajax request. ' + msg);
	
	if(console && console.log){
		console.log('Ajax Error: ' + jqXHR.responseText);
	}
}

console = console || {};
console.log = console.log || function(msg){};
console.dir = console.dir || function(obj){};