//mouse wheel enable for jquery
(function(a){function d(b){var c=b||window.event,d=[].slice.call(arguments,1),e=0,f=!0,g=0,h=0;return b=a.event.fix(c),b.type="mousewheel",c.wheelDelta&&(e=c.wheelDelta/120),c.detail&&(e=-c.detail/3),h=e,c.axis!==undefined&&c.axis===c.HORIZONTAL_AXIS&&(h=0,g=-1*e),c.wheelDeltaY!==undefined&&(h=c.wheelDeltaY/120),c.wheelDeltaX!==undefined&&(g=-1*c.wheelDeltaX/120),d.unshift(b,e,g,h),(a.event.dispatch||a.event.handle).apply(this,d)}var b=["DOMMouseScroll","mousewheel"];if(a.event.fixHooks)for(var c=b.length;c;)a.event.fixHooks[b[--c]]=a.event.mouseHooks;a.event.special.mousewheel={setup:function(){if(this.addEventListener)for(var a=b.length;a;)this.addEventListener(b[--a],d,!1);else this.onmousewheel=d},teardown:function(){if(this.removeEventListener)for(var a=b.length;a;)this.removeEventListener(b[--a],d,!1);else this.onmousewheel=null}},a.fn.extend({mousewheel:function(a){return a?this.bind("mousewheel",a):this.trigger("mousewheel")},unmousewheel:function(a){return this.unbind("mousewheel",a)}})})(jQuery)


$(document).ready(function() {
	/* Making hover work in IE */
	$('.featured_product_clickable,.featured_menu_link, .product_section a, #featured_pager a,#footer_nav li').hover(function(){$(this).addClass('hover');},function(){$(this).removeClass('hover')});
	
	/* Search */
	$('.button-search').bind('click', function() {
		url = $('base').attr('href') + 'index.php?route=product/search';
				 
		var filter_name = $('input[name=\'filter_name\']').attr('value');
		
		if (filter_name) {
			url += '&filter_name=' + encodeURIComponent(filter_name);
		}
		
		location = url;
	});
	
	$('#header input[name=\'filter_name\']').bind('keydown', function(e) {
		if (e.keyCode == 13) {
			url = $('base').attr('href') + 'index.php?route=product/search';
			 
			var filter_name = $('input[name=\'filter_name\']').attr('value');
			
			if (filter_name) {
				url += '&filter_name=' + encodeURIComponent(filter_name);
			}
			
			location = url;
		}
	});
	
	/* Ajax Cart */
	$('#cart > .heading a').live('click', function() {
		$('#cart').addClass('active');
		
		$('#cart').load('index.php?route=module/cart #cart > *');
		
		$('#cart').live('mouseleave', function() {
			$(this).removeClass('active');
		});
	});
	
	/* Mega Menu */
	$('#menu ul > li > a + div').each(function(index, element) {
		// IE6 & IE7 Fixes
		if ($.browser.msie && ($.browser.version == 7 || $.browser.version == 6)) {
			var category = $(element).find('a');
			var columns = $(element).find('ul').length;
			
			$(element).css('width', (columns * 143) + 'px');
			$(element).find('ul').css('float', 'left');
		}
		
		var menu = $('#menu').offset();
		var dropdown = $(this).parent().offset();
		
		i = (dropdown.left + $(this).outerWidth()) - (menu.left + $('#menu').outerWidth());
		
		if (i > 0) {
			$(this).css('margin-left', '-' + (i + 5) + 'px');
		}
	});

	// IE6 & IE7 Fixes
	if ($.browser.msie) {
		if ($.browser.version <= 6) {
			$('#column-left + #column-right + #content, #column-left + #content').css('margin-left', '195px');
			
			$('#column-right + #content').css('margin-right', '195px');
		
			$('.box-category ul li a.active + ul').css('display', 'block');
		}
		
		if ($.browser.version <= 7) {
			$('#menu > ul > li').bind('mouseover', function() {
				$(this).addClass('active');
			});
				
			$('#menu > ul > li').bind('mouseout', function() {
				$(this).removeClass('active');
			});
		}
	}
	
	$('.success img, .warning img, .attention img, .notify img, .information img').live('click', function() {
		$(this).parent().fadeOut('slow', function() {
			$(this).remove();
		});
	});
	
	$('form input').keydown(function(e) {
		if (e.keyCode == 13) {
			$(this).closest('form').submit();
		}
	});
	
	if($('.flash_countdown').length > 0)
			countdown();
});


