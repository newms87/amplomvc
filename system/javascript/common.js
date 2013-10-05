function syncload(s) {
	s = $.ac_vars.site_url + s;

	$.ajax({
		async: false,
		cache: true,
		url: s,
		error: function (e) {
			$.error('Failed to load script from ' + s)
		},
		dataType: 'script',
	});
}

//Load jQuery Plugins On Call
$.fn.codemirror = function (params) {
	$.fn.codemirror = null;
	$('head').append('<link rel="stylesheet" type="text/css" href="system/javascript/codemirror/lib/codemirror.css" />');
	$('head').append('<link rel="stylesheet" type="text/css" href="system/javascript/codemirror/ui/css/codemirror-ui.css" />');
	syncload('system/javascript/codemirror/lib/codemirror.js');

	params = $.extend({},{
		tabSize: 3,
		indentWithTabs: true,
		indentUnit: 3
	}, params);


	var depends = {};
	var addons = {'edit': {'matchbrackets':1}, 'search': {'searchcursor':1}};
	params.matchBrackets = true;

	switch(params.mode) {
		case 'html':
		case 'htmlmixed':
		case 'php':
			params.mode = 'php';
			depends = {'php':1, 'htmlmixed':1, 'css':1, 'clike':1, 'javascript':1,'xml':1};
			break;

		case 'javascript':
		case 'js':
			params.mode = 'javascript';
			depends = {'javascript':1};

		case 'css':
			depends = {'css':1};
			break;
	}

	for (d in depends) {
		syncload('system/javascript/codemirror/mode/' + d + '/' + d + '.js');
	}

	for (a in addons) {
		for (f in addons[a]) {
			syncload('system/javascript/codemirror/addon/' + a + '/' + f + '.js');
		}
	}

	//CodeMirrorUI
	syncload('system/javascript/codemirror/ui/js/codemirror-ui.js');
	uiOptions = {
		searchMode: 'popup',
		'path': 'system/javascript/codemirror/ui/js/'
	}

	this.each(function(i,e){
		new CodeMirrorUI(e, uiOptions, params);
	});
}

$.fn.nivoSlider = function (params) {
	$.fn.nivoSlider = null;
	syncload('system/javascript/jquery/nivo_slider/nivo-slider.js');
	if (this.nivoSlider) this.nivoSlider(params);
}

$.ac_template = $.fn.ac_template = function (name, action, data) {
	$.ac_template = $.fn.ac_template = null;
	syncload('system/javascript/jquery/ac_template.js');
	if (this.ac_template) this.ac_template(name, action, data);
}

$.fn.jqzoom = function (params) {
	$.fn.jqzoom = null;
	syncload('system/javascript/jquery/jqzoom/jqzoom.js');
	if (this.jqzoom) this.jqzoom(params);
}

$.colorbox = $.fn.colorbox = function (params, loadonly) {
	$.colorbox = $.fn.colorbox = null;
	syncload('system/javascript/jquery/colorbox/colorbox.js');
	if (this.colorbox && !loadonly) this.colorbox(params);
}

//Add the date/time picker to the elements with the special classes
$.ac_datepicker = function (params) {
	$('.datepicker, .timepicker, .datetimepicker').ac_datepicker(params);
}

$.fn.ac_datepicker = function (params) {
	if (!$.ui.timepicker) {
		var selector = this;
		$.ajaxSetup({cache: true});
		$.getScript($.ac_vars.site_url + 'system/javascript/jquery/ui/datetimepicker.js', function () {
			selector.ac_datepicker(params);
		});
		return;
	}

	params = $.extend({}, {
		type: null,
		dateFormat: 'yy-mm-dd',
		timeFormat: 'h:m',
	}, params);

	return this.each(function (i, e) {
		type = params.type ||
			$(e).hasClass('datepicker') ? 'datepicker' :
			$(e).hasClass('timepicker') ? 'timepicker' : 'datetimepicker';

		$(e)[type](params);
	});
}

$.fn.ac_radio = function () {
	var radiolist = this;

	this.find('input[type=radio]').hide();

	this.each(function (i, e) {
		if ($(e).find('input[type=radio]:checked').length) {
			$(e).addClass('checked');
		}
	});

	this.click(function () {
		radiolist.removeClass('checked').find('input[type=radio]').prop('checked', false);
		$(this).addClass("checked").find('input[type=radio]').prop('checked', true);
	});
}

//Apply a filter form to the URL
$.fn.apply_filter = function (url) {
	filter_list = this.find('[name]')
		.filter(function (index) {
			return $(this).val() !== '';
		});

	if (filter_list.length) {
		url += (url.search(/\?/) ? '&' : '?') + filter_list.serialize();
	}

	location = url;
}

//A jQuery Plugin to update the sort orders columns (or any column needing to be indexed)
$.fn.update_index = function (column) {
	column = column || '.sort_order';

	return this.each(function (i, ele) {
		count = 0;
		$(ele).find(column).each(function (i, e) {
			$(e).val(count++);
		});
	});
}

$.fn.flash_highlight = function () {
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

	ele.animate({'background-color': 'rgba(255,255,85,1)'}, {duration: 300, always: function () {
		ele.animate({'background-color': 'rgba(255,255,255,0)'}, {duration: 700, always: function () {
			ele.remove()
		}});
	}});

	return this;
}

