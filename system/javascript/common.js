function syncload(s) {
	s = $.ac_vars.site_url + s;
	
	$.ajax({
		async:false,
		cache:true,
		url: s,
		error: function(e){$.error('Failed to load script from ' + s)},
		dataType:'script',
	});
}

//Load jQuery Plugins On Call
$.ac_template = $.fn.ac_template = function(name, action, data) {
	$.ac_template = $.fn.ac_template = null;
	syncload('system/javascript/jquery/ac_template.js');
	if (this.ac_template) this.ac_template(name, action, data);
}

$.fn.jqzoom = function(params){
	$.fn.jqzoom = null;
	syncload('system/javascript/jquery/jqzoom/jqzoom.js');
	if (this.jqzoom) this.jqzoom(params);
}

$.colorbox = $.fn.colorbox = function(params, loadonly){
	$.colorbox = $.fn.colorbox = null;
	syncload('system/javascript/jquery/colorbox/colorbox.js');
	if (this.colorbox && !loadonly) this.colorbox(params);
}

//Add the date/time picker to the elements with the special classes
$.ac_datepicker = function(params) {
	$('.datepicker, .timepicker, .datetimepicker').ac_datepicker(params);
}

$.fn.ac_datepicker = function(params) {
	if (!$.ui.timepicker) {
		var selector = this;
		$.ajaxSetup({cache: true});
		$.getScript($.ac_vars.site_url + 'system/javascript/jquery/ui/datetimepicker.js', function(){selector.ac_datepicker(params);});
		return;
	}
	
	params = $.extend({},{
			type: null,
			dateFormat: 'yy-mm-dd',
			timeFormat: 'h:m',
		}, params);
		
	return this.each(function(i,e) {
		type = params.type ||
			$(e).hasClass('datepicker') ? 'datepicker' :
			$(e).hasClass('timepicker') ? 'timepicker' : 'datetimepicker';
		
		$(e)[type](params);
	});
}

//Apply a filter form to the URL
$.fn.apply_filter = function(url) {
	filter_list = this.find('[name]')
		.filter(function(index){ return $(this).val() !== ''; });
	
	if (filter_list.length) {
		url += (url.search(/\?/) ? '&' : '?') + filter_list.serialize();
	}
	
	location = url;
}

//A jQuery Plugin to update the sort orders columns (or any column needing to be indexed)
$.fn.update_index = function(column) {
	column = column || '.sort_order';
	
	return this.each(function(i,ele){
		count = 0;
		$(ele).find(column).each(function(i,e){
			$(e).val(count++);
		});
	});
}

$.fn.flash_highlight = function() {
	pos = this.offset();
	
	var ele = $('<div />');
	
	ele.css({
			background: 'rgba(255,255,255,0)',
			position: 'absolute',
			top: pos.top,
			left: pos.left,
			opacity: .8,
			'z-index': 10000
		})
		.width($(this).width())
		.height($(this).height());
	
	$('body').css({position: 'relative'});
	$('body').append(ele);
	
	ele.animate({'background-color': 'rgba(255,255,85,1)'}, {duration: 300, always: function(){
		ele.animate({'background-color': 'rgba(255,255,255,0)'}, {duration: 700, always: function(){ele.remove()}});
	}});
	
	return this;
}

$.fn.tabs = function() {
	var selector = this;
	this.each(function(i, obj) {
		var obj = $(obj);
		
		$(obj.attr('href')).hide();
		
		obj.click(function() {
			selector.removeClass('selected');
			
			selector.each(function(i, element) {
				$($(element).attr('href')).hide();
			});
			
			obj.addClass('selected');
			
			$(obj.attr('href')).show();
			
			return false;
		});
	});

	this.show().first().click();
	
	return this;
};

function colorbox(context, data){
	context = context || $(this);
	
	if (context.attr('href')) {
		href = context.attr('href');
		html = null;
	} else {
		href = null
		html = context.html();
	}
	
	defaults = {
		overlayClose: true,
		opacity: 0.5,
		width: '60%',
		height: '80%',
		href: href,
		html: html,
		onCleanup: function(){ $.colorbox.close(); },
		onClosed: function(){ $.colorbox.remove(); },
	};
	
	if (typeof data == 'object') {
		for (var d in data) {
			defaults[d] = data[d];
		}
	}
	
	$.colorbox(defaults);
	
	return false;
}

//Utility Functions
String.prototype.str_replace = function(find, replace) {
  var str = this;
  for (var i = 0; i < find.length; i++) {
    str = str.replace(find[i], replace[i]);
  }
  return str;
};

function getQueryString(key, defaultValue) {
	if(defaultValue == null) defaultValue = "";
	key = key.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
	var regex = new RegExp("[\\?&]" + key + "=([^&#]*)");
	var qs = regex.exec(window.location.href);
	if(qs == null)
		return defaultValue;
	else
		return qs[1];
}

$.cookie = function (key, value, options) {
    if (arguments.length > 1 && (value === null || typeof value !== "object")) {
        options = options || {};

        if (value === null) {
            options.expires = -1;
        }

        if (typeof options.expires === 'number') {
            var days = options.expires, t = options.expires = new Date();
            t.setDate(t.getDate() + days);
        }

        return (document.cookie = [
            encodeURIComponent(key), '=',
            options.raw ? String(value) : encodeURIComponent(String(value)),
            options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
            options.path ? '; path=' + options.path : '',
            options.domain ? '; domain=' + options.domain : '',
            options.secure ? '; secure' : ''
        ].join(''));
    }

    // key and possibly options given, get cookie...
    options = value || {};
    var result, decode = options.raw ? function (s) { return s; } : decodeURIComponent;
    return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? decode(result[1]) : null;
};

$(document).ready(function() {
	$('form input').keydown(function(e) {
		if (e.keyCode == 13) {
			$(this).closest('form').submit();
		}
	});
	
	if ($('.colorbox').click(colorbox).length) {
		$.colorbox(null, true); //load colorbox script
	}
});
