//Ensures all ajax requests are submitted as an ajax URL
String.prototype.ajaxurl = function () {
	return this.match(/\?/) ? this + '&ajax=1' : this + '?ajax=1';
}

String.prototype.toSlug = function () {
	return this.toLowerCase().replace(/\s/, '-').replace(/[^a-z0-9-_]/, '');
}

$._ajax = $.ajax;

$.ajax = function (params, p2) {
	params.url = (params.url ? params.url : document.URL).ajaxurl();
	return $._ajax(params, p2);
}

//Load synchronously
function syncload(s) {
	if (!s.match(/^https?:\/\//)) {
		s = $.ac_vars.url_site + s;
	}

	$.ajax({
		async: false,
		cache: true,
		url: s,
		error: function (e) {
			$.error('Failed to load script from ' + s)
		},
		dataType: 'script'
	});
}

//Load jQuery Plugins On Call
$.fn.codemirror = function (params) {
	if (!$.fn.codemirror.once) {
		$('head').append('<link rel="stylesheet" type="text/css" href="system/resources/js/codemirror/lib/codemirror.css" />');
		$('head').append('<link rel="stylesheet" type="text/css" href="system/resources/js/codemirror/ui/css/codemirror-ui.css" />');
		syncload('system/resources/js/codemirror/lib/codemirror.js');

		//CodeMirrorUI
		syncload('system/resources/js/codemirror/ui/js/codemirror-ui.js');
		uiOptions = {
			searchMode: 'popup',
			path: 'system/resources/js/codemirror/ui/js/',
			imagePath: 'system/resources/js/codemirror/ui/images/silk'
		}

		$.fn.codemirror.once = true;
	}

	params = $.extend({}, {
		tabSize: 3,
		indentWithTabs: true,
		lineNumbers: false,
		indentUnit: 3
	}, params);

	var depends = {};
	var addons = {'edit': {'matchbrackets': 1}, 'search': {'searchcursor': 1}};
	params.matchBrackets = true;

	switch (params.mode) {
		case 'html':
		case 'htmlmixed':
		case 'php':
			params.mode = 'php';
			depends = {'php': 1, 'htmlmixed': 1, 'css': 1, 'clike': 1, 'javascript': 1, 'xml': 1};
			break;

		case 'javascript':
		case 'js':
			params.mode = 'javascript';
			depends = {'javascript': 1};

		case 'css':
			depends = {'css': 1};
			break;
	}

	for (d in depends) {
		syncload('system/resources/js/codemirror/mode/' + d + '/' + d + '.js');
	}

	for (a in addons) {
		for (f in addons[a]) {
			syncload('system/resources/js/codemirror/addon/' + a + '/' + f + '.js');
		}
	}

	return this.each(function (i, e) {
		e.cm_editor = new CodeMirrorUI(e, uiOptions, params);
	});
}

$.ac_template = $.fn.ac_template = function (name, action, data) {
	$.ac_template = $.fn.ac_template = null;
	syncload('system/resources/js/jquery/ac_template.js');
	if (this.ac_template) this.ac_template(name, action, data);
}

$.fn.jqzoom = function (params) {
	$.fn.jqzoom = null;
	syncload('system/resources/js/jquery/jqzoom/jqzoom.js');
	if (this.jqzoom) this.jqzoom(params);
}

$.colorbox = $.fn.colorbox = function (params, loadonly) {
	$.colorbox = $.fn.colorbox = null;
	syncload('system/resources/js/jquery/colorbox/colorbox.js');
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
		$.getScript($.ac_vars.url_site + 'system/resources/js/jquery/ui/datetimepicker.js', function () {
			selector.ac_datepicker(params);
		});
		return;
	}

	params = $.extend({}, {
		type: null,
		dateFormat: 'yy-mm-dd',
		timeFormat: 'HH:mm',
	}, params);

	return this.each(function (i, e) {
		type = params.type ||
		$(e).hasClass('datepicker') ? 'datepicker' :
			$(e).hasClass('timepicker') ? 'timepicker' : 'datetimepicker';

		$(e)[type](params);
	});
}