$.fn.tabs = function (callback) {
	var selector = this;
	this.each(function (i, obj) {
		var obj = $(obj);

		$(obj.attr('href')).hide();

		obj.click(function () {
			selector.removeClass('selected');

			selector.each(function (i, element) {
				$($(element).attr('href')).hide();
			});

			obj.addClass('selected');

			content = $(obj.attr('href')).show();

			if (typeof callback === 'function') {
				callback(content.attr('id'), obj, content);
			}
			return false;
		});

		var tab_name = obj.find('.tab_name');

		if (tab_name.length) {
			$(obj.attr('href')).find('.tab_name').keyup(function(){
				tab_name.html($(this).val());
			});
		}
	});

	this.show().first().click();

	return this;
};

function colorbox(context, data) {
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
	};

	if (typeof data == 'object') {
		for (var d in data) {
			defaults[d] = data[d];
		}
	}

	$.colorbox(defaults);

	return false;
}

$.fn.display_error = function (msg, id) {
	if (id && $('#' + id).length) return;

	this.after('<div class="display_error"' + (id ? 'id="' + id + '"' : '') + '>' + msg + '</div>');
}

$.clear_errors = function (id) {
	if (id) {
		$('#' + id).remove();
	} else {
		$('.display_error').remove();
	}
}

$.fn.fade_post = function (url, data, callback, dataType) {
	context = this;

	context.stop().fadeOut(300);

	context.after($.loading);

	$.post(url, data, function (data) {
		context.fadeIn(0);
		$.loading('stop');

		if (typeof callback === 'function') {
			callback(data);
		}
	});

	return this;
}

$.loading = function (params) {
	if (params === 'stop') {
		$('.loader').remove();
		return;
	}

	params = $.extend({}, {
		dots: 8,
		width: null,
		height: null,
		animations: 'bounce, fadecolor'
	}, params);

	loader = $('<div class="loader">' + '<div class="loader_item"></div>'.repeat(params.dots) + '</div>');

	loader.children('.loader_item').each(function (i, e) {
		$(e).attr('style', '-webkit-animation-name: ' + params.animations + '; animation-name: ' + params.animations);
	});

	if (params.width) {
		loader.width(params.width);
	}

	if (params.height) {
		loader.height(params.height);
	}

	return loader[0].outerHTML;
}

$.fn.loading = function (params) {
	return this.append($.loading(params));
}

String.prototype.repeat = function (times) {
	return (new Array(times + 1)).join(this);
};

//Utility Functions
String.prototype.str_replace = function (find, replace) {
	var str = this;
	for (var i = 0; i < find.length; i++) {
		str = str.replace(find[i], replace[i]);
	}
	return str;
};

function getQueryString(key, defaultValue) {
	if (defaultValue == null) defaultValue = "";
	key = key.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
	var regex = new RegExp("[\\?&]" + key + "=([^&#]*)");
	var qs = regex.exec(window.location.href);
	if (qs == null)
		return defaultValue;
	else
		return qs[1];
}

function currency_format(number, params) {
	params = $.extend({}, {
		symbol_left: $.ac_vars.currency.symbol_left,
		symbol_right: $.ac_vars.currency.symbol_right,
		decimals: $.ac_vars.currency.decimals,
		dec_point: $.ac_vars.currency.decimal_point,
		thousands_sep: $.ac_vars.currency.thousands_sep,
		neg: '-',
		pos: '+'
	}, params);

	str = number_format(Math.abs(number), params.decimals, params.dec_point, params.thousands_sep);

	return (number < 0 ? params.neg : params.pos) + params.symbol_left + str + params.symbol_right;
}

function number_format(number, decimals, dec_point, thousands_sep) {
	number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
	var n = !isFinite(+number) ? 0 : +number,
		prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
		sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
		dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
		s = '',
		toFixedFix = function (n, prec) {
			var k = Math.pow(10, prec);
			return '' + Math.round(n * k) / k;
		};
	// Fix for IE parseFloat(0.55).toFixed(0) = 0;
	s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
	if (s[0].length > 3) {
		s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	}
	if ((s[1] || '').length < prec) {
		s[1] = s[1] || '';
		s[1] += new Array(prec - s[1].length + 1).join('0');
	}
	return s.join(dec);
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
	var result, decode = options.raw ? function (s) {
		return s;
	} : decodeURIComponent;
	return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? decode(result[1]) : null;
};

$(document).ready(function () {
	$('.ui-autocomplete-input').on("autocompleteselect", function (e, ui) {
		if (!ui.item.value && ui.item.href) {
			window.open(ui.item.href);
		}
	});

	$('form input').keydown(function (e) {
		if (e.keyCode == 13) {
			$(this).closest('form').submit();
		}
	});

	if ($('.colorbox').click(colorbox).length) {
		$.colorbox(null, true); //load colorbox script
	}

	//AC Checkbox (No IE8)
	if ($('body.IE8').length === 0) {
		$('.ac_checkbox').each(function(i,e){
			var div = $('<div class="ac_checkbox"></div>');
			var cb = $(e);

			div.toggleClass("checked", cb.prop('checked'));

			cb.after(div).removeClass('ac_checkbox');
			$(e).appendTo(div);

			div.click(function(){
				cb.prop('checked', !cb.prop('checked'));
				div.toggleClass("checked", cb.prop('checked'));
			});
		});
	}
});