/*COUNTDOWN FOR FLASH SALES AJAX **/
function countdown(){
	var cd =$('.flash_countdown');
	if(cd.length == 0)return;
	flashsales = {}
	cd.each(function(i,e){
		flashsales[i] = {};
		flashsales[i]['id'] = $(e).attr('id');
		flashsales[i]['flash_id'] = $(e).attr('flashid');
		flashsales[i]['type'] = $(e).attr('type') || 'long';
		flashsales[i]['msg_start'] = $(e).attr('msg_start') || '';
	});
	$.post('index.php?route=sales/flashsale/ajax_countdown', {flashsales: flashsales},
		function(json){
			if(!json)return;
			for(var i=0;i<json.length;i++){
				context = $('.flash_countdown#'+json[i].id);
				context.html(json[i].countdown);
				if(!json[i].countdown || json[i].countdown.match(/ended/)){
					callback = context.attr('callback');
					if(typeof window[callback] == 'function')
						window[callback](context,'ended');
				}
			}
		},'json');
		setTimeout(countdown,1000);
}
 
 
function toggleDD(dd, show){
	dd = $(dd).find('ul:first');
	if(dd.is(':animated'))return;

	show = typeof show == 'boolean' ? show : !dd.is(':visible');
	
	dd.data('orig_height', dd.height());
	var orig_height, a_height, complete;
	if(show){
		a_height = dd.height();
		start_height = 0;
		complete = null;
		dd.css('z-index', 100);
		dd.show();
		
		active = $("<input class='the_focus' type='text' style='position:absolute;left:-9999px' />");
		active.appendTo(dd);
		active.focus().blur(function(){if($('.select_dd li:active').length<1)toggleDD(dd, false);});
	}
	else{
		a_height = 0;
		start_height = dd.height();
		complete = function(){$(this).height($(this).data('orig_height')).css('z-index',0).hide();};
		$('.the_focus').remove();
	}
	
	dd.height(start_height).animate({height: a_height}, {duration:200, complete: complete });
}
function select_menu_item(item){
	$(item).closest('select_dd').find('input').val($(item).attr('data'));
	$(item).closest('select_dd').find('.current_selection').html($(item).html());
}

function show_msg(type, html){
	$('.warning, .success, .notify').remove();
	var notify = $('#notification').show();

	notify.html('<div class="message_box ' + type + '" style="display: none;">' + html + '<span class="close"></span></div>');
	$('.'+type).fadeIn('slow');
	notify.appendTo($('body'));
	update_floating_window();
	$('.message_box .close').click(function(){$(this).parent().remove();});
	$(window).scroll(update_floating_window);
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
		url: 'index.php?route=cart/cart/add',
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
		url: 'index.php?route=account/wishlist/add',
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
		url: 'index.php?route=product/compare/add',
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
	
	context.load('index.php?route=' + route, data, function(){context.trigger('loaded')});
}

function handle_ajax_error(jqXHR, status){
	if(jqXHR.responseText.length < 1000){
		msg = jqXHR.responseText;
	}else{
		msg = '';
	}
	
	show_msg('warning', '<?= $error_ajax_response; ?>' + msg);
	
	if(console && console.log){
		console.log('validate_form(): Ajax Error: ' + jqXHR.responseText);
	}
}

console = console || {};
console.log = console.log || function(msg){};
console.dir = console.dir || function(obj){};