$.fn.ac_radio = function (params) {
	params = $.extend({}, {
		elements: $(this).children().not('.noradio')
	}, params);

	this.find('input[type=radio]').hide();

	params.elements.each(function (i, e) {
		if ($(e).find('input[type=radio]:checked').length) {
			$(e).addClass('checked');
		}
	})

		.click(function () {
			params.elements.removeClass('checked').find('input[type=radio]').prop('checked', false);
			$(this).addClass("checked").find('input[type=radio]').prop('checked', true);
		});

	return this;
}

$.fn.ac_checklist = function (params) {
	params = $.extend({}, {
		elements: $(this).children().not('.nocheck'),
		change: null
	}, params);

	this.find('input[type=checkbox]').hide();

	params.elements.each(function (i, e) {
		if ($(e).find('input[type=checkbox]:checked').length) {
			$(e).addClass('checked');
		}
	})

		.click(function () {
			if ($(this).hasClass('checked')) {
				$(this).removeClass('checked');
				$(this).find('input[type=checkbox]').prop('checked', false).change();
			} else {
				$(this).addClass('checked');
				$(this).find('input[type=checkbox]').prop('checked', true).change();
			}

			if (typeof params.change === 'function') {
				params.change($(this), $(this).hasClass('checked'));
			}
		});

	return this;
}

$.fn.ac_slidelist = function (params) {
	var allowed = 'div, a, span';
	this.each(function (i, e) {
		var box = $('<div class="slidelistbox" />');
		var slider = $('<div class="slidelist" />').width($(e).width()).append($(e).children(allowed));

		if (params.x_dir < 0) {
			box.addClass('right');
		}
		$(e).append(box.append(slider));

		var items = slider.children(':not(.add_slide)').addClass('slideitem');
		var add_slide = slider.children('.add_slide');

		params = $.extend(true, {}, {
			min_space_y: 10,
			min_space_x: 0,
			pad_y: 0,
			pad_x: 0,
			add_slide: {x: 0, y: null, xout: 0, yout: 0},
			item_height: items.first().outerHeight(true),
			item_width: items.first().outerWidth(true),
			max_rows: 4,
			x_dir: 1,
			hover_in_delay: 0,
			hover_out_delay: 0
		}, params);

		if (params.add_slide.y === null) {
			params.add_slide.y = params.item_height * .4;
		}

		var rows = Math.min(items.length - 1, params.max_rows);
		var cols = Math.floor((items.length - 1) / rows);
		var item_height = params.item_height;
		var item_width = params.item_width;
		var max_height = (params.pad_y + item_height) * rows;
		var min_height = rows * params.min_space_y;
		var max_width = (params.pad_x + item_width) * cols;
		var min_width = cols * params.min_space_x;

		slider.css({
			'margin-top': item_height
		});

		box.width(params.item_width);

		var sort = function () {
			slider.children('.slideitem:first').css({
				top: -item_height,
				bottom: 'auto',
				left: params.x_dir >= 0 ? 0 : 'auto',
				right: params.x_dir < 0 ? 0 : 'auto',
				'z-index': items.length
			});

			slider.children('.slideitem').not(':first').each(function (i, e) {
				y_perc = (rows - (i % rows) - 1) / rows * 100;
				x_perc = Math.floor(i / rows) / cols * 100;

				left = params.x_dir >= 0 ? x_perc + '%' : 'auto';
				right = params.x_dir < 0 ? x_perc + '%' : 'auto';

				$(e).css({
					top: 'auto',
					bottom: y_perc + '%',
					left: left,
					right: right,
					'z-index': items.length - i - 1
				});
			})
		}

		function hoverIn() {
			slider.css({
				height: max_height,
				width: max_width
			})

			add_slide.css({
				bottom: (-params.add_slide.y / max_height * 100) + '%'
			});
		};

		function hoverOut() {
			slider.css({
				height: min_height,
				width: min_width
			})

			add_slide.css({
				bottom: ((params.add_slide.yout + min_height) / min_height) + '%'
			});
		};
		hoverOut();//call this for initial position

		slider.parent().hover(function () {
			setTimeout(hoverIn, params.hover_in_delay);
		}, function () {
			setTimeout(hoverOut, params.hover_out_delay);
		});

		slider.click(function () {
			slider.prepend(slider.children('.checked'));
			sort();
		}).click();
	});

	return this;
};

