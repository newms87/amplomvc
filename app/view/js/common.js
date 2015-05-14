//Similar to LESS screen sizing
var screen_width = (window.innerWidth > 0) ? window.innerWidth : screen.width;
var screen_lg = screen_width >= 1200,
	screen_md = screen_width >= 768 && screen_width < 1200,
	screen_sm = screen_width >= 480 && screen_width < 768,
	screen_xs = screen_width < 480;

Function.prototype.loop = function (time) {
	var fn = this;
	setTimeout(function () {
		fn() === false ? 0 : fn.loop(time)
	}, time);
}

String.prototype.toSlug = function (sep) {
	return this.toLowerCase().replace(/\s/, sep || '-').replace(/[^a-z0-9-_]/, '');
}

String.prototype.repeat = function (times) {
	return (new Array(times + 1)).join(this);
};

String.prototype.str_replace = function (find, replace) {
	var str = this;
	for (var i = 0; i < find.length; i++) {
		str = str.replace(find[i], replace[i]);
	}
	return str;
};

String.prototype.toCurrency = Number.prototype.toCurrency = function (params) {
	var n = parseFloat(this);
	params = $.extend({}, $ac.currency, params);

	return (n < 0 ? params.neg : params.pos) + params.symbol_left + Math.abs(n).formatNumber() + params.symbol_right;
}

String.prototype.formatNumber = Number.prototype.formatNumber = function (params) {
	params = $.extend({}, $ac.currency, params);

	var n = parseFloat(this);
	var prec = !isFinite(+params.decimals) ? 0 : Math.abs(params.decimals);

	n = (n + '').replace(/[^0-9+\-Ee.]/g, '');
	n = !isFinite(+n) ? 0 : +n;

	var s = ('' + n.roundFloat(prec)).split('.');

	if (s[0].length > 3) {
		s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, params.thousands_sep);
	}
	if ((s[1] || '').length < prec) {
		s[1] = s[1] || '';
		s[1] += new Array(prec - s[1].length + 1).join('0');
	}
	return s.join(params.dec_point);
}

Number.prototype.roundFloat = function (p) {
	var k = Math.pow(10, p);
	return '' + Math.round(this * k) / k;
}

//Async Load on call
$ac.alq = {}, $ac.al_loaded = {};

$ac.al = $.extend($ac.al || {}, {
	codemirror:     ['system/resources/js/codemirror/codemirror.js', 'system/resources/js/codemirror/wrapper.js'],
	list_widget:    'app/view/js/listings.js',
	listview:       'app/view/js/listings.js',
	ac_template:    'app/view/js/ac_template.js',
	amplo_slider:   'app/view/js/amplo_slider.js',
	flexselect:     'app/view/js/flexselect.js',
	jqzoom:         'system/resources/js/jquery/jqzoom/jqzoom.js',
	ac_imageinput:  'app/view/js/image_manager.js',
	ac_filemanager: 'app/view/js/image_manager.js'
});

for (var fn in $ac.al) {
	register_autoload(fn, $ac.al[fn]);
}

function register_autoload(fn, url) {
	if (!$.fn[fn]) {
		url = typeof url === 'string' ? [url] : url;

		$.fn[fn] = function (view_id) {
			var al = arguments.callee.fn;
			var fn = al;

			if (!$ac.alq[al]) {
				var load_count = 1;
				for (var u in url) {
					if ($ac.al_loaded[url[u]]) {
						fn = al;
						al = $ac.al_loaded[url[u]];
					} else {
						$ac.alq[al] = [];
						$ac.al_loaded[url[u]] = al;
						js_url = url[u].match(/^([a-z]+:\/\/)|(\/\/)/) ? url[u] : $ac.site_url + url[u];
						$.getScript(js_url, function () {
							if (load_count++ >= url.length) {
								for (var l in $ac.alq[al]) {
									var q = $ac.alq[al][l];
									$.fn[q.fn].apply(q.me, q.args);
								}

								$(document).trigger(al);
							}
						});
					}
				}
			}

			$ac.alq[al].push({fn: fn, me: this, args: arguments});

			return this;
		};

		$.fn[fn].fn = fn;
	}
}

$.fn.use_once = function (label) {
	label = label || 'activated';
	return this.not('.' + label).addClass(label);
}

$.fn.scrollTo = function (target, options) {
	target = $(target);

	if (!target.length) {
		return false;
	}

	var $this = this;
	var $header = $('header.main-header');

	options = $.extend({}, {
		offset:   $header.css('position') === 'fixed' ? -$header.outerHeight() : 0,
		callback: null
	}, options);

	var top = target.offset().top + options.offset;
	this.stop();

	$this.animate({scrollTop: top}, {
		duration: 1000, complete: function (e) {
			if (typeof options.callback == 'function') {
				options.callback(e);
			}
		}
	});
}

//Add the date/time picker to the elements with the special classes
$.ac_datepicker = function (params) {
	$('.datepicker, .timepicker, .datetimepicker').ac_datepicker(params);
}

$.fn.ac_datepicker = function ac_datepicker(params) {
	if (!this.length) {
		return this;
	}

	if (!$.ui.timepicker) {
		var selector = this;
		$.ajaxSetup({cache: true});
		if (!$.ac_datepicker.loading) {
			$.ac_datepicker.loading = true;
			$.getScript($ac.site_url + 'system/resources/js/jquery/ui/datetimepicker.js', function () {
				selector.ac_datepicker(params);
			});
		}
		return;
	}

	params = $.extend({}, {
		type:       null,
		dateFormat: 'yy-mm-dd',
		timeFormat: 'HH:mm'
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
		change:   null
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

//Apply a filter form to the URL
$.fn.apply_filter = function (url) {
	var filter_list = this.find('[name]');

	if (filter_list.length) {
		filter_list.each(function (i, e) {
			var $e = $(e);
			var $filter = $e.closest('.column-filter');
			var $type = $filter.find('.filter-type');

			if ($type.hasClass('not')) {
				$e.attr('name', $e.attr('name').replace(/^filter\[!?/, 'filter[!'));
			}

			if (!$type.hasClass('not') && !$type.hasClass('equals')) {
				delete filter_list[i];
			}
		});

		url += (url.search(/\?/) === -1 ? '?' : '&') + filter_list.serialize();
	}

	return url;
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
		position:   'absolute',
		top:        pos.top,
		left:       pos.left,
		opacity:    .8,
		'z-index':  10000
	})
		.width($(this).width())
		.height($(this).height());

	$('body').css({position: 'relative'});
	$('body').append(ele);

	ele.animate({'background-color': 'rgba(255,255,85,1)'}, {
		duration: 300, always: function () {
			ele.animate({'background-color': 'rgba(255,255,255,0)'}, {
				duration: 700, always: function () {
					ele.remove()
				}
			});
		}
	});

	return this;
}

$.fn.overflown = function (dir, tolerance) {
	return this.each(function (i, e) {
		var over;

		if (dir) {
			over = dir === 'y' ? e.scrollHeight > (e.clientHeight + tolerance) : e.scrollWidth > (e.clientWidth + tolerance);
		}

		over = e.scrollHeight > (e.clientHeight + tolerance) || e.scrollWidth > (e.clientWidth + tolerance);

		if (over) {
			$(e).addClass('overflown');
		}
	});
}

$.fn.tabs = function (opts) {
	var $tabs = this;

	opts = $.extend({}, {
		callback: null,
		toggle:   false
	}, opts);

	$tabs.o = opts;

	$tabs.changeOptions = function (o) {
		$.extend($tabs.o, o);
	}

	$tabs.setOptions = function (o) {
		$tabs.o = o;
	}

	$tabs.click(function () {
		var $this = $(this);
		var $content = $($this.attr('href'));

		if (typeof $tabs.o.toggle === 'function' ? $tabs.o.toggle.call($tabs, $this) : $tabs.o.toggle) {
			$this.toggleClass('active');
			$content.toggleClass('hidden', $this.hasClass('active'));
		} else {
			$tabs.removeClass('active');

			$tabs.each(function (i, e) {
				$($(e).attr('href')).addClass('hidden');
			});

			$this.addClass('active');
			$content.removeClass('hidden');
		}

		if (typeof $tabs.o.callback === 'function') {
			$tabs.o.callback($this, $content);
		}

		return false;
	}).first().click();

	return this;
};

$.fn.show_msg = function (type, msg, options) {
	if (type === 'clear') {
		return $(this).find('.messages').remove();
	}

	//Data types are not messages
	if (type === 'data') {
		return;
	}

	options = $.extend({
		style:       'stacked',
		inline:      $ac.show_msg_inline,
		append:      true,
		append_list: false,
		delay:       false,
		close:       true,
		clear:       true
	}, options);

	if (typeof msg === 'undefined' || msg === null) {
		msg = type;
		type = null;
	}

	if (options.clear) {
		this.find('.messages').remove();
	}

	if (typeof msg === 'object') {
		for (var m in msg) {
			options.clear = false;
			this.show_msg(type || m, msg[m], options);
		}
		return this;
	}

	return this.each(function (i, e) {
		var $e = options.inline ? $(e) : $('#message-box');

		var $box = $e.find('.messages.' + type);

		if (!$box.length) {
			$box = $('<div />').addClass('messages ' + type + ' ' + options.style);

			if (options.close) {
				$box.append($('<div />').addClass('close').click(function () {
					$(this).closest('.messages').remove();
				}));
			}

			if (options.append) {
				$e.append($box);
			} else {
				$e.prepend($box);
			}
		}

		var $msg = $('<div />').addClass('message hide').html(msg);

		if (options.append_list) {
			$box.append($msg);
		} else {
			$box.prepend($msg);
		}

		$msg.removeClass('hide');

		if (options.delay) {
			setTimeout(function () {
				$box.remove()
			}, options.delay);
		}
	});
}

$.fn.ac_errors = function (errors, noclear) {
	if (!noclear) {
		this.find('.err-msg').remove();
	}

	for (var err in errors) {
		if (typeof errors[err] == 'object') {
			this.ac_errors(errors[err], true);
			continue;
		}

		var ele = this.find('[name="' + err + '"]');

		if (!ele.length) {
			ele = $('#' + err);
		}

		if (!ele.length) {
			ele = $(err);
		}

		if (!ele.length) {
			return this.show_msg('error', errors);
		}

		ele.after($("<div />").addClass('error err-msg').html(errors[err]));
	}

	return this;
}

$.ac_errors = function (errors) {
	$('body').ac_errors(errors);
}

$.fn.fade_post = function (url, data, callback, dataType) {
	var $this = this;
	$this.stop().fadeOut(300).after($.loading);

	$.post(url, data, function (data) {
		$this.fadeIn(0);
		$.loading('stop');

		if (typeof callback === 'function') {
			callback(data);
		}
	}, dataType || null);

	return this;
}

$.fn.file_upload = function (options) {
	return this.each(function (i, e) {
		options = $.extend({
			change:   amplo_file_upload,
			progress: amplo_progress,
			success:  amplo_success,
			url:      $ac.site_url + 'common/file-upload',
			xhr:      amplo_xhr,
			path:     '',
			preview:  null,
			message:  'Click to upload file'
		}, options);

		var $input = $(e);
		var $upload = $('<div class="file-upload-box"><div class="msg"/></div>');
		$input.after($upload).appendTo($upload);
		var $bar = $('<div class="progress-bar"><div class="progress" /></div>').appendTo($upload);

		$input[0].save = $('<input type="hidden" name="' + $input.attr('name') + '" />').appendTo($upload);
		$input[0].preview = $($input.attr('data-preview'));
		$input[0].msg = $upload.find('.msg').html(options.message);
		$input[0].progress = $bar.find('.progress');

		//Hide Input field
		$input.css({left: -99999});
		$input.click(function (e) {
			e.stopPropagation();
		});

		$upload.click(function () {
			$input.click();
		});

		$input.removeAttr('name');

		if (typeof options.change === 'function') {
			$input.change(options.change);
		}

		function amplo_file_upload() {
			var $this = this;

			if (!$this.files) {
				return alert('No Files to upload');
			}

			for (var i = 0; i < $this.files.length; i++) {
				var file = $this.files[i];
				var fd = new FormData();

				fd.append('file', file);
				fd.append('path', options.path);

				$.ajax({
					url:         options.url,
					data:        fd,
					processData: false,
					contentType: false,
					type:        'POST',
					xhr:         function (e) {
						this.context = $this;
						return options.xhr.call(this, e);
					},
					success:     function (response, status, xhr) {
						this.context = $this;
						return options.success.call(this, response, status, xhr);
					}
				});
			}
		}

		function amplo_xhr() {
			var $this = this;
			var myXhr = $.ajaxSettings.xhr();

			if (myXhr.upload) {
				myXhr.upload.addEventListener('progress', function (e) {
					this.context = $this.context;
					return options.progress.call(this, e);
				}, false);
			}

			return myXhr;
		}

		function amplo_success(response, status, xhr) {
			amplo_progress.call(this, 100);

			if (response.data) {
				for (var f in response.data) {
					var url = response.data[f];
					this.context.save.val(url);
					this.context.msg.html(url);

					var preview = options.preview ? $(options.preview) : this.context.preview;
					if (preview.length) {
						preview.attr('src', url);
					}

					break;
				}
			}
		}

		function amplo_progress(e) {
			//Multiply by 75 to account for the delay of server response
			var total = typeof e === 'object' ? (e.loaded / e.total) * 75 : e;
			total = total.toFixed(1);
			this.context.progress.css({width: total + '%'});
			this.context.msg.html(total + '%');
		}
	});
}

function ac_form(params) {
	params = params || {}

	var $form = $(this);
	var callback = params.success;
	var complete = params.complete;

	params = $.extend({}, {
		data:     $form.serialize(),
		dataType: 'json',
		type:     'POST'
	}, params);

	params.success = function (data, textStatus, jqXHR) {
		if (typeof data == 'object') {
			if (data.error) {
				$form.ac_errors(data.error);
			}

			$form.show_msg(data);
		} else {
			$form.replaceWith(data);
		}

		if (typeof callback == 'function') {
			callback(data, textStatus, jqXHR);
		}
	}

	params.complete = function (jqXHR, textStatus) {
		$form.find('[data-loading]').loading('stop');

		if (typeof complete == 'function') {
			complete(jqXHR, textStatus);
		}
	}

	$.ajax($form.attr('action'), params);

	return false;
}

$.loading = function (params) {
	if (params == 'stop') {
		$('.loader').remove();
		return;
	}

	params = $.extend({}, {
		dots:       8,
		width:      null,
		height:     null,
		animations: 'bounce, fadecolor'
	}, params);

	loader = $('<div class="loader">' + '<div class="loader-item"></div>'.repeat(params.dots) + '</div>');

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
	return this.each(function (i, e) {
		var $e = $(e);

		var option = typeof params === 'string' ? {} : $.extend({}, {
			text:    $e.attr('data-loading') || (params ? params.default_text : false),
			disable: true,
			delay:   false,
			onStop:  null
		}, params);

		if (option.text || $e.data('original')) {
			if (params === 'stop') {
				$e.prop('disabled', false).removeAttr('disabled');
				$e.html($e.data('original'));
			} else {
				if (option.disable) {
					$e.prop('disabled', true).attr('disabled', 'disabled');
				}
				if (!$e.data('original')) {
					$e.data('original', $e.html());
				}
				if (option.text) {
					$e.html(option.text);
				}

				if (option.delay) {
					setTimeout(function () {
						$e.loading('stop');
						if (typeof option.onStop === 'function') {
							option.onStop.call($e);
						}
					}, option.delay);
				}
			}
		} else {
			$e.find('.loader').remove();
			$e.append($.loading(params));
		}
	});
}

$.fn.ac_zoneselect = function (params, callback) {
	var $this = this;

	params = $.extend({}, {
		listen:    null,
		allow_all: false,
		select:    null,
		url:       $ac.site_url + 'data/locale/load_zones?ajax=1'
	}, params);

	if (!params.listen) {
		throw "You must specify 'listen' in the parameters. This is the Country selector element";
	} else {
		params.listen = $(params.listen);
	}

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

jQuery.fn.serializeObject = function () {
	var arrayData, objectData;
	arrayData = this.serializeArray();
	objectData = {};

	$.each(arrayData, function () {
		var value;

		if (this.value != null) {
			value = this.value;
		} else {
			value = '';
		}

		if (objectData[this.name] != null) {
			if (!objectData[this.name].push) {
				objectData[this.name] = [objectData[this.name]];
			}

			objectData[this.name].push(value);
		} else {
			objectData[this.name] = value;
		}
	});

	return objectData;
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

$ac.currency = $ac.currency || {}
$ac.currency = $.extend({
	symbol_left:   '$',
	symbol_right:  '',
	decimals:      2,
	dec_point:     '.',
	thousands_sep: ',',
	neg:           '-',
	pos:           ''
}, $ac.currency);

$.cookie = function (key, value, options) {
	if (arguments.length > 1) {
		options = options || {};

		if (value === null) {
			options.expires = -1;
		} else if (typeof value === "object") {
			value = JSON.stringify(value);
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
	var result;

	var decode = function (s) {
		if (options.raw) {
			return s;
		}
		s = decodeURIComponent(s);

		return JSON.parse(s) || s;
	}

	return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? decode(result[1]) : null;
};

function ac_radio_bubble() {
	$('.ac-radio').not('ac-radio-bubble').click(function () {
		$(this).addClass('ac-radio-bubble');
		var $radio = $(this).parents('label').children('input[type=radio]');

		if (!$radio.prop('checked')) {
			$radio.prop('checked', true).change();
		}
	});

	$('.ac-radio input').focus(function () {
		var $radio = $(this).closest('.ac-radio').children('input[type=radio]');
		if (!$radio.prop('checked')) {
			$radio.prop('checked', true).change();
		}
	});
}

function register_confirms() {
	var $confirms = $('[data-confirm], [data-confirm-text]').use_once();

	$confirms.click(function () {
		var $this = $(this);

		if ($this.prop('disabled')) {
			return false;
		}

		if ($this.is('[data-confirm]') && !$this.hasClass('confirm')) {
			setTimeout(function () {
				$this.removeClass('confirm').loading('stop');
			}, 2000);
			$this.loading({text: $this.attr('data-confirm') || "Confirm?", disable: false}).addClass('confirm');

			return false;
		}

		if ($this.is('[data-confirm-text]')) {
			if (!confirm($this.attr('data-confirm-text') || "Are you sure you want to continue?")) {
				return false;
			}
		}

		if ($this.hasClass('ajax-call')) {
			amplo_ajax_cb.call($this);
			return false;
		}
	});

	$('.action-delete').use_once().click(function () {
		return confirm("Deleting this entry will completely remove all data associated from the system. Are you sure?");
	});

	$('.ajax-form').use_once('ajax-init').submit(function () {
		return ac_form.call(this);
	});
}

function register_ajax_calls(is_ajax) {
	$('a[data-loading]').click(function () {
		$(this).loading();
	});

	$('form').use_once('data-loading-set').submit(function () {
		$(this).find('button[data-loading]').loading();
	});

	$((is_ajax ? '[data-if-ajax],' : '') + '[data-ajax]').use_once('ajax-call').not('[data-confirm], [data-confirm-text]').amplo_ajax();

	// Multistate Checkboxes
	$('[data-multistate]').use_once().click(function () {
		var $this = $(this);
		var val = $this.val();
		var states = $this.attr('data-multistate').split(';');

		if (!$this.prop('checked')) {
			for (var s = 0; s < states.length; s++) {
				if (states[s] === val) {
					if (s < states.length - 1) {
						$this.val(states[s + 1]);
					} else {
						$this.val(states[0]);
						return true;
					}
				}
			}
			return false;
		}
	});

	if (is_ajax) {
		$('form.ctrl-save').use_once().submit(function () {
			var $form = $(this);

			var params = {
				callback: function (response) {
					//Redirect from new form to edit form
					if (response.data) {
						for (var id in response.data) {
							var regx = new RegExp(id + '=\\d+');

							if (!location.href.match(regx)) {
								location = location.href + (location.href.indexOf('?') > 0 ? '&' : '?') + id + '=' + response.data[id];
							}
						}
					}

					$form.show_msg(response);

					if (!response.error && $form.closest('#colorbox').length) {
						$.colorbox.close();
					}
				}
			}

			$(this).submit_ajax_form(params);

			return false;
		}).find('a.cancel, a.back').click(function () {
			if ($(this).closest('#colorbox').length) {
				$.colorbox.close();
				return false;
			}
		});
	}
}

function register_colorbox() {
	var $colorbox = $('.colorbox').use_once('colorbox-init');

	if ($colorbox.length) {
		var width = Math.max($('body').width() * .6, Math.min($('body').width() - 16, 400));

		var defaults = {
			overlayClose: true,
			opacity:      0.5,
			width:        width,
			height:       '80%'
		}

		$colorbox.each(function (i, e) {
			var $e = $(e);
			defaults.photo = $e.hasClass('colorbox-photo');
			$e.colorbox(defaults);
		});
	}
}

$.fn.collapsible = function () {
	return this.each(function (i, e) {
		var $c = $(e);

		$c.click(function () {
			$(this).toggleClass('hide');
		});

		$c.find('.collapse, input, select, textarea, a').click(stopProp);
	});
}

function stopProp(e) {
	e.stopPropagation();
}

$.fn.form_editor = function () {
	return this.use_once('form-editor-enabled').each(function (i, e) {
		$fe = $(e);
		var $form = $fe.is('form') ? $fe : $fe.find('form');

		$fe.find('.edit-form').click(edit_form_editor);
		$fe.find('.toggle-form').click(toggle_form_editor);
		$fe.find('.cancel-form').click(cancel_form_editor);

		$form.not('[data-noajax]').submit(function () {
			var $form = $(this);

			$form.find('[data-loading]').loading();

			$.post($form.attr('action'), $form.serialize(), function (response) {
				$form.find('[data-loading]').loading('stop');

				$form.show_msg('clear');

				if (response.error) {
					$form.show_msg(response);
				} else {
					if ($form.attr('data-reload')) {
						window.location.reload();
					} else if ($form.attr('data-callback')) {
						var cb = window[$form.attr('data-callback')];
						if (typeof cb === 'function') {
							cb.call($form);
						}
					} else {
						$form.find('[name]').each(function (i, e) {
							var $e = $(e);
							var val = $e.val();

							if ($e.is('select')) {
								val = $e.find('option[value="' + val + '"]').html();
							}

							var $field = $form.find('[data-name="' + $e.attr('name') + '"]');

							if (!$field.length && $e.attr('name').indexOf('[') === -1) {
								$field = $form.find('.field.' + $e.attr('name'));
							}

							$field.html(val).val(val);
						});

						if (response.success) {
							$form.show_msg('success', response, {delay: 4000});
						}
					}

					cancel_form_editor.call($form);
				}
			});

			return false;
		});
	});
}

function edit_form_editor() {
	$(this).closest('.form-editor').addClass('edit').removeClass('read').find('[readonly]').attr('data-readonly', 1).removeAttr('readonly');
	return false;
}

function toggle_form_editor() {
	$(this).closest('.form-editor').hasClass('read') ? edit_form_editor.call(this) : cancel_form_editor.call(this);
	return false;
}

function cancel_form_editor() {
	var $section = $(this).is('.form-editor') ? $(this) : $(this).closest('.form-editor');
	$section.removeClass('edit').addClass('read').find('[data-readonly]').attr('readonly', '');
	return false;
}

function colorbox(params) {
	$.extend(params, {
		maxWidth:  '90%',
		maxHeight: '90%'
	});

	$.colorbox(params);
}

var amplo_ajax_cb = function () {
	var $this = $(this), callback;

	if ($this.prop('disabled')) {
		return false;
	}

	var ajax_cb = $this.attr('data-if-ajax') || $this.attr('data-ajax');

	if (typeof window[ajax_cb] !== 'function') {
		var $replace = $(ajax_cb);

		if (!$replace.length) {
			var opts = {href: $this.attr('href')};

			if (ajax_cb === 'iframe') {
				opts['iframe'] = true;
				opts['width'] = window.innerWidth * .8;
				opts['height'] = window.innerHeight * .9;
			}

			if ($this.attr('data-loading')) {
				$this.loading();
			}

			colorbox(opts);
			return false;
		}

		callback = function (response) {
			$replace.replaceWith(response);
		}
	} else {
		callback = function (response) {
			window[ajax_cb].call($this, response);
		}
	}

	if ($this.is('form')) {
		$this.submit_ajax_form({callback: callback});
	} else {
		$this.loading({text: $this.is('[data-loading]') || 'Loading...'})
		$.get($this.attr('href'), {}, callback)
			.always(function () {
				$this.loading('stop');
			});

	}

	return false;
};

$.fn.amplo_ajax = function () {
	return this.each(function (i, e) {
		var $e = $(e);

		if ($e.is('form')) {
			$e.submit(amplo_ajax_cb);
		} else {
			$e.click(amplo_ajax_cb);
		}
	});
}

$.fn.submit_ajax_form = function (params) {
	params = $.extend({}, {
		callback: null
	}, params);

	return this.each(function (i, e) {
		var $form = $(e);

		var $btns = $form.find('button, input[type=submit], [data-loading]').loading({default_text: 'Submitting...'});

		$.post($form.attr('action'), $form.serialize(), typeof params.callback === 'function' ? params.callback : function (response) {
			$form.show_msg(response);
		}).always(function () {
			$btns.loading('stop');
		});
	});
}

function content_loaded(is_ajax) {
	var $forms = $('form');

	$forms.find('input').use_once('form-input').keydown(function (e) {
		if (e.keyCode == 13) {
			$(this).closest('form').submit();
			return false;
		}
	});

	$forms.find('[name=username], [name=name], [name=email], [name=password], [name=confirm]').prop('autocorrect', false).attr('autocorrect', 'off');

	if ($ac.defer_scripts) {
		var scripts = '';

		$('script').each(function (i, e) {
			if ($(e).attr('type') === 'text/defer-javascript') {
				if ($(e).attr('src')) {
					console.error('External script ' + $(e).attr('src') + ' cannot be loaded synchronously with defer_scripts enabled. Use $.getScript() to load asynchronously or use $this->document->addScript() in your PHP Controller class.');
				} else {
					scripts += e.innerHTML;
					$(e).remove();
				}
			}
		});

		if (scripts) {
			var script = document.createElement('script');
			script.type = 'text/javascript';
			script.innerHTML = scripts;
			document.body.appendChild(script);
		}
	}

	for (var f in arguments.callee.fn) {
		fn = arguments.callee.fn[f];
		if (typeof fn === 'function') {
			fn.call(this, is_ajax);
		}
	}
}

content_loaded.fn = {};

content_loaded.fn['ajax_calls'] = register_ajax_calls;
content_loaded.fn['confirms'] = register_confirms;
content_loaded.fn['colorbox'] = register_colorbox;

$(document)
	.ready(function () {
		$('.ui-autocomplete-input').on("autocompleteselect", function (e, ui) {
			if (!ui.item.value && ui.item.href) {
				window.open(ui.item.href);
			}
		});

		content_loaded();
	})

	.keydown(function (e) {
		if (e.ctrlKey && (e.which == 83)) {
			$('form.ctrl-save').submit_ajax_form();

			e.preventDefault();
			return false;
		}
	})

	.on("DOMNodeInserted", function () {
		ac_radio_bubble();
	})

	.ajaxComplete(function () {
		content_loaded(true);
	});


//Chrome Autofill disable hack
if (navigator.userAgent.toLowerCase().indexOf("chrome") >= 0) {
	$(window).load(function () {
		$('input:-webkit-autofill[autocomplete="off"]').each(function () {
			var $this = $(this);
			if (!$this.attr('value')) {
				$this.val('');
				setTimeout(function () {
					$this.val('');
				}, 200);
			}
		});
	});
}