//Apply a filter form to the URL
$.fn.apply_filter = function (url) {
	filter_list = this.find('[name]')
		.filter(function (index) {
			return $(this).val() !== '';
		});

	if (filter_list.length) {
		url += (url.search(/\?/) ? '&' : '?') + filter_list.serialize();
	}

	return url;
}

$.fn.ac_msg = function (type, msg, prepend, replace) {
	if (typeof msg == 'object') {
		for (var m in msg) {
			this.ac_msg(type, msg[m], prepend, replace);
		}
		return this;
	}

	replace = replace || false;

	if (replace) {
		$('.message, .warning, .success, .notify').remove();
	}

	return this.each(function (i, e) {
		var box = $(e).find('.message.' + type);

		if (!box.length) {
			box = $('<div class="message ' + type + '" style="display: none;"><span class="close"></span></div>');
			box.fadeIn('slow').find('.close').click(function () {
				$(this).parent().remove();
			});
			if (prepend) {
				$(e).prepend(box);
			} else {
				$(e).append(box);
			}
		}

		box.prepend($('<div />').html(msg));
	});
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
	var $this = this;

	this.each(function (i, obj) {
		var obj = $(obj);

		$(obj.attr('href')).hide();

		obj.click(function () {
			$this.removeClass('selected');

			$this.each(function (i, e) {
				$($(e).attr('href')).hide();
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
			$(obj.attr('href')).find('.tab_name').keyup(function () {
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

$.fn.ac_errors = function (errors) {
	for (err in errors) {
		if (typeof errors[err] == 'object') {
			this.ac_errors(errors[err]);
			continue;
		}

		var ele = this.find('[name="' + err + '"]');

		if (!ele.length) {
			ele = $('#' + e);
		}

		if (!ele.length) {
			ele = $(e);
		}

		ele.after($("<span/>").addClass('error').html(errors[err]));
	}

	return this;
}

$.ac_errors = function (errors) {
	$('body').ac_errors(errors);
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
	}, dataType || null);

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

$.fn.postForm = function (callback, datatype, params) {
	$.post(this.attr('action'), this.serialize(), callback, datatype);
}

$.fn.ac_zoneselect = function (params, callback) {
	var $this = this;

	params = $.extend({}, {
		listen: null,
		allow_all: false,
		select: null,
		url: $.ac_vars.url_site + 'data/locale/load_zones'
	}, params);

	if (!params.listen) {
		throw "You must specify 'listen' in the parameters. This is the Country selector element";
	} else {
		params.listen = $(params.listen);
	}

	params.url = params.url.ajaxurl();

	if (params.allow_all) {
		params.url += '&allow_all';
	}

	if (params.select) {
		params.url += '&zone_id=' + params.select;
	}

	if (callback) {
		$this.success = callback;
	}

	params.listen.change(function () {
		var $cs = $(this);

		if (!$cs.val()) return;

		if ($this.children().length && $this.attr('data-country-id') == $this.val()) return;

		$this.attr('data-country-id', $cs.val());
		$this.attr('data-zone-id', $this.val() || $this.attr('data-zone-id') || 0);

		$this.load(params.url + '&country_id=' + $cs.val(), $this.success);
	});

	if ($this.children().length < 1 || !$this.val()) {
		params.listen.change();
	}

	return $this;
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
		$('.ac_checkbox').each(function (i, e) {
			var div = $('<div class="ac_checkbox"></div>');
			var cb = $(e);

			div.toggleClass("checked", cb.prop('checked'));

			cb.after(div).removeClass('ac_checkbox');
			$(e).appendTo(div);

			div.click(function () {
				cb.prop('checked', !cb.prop('checked'));
				div.toggleClass("checked", cb.prop('checked'));
			});
		});
	}
});


//Chrome Autofill disable hack
if (navigator.userAgent.toLowerCase().indexOf("chrome") >= 0) {
	$(window).load(function(){
		$('input:-webkit-autofill[autocomplete="off"]').each(function(){
			var $this = $(this);
			if (!$this.attr('value')) {
				$this.val('');
				setTimeout(function(){$this.val('');}, 200);
			}
		});
	});
}