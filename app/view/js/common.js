//Similar to LESS screen sizing
var screen_width = (window.innerWidth > 0) ? window.innerWidth : screen.width;
var screen_lg = screen_width >= 1200,
	screen_md = screen_width >= 768 && screen_width < 1200,
	screen_sm = screen_width >= 480 && screen_width < 768,
	screen_xs = screen_width < 480;

$('body').toggleClass('webkit', /AppleWebKit/.test(navigator.userAgent));

Function.prototype.loop = function (time, count) {
	var fn = this;
	setTimeout(function () {
		(fn(count = (+count || 0) - 1) === false || !count) ? 0 : fn.loop(time, count)
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

for (var fn in $ac.al) {
	register_autoload(fn, $ac.al[fn]);
}

function register_autoload(fn, url) {
	if (!$[fn]) {
		url = typeof url === 'string' ? [url] : url;

		$[fn] = function () {
			autoload_js_file.call(this, url, arguments, 'base')
		}

		$.fn[fn] = function () {
			autoload_js_file.call(this, url, arguments, 'fn')
		}

		$[fn].fn = $.fn[fn].fn = fn;
	}
}

function autoload_js_file(url, args, type) {
	var al = args.callee.fn;
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

				$.ajax({
						url:      js_url,
						dataType: 'script',
						cache:    true
					})
					.done(function () {
						if (load_count++ >= url.length) {
							for (var l in $ac.alq[al]) {
								var q = $ac.alq[al][l];
								(type === 'fn' ? $.fn[q.fn] : $[q.fn]).apply(q.me, q.args);
							}

							$(document).trigger(al);
						}
					}).always(function (jqXHR, status, msg) {
					if (status !== 'success') {
						$.error('There was an error loading the autoloaded file:', url, msg, jqXHR);
					}
				});
			}
		}
	}

	$ac.alq[al].push({fn: fn, me: this, args: args, type: type});

	return this;
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

//Generic amp protocol for jQuery plugins
$.amp = function (p, args) {
	var o = args[0];

	if (p[o]) {
		return p[o].apply(this, Array.prototype.slice.call(args, 1));
	} else if (typeof o === 'object' || !o) {
		return p.init.apply(this, args);
	} else {
		$.error('Method ' + o + ' does not exist for jQuery plugin ' + p.name);
	}
}

//ampToggle jQuery Plugin
$.ampToggle = $.fn.ampToggle = function (o) {
	return $.amp.call(this, $.ampToggle, arguments);
}

$.extend($.ampToggle, {
	init: function (o) {
		if (!o) {
			$.error("ampToggle parameter error: content must be an existing DOM element");
			return this;
		}

		o = $.extend({}, {
			toggle:           this,
			content:          this,
			toggleClass:      'active',
			contentClass:     'active',
			hideToggleClass:  '',
			hideContentClass: null,
			start:            'hide',
			acceptParent:     '',
			onShow:           null,
			onHide:           null
		}, o);

		o.toggle = $(o.toggle || this);
		o.content = $(o.content || this);

		//Only add .hidden class if the toggle is not a child / same element as the content
		if (o.hideContentClass === null) {
			o.hideContentClass = o.toggle.closest(o.content).length ? '' : 'hidden';
		}

		if (o.toggle.length) {
			o.toggleClass += ' amp-toggle-show';
			o.contentClass += ' amp-toggle-show';
			o.hideToggleClass += ' amp-toggle-hide';
			o.hideContentClass += ' amp-toggle-hide';
			o.content.data('amp-toggle-o', o).addClass('amp-toggle-content');

			o.toggle.data('amp-toggle-o', o).addClass('amp-toggle').click(function () {
				$.ampToggle.blurred ? $.ampToggle.blurred = false : o.toggle.ampToggle(o.toggle.hasClass(o.toggleClass) ? 'hide' : 'show');
			})

			if (o.start) {
				o.toggle.ampToggle(o.start === 'show' ? 'show' : 'hide');
			}
		}

		return this;
	},

	_blur: function (e) {
		var $t = $(e.target), o = $.ampToggle.active.data('amp-toggle-o');

		if ($t.closest(o.content).length) {
			!o.content.is('.amp-toggle') || ($.ampToggle.blurred = true);
		} else if (!$t.closest(o.acceptParent).length) {
			$.ampToggle.active.ampToggle('hide');

			if ($t.hasClass('amp-toggle')) {
				$.ampToggle.blurred = true;
			}
		}
	},

	show: function () {
		var $this = $(this);
		var o = $this.data('amp-toggle-o');

		o.toggle.addClass(o.toggleClass).removeClass(o.hideToggleClass);
		o.content.addClass(o.contentClass).removeClass(o.hideContentClass);
		$.ampToggle.active = $this;
		document.addEventListener('click', $.ampToggle._blur, true);

		if (typeof o.onShow === 'function') {
			o.onShow.call(this, o);
		}
	},

	hide: function () {
		var $this = $(this);
		var o = $this.data('amp-toggle-o');

		o.toggle.removeClass(o.toggleClass).addClass(o.hideToggleClass);
		o.content.removeClass(o.contentClass).addClass(o.hideContentClass);
		$.ampToggle.active = null;
		document.removeEventListener('click', $.ampToggle._blur, true);

		if (typeof o.onHide === 'function') {
			o.onHide.call(this, o);
		}
	},
});

//ampYouTube
$.ampYouTube = $.fn.ampYouTube = function () {
	return $.amp.call(this, $.ampYouTube, arguments);
}

$.extend($.ampYouTube, {
	init: function (o) {
		o = $.extend({}, {
			width:      null,
			height:     null,
			ratio:      .6,
			playlistID: '',
			videoID:    '',
			paused:     true,
			onChange:   null,
			onReady:    null
		}, o);

		var tag = document.createElement('script');
		tag.src = "https://www.youtube.com/iframe_api";
		var firstScriptTag = $('script')[0];
		firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

		o.width = o.width || this.width();
		o.height = o.height || (o.ratio * o.width) || this.height();

		this.width(o.width);
		this.height(o.height);
		this.data('o', o);

		if (!$.ampYouTube.players) {
			$.ampYouTube.players = this;
		} else {
			$.ampYouTube.players.add(this);
		}

		return this;
	},

	initPlayer: function () {
		var o = this.data('o');

		var player = new YT.Player(this.attr('id'), {
			width:   o.width,
			height:  o.height,
			videoId: o.videoID,
			events:  {
				onReady:       $.ampYouTube.onReady,
				onStateChange: $.ampYouTube.onChange
			}
		});

		player.ampO = o;

		this.data('player', player);
	},

	play: function () {
		var player = $(this).data('player');
		player.ampO.playing = true;
		player.playVideo();
	},

	pause: function () {
		var player = $(this).data('player');
		player.ampO.playing = false;
		player.pauseVideo();
	},

	playIndex: function (i) {
		this.data('player').playVideoAt(i);
	},

	onReady: function (e) {
		e.target.loadPlaylist({
			list: e.target.ampO.playlistID
		});

		e.target.ampO.playing = false;

		if (typeof e.target.ampO.onReady === 'function') {
			e.target.ampO.onReady.call(this, e);
		}
	},

	onChange: function (e) {
		if (e.data === 3) {
			e.target.ampO.playing = true;
		}

		if (!e.target.ampO.playing) {
			e.target.pauseVideo();
		}

		if (typeof e.target.ampO.onChange === 'function') {
			e.target.ampO.onChange.call(this, e);
		}
	}
});


function onYouTubeIframeAPIReady() {
	$.ampYouTube.players.each(function (i, e) {
		$(e).ampYouTube('initPlayer');
	});
}

//ampModal jQuery Plugin
$.ampModal = $.fn.ampModal = function (o) {
	return $.amp.call(this, $.ampModal, arguments)
}

$.extend($.ampModal, {
	init: function (o) {
		if (typeof this === 'function' && !o.content) {
			return $.error('Error ampModal: You must specify a value for content.');
		}

		o = $.extend({}, {
			context:     'body',
			title:       '',
			class:       '',
			content:     this,
			buttons:     {},
			shadow:      true,
			shadowClose: true,
			onAction:    null,
			onOpen:      null,
			onClose:     null
		}, o);

		this.data('o', o);

		if (!(o.context = $(o.context)).length) {
			$.error('ampModal parameter error: context must be an existing element');
			return this;
		}

		return $(o.content).use_once('amp-modal-enabled').each(function (i, e) {
			var $e = $(e),
				$box = $('<div />').addClass('amp-modal').addClass(o.class),
				$content = $('<div />').addClass('amp-modal-content'),
				$title = $('<div/>').addClass('amp-modal-title');

			o.context.append($box);

			$content
				.append(o.title ? $title.html(o.title) : '')
				.append($e)

			if (o.buttons) {
				var $buttons = $('<div/>').addClass('amp-modal-buttons');

				for (var b in o.buttons) {
					btn = o.buttons[b];

					if (!o.buttons[b].action) {
						o.buttons[b].action = function () {
							$(this).ampModal('close');
						}
					}

					var $btn = $('<button/>')
						.addClass('amp-modal-button ' + b)
						.attr('data-action', b)
						.append(btn.label || b)
						.click(btn.action)
						.appendTo($buttons)

					if (typeof o.onAction === 'function') {
						$btn.click(function () {
							o.onAction.call(this, $(this).attr('data-action'));
						})
					}
				}

				$content.append($buttons);
			}

			$box
				.append($('<div/>').addClass('align-middle'))
				.append($content)

			if (o.shadow) {
				var $shadow = $('<div/>').addClass('shadow-box').appendTo($box);

				if (o.shadowClose) {
					$shadow.click($.ampModal.close)
				}
			}
		});
	},
	open: function () {
		var $this = $(this).closest('.amp-modal').addClass('active')

		var onOpen = $this.data('o').onOpen;

		if (typeof onOpen === 'function') {
			onOpen.call($this);
		}

		return $this;
	},

	close: function () {
		var $this = $(this).closest('.amp-modal').removeClass('active')

		var onClose = $this.data('o').onClose;

		if (typeof onClose === 'function') {
			onClose.call($this);
		}

		return $this;
	}
});

$.ampAlert = $.fn.ampAlert = function (o) {
	o = $.extend({}, {
		title:       'Warning!',
		class:       'amp-modal-alert',
		content:     $('<div/>').addClass('amp-alert'),
		text:        typeof o === 'string' ? o : 'This warning was generated by Amplo MVC',
		onClose:     function () {
			this.remove()
		},
		shadowClose: false,
		buttons:     {
			ok: {
				label: 'Ok',
			},
		}
	}, o)

	o.content = $(o.content).append(o.text);

	return $.ampModal.call(this, o).ampModal('open');
}

$.ampConfirm = $.fn.ampConfirm = function (o) {
	o = $.extend({}, {
		title:       'Are you sure?',
		class:       'amp-modal-confirm',
		content:     $('<div/>').addClass('amp-confirm'),
		text:        'Are you sure you want to continue?',
		onConfirm:   null,
		onCancel:    null,
		onClose:     function () {
			this.remove()
		},
		shadowClose: false,
		buttons:     {
			cancel:  {
				label: 'Cancel',
			},
			confirm: {
				label: 'Confirm',
			}
		}
	}, o)

	o.content = $(o.content).append(o.text);

	if (o.buttons.confirm && !o.buttons.confirm.action) {
		o.buttons.confirm.action = function () {
			if (typeof o.onConfirm === 'function') {
				o.onConfirm.call(this, $(this).closest('.amp-modal'));
			}

			$(this).ampModal('close');
		}
	}

	if (o.buttons.cancel && !o.buttons.cancel.action) {
		o.buttons.cancel.action = function () {
			if (typeof o.onCancel === 'function') {
				o.onCancel.call(this, $(this).closest('.amp-modal'));
			}

			$(this).ampModal('close');
		}
	}

	return $.ampModal.call(this, o).ampModal('open');
}

//ampSelect jQuery Plugin
$.ampSelect = $.fn.ampSelect = function (o) {
	return $.amp.call(this, $.ampSelect, arguments)
}

$.extend($.ampSelect, {
	init: function (o) {
		o = $.extend({}, {}, o);

		return this.use_once('amp-select-enabled').each(function (i, e) {
			var $select = $(e);
			var $selected = $("<div />").addClass('amp-selected').append($('<div/>').addClass('align-middle')).append($('<div/>').addClass('value')),
				$box = $('<div/>').addClass('amp-select-box'),
				$options = $('<div/>').addClass('amp-select-options no-parent-scroll'),
				$checkall = $('<label/>').addClass('amp-select-checkall checkbox white').append($('<input/>').attr('type', 'checkbox')).append($('<span/>').addClass('label')),
				$actions = $('<div/>').addClass('amp-select-actions'),
				$done = $('<a/>').addClass('amp-select-done button').html('Done'),
				$title = $('<div/>').addClass('amp-select-title').append($('<div/>').addClass('text').html($select.attr('data-label') || 'Select one or more items'));

			$select.before($selected);
			$checkall.data('box', $box).find('input').data('box', $box);
			$selected.data('box', $box);

			//Events
			$checkall.find('input').change($.ampSelect.checkall);

			//Actions
			$selected.click($.ampSelect.open);
			$done.click($.ampSelect.close);

			//Setup Box
			$box
				.data('o', o)
				.data('selected', $selected)
				.data('placeholder', $select.attr('data-placeholder') || 'Select Items...')
				.data('options', $options)
				.data('checkall', $checkall)
				.append($options)
				.append($actions.append($done))
				.ampSelect('assignSelect', $select);

			$box.ampModal({
				title:   $title.prepend($checkall),
				context: $select.parent()
			});

			if ($selected.is(':visible')) {
				$selected.width($selected.width())
			}
		});
	},

	open: function () {
		var $box = $(this).data('box') || $(this).closest('.amp-select-box');
		$box.closest('.amp-modal').ampModal('open');
	},

	close: function () {
		var $box = $(this).data('box') || $(this).closest('.amp-select-box');
		$box.ampModal('close');
	},

	checkall: function (checked) {
		var $box = $(this).data('box') || $(this).closest('.amp-select-box');
		$box.data('options').find('.amp-option input').prop('checked', typeof checked === 'boolean' ? checked : $box.data('checkall').find('input').is(':checked')).first().change();
	},

	sortable: function (s) {
		var $box = $(this).data('box') || $(this).closest('.amp-select-box');

		o = $box.data('o') || {};
		o.sortable = s || {}

		!$box.data('options') || $box.data('options').sortable(o.sortable);
	},

	assignSelect: function ($select) {
		var $box = $(this).data('box') || $(this).closest('.amp-select-box');
		var $options = $box.data('options');

		$options.children().remove();

		$select.find('option').each(function (j, o) {
			var $o = $(o);

			$options.append(
				$('<label />').addClass('amp-option checkbox')
					.append($('<input/>').attr('type', 'checkbox').attr('value', $o.attr('value')).prop('checked', $o.is(':selected')))
					.append($('<span/>').addClass('label').html($o.html()))
			);
		});

		$options.find('.amp-option input').change($.ampSelect.update)

		$select.data('box', $box);
		$select.change($.ampSelect.refresh);
		$box.data('select', $select);

		if ((s = $box.data('o').sortable)) {
			$box.ampSelect('sortable', typeof s === 'object' ? s : {});
		}

		$box.ampSelect('update')
	},

	update: function () {
		var $box = $(this).data('box') || $(this).closest('.amp-select-box'), value = [], placeholder = '';

		$box.find('.amp-option input').each(function (i, o) {
			$o = $(o);
			if ($o.is(':checked')) {
				value.push($o.attr('value'));
				placeholder += (placeholder ? ', ' : '') + $o.siblings('.label').html()
			}
		})

		$box.data('select').val(value)
		$box.data('selected').find('.value').html(placeholder || $box.data('placeholder'));
	},

	refresh: function () {
		var $box = $(this).data('box') || $(this).closest('.amp-select-box');
		var $options = $box.data('options');

		$box.data('select').find('option').each(function (i, o) {
			$o = $(o);
			$options.find('[value=' + $o.attr('value') + ']').prop('checked', $o.is(':selected'))
		})

		$box.ampSelect('update');
	}
})

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

$.ampResize = $.fn.ampResize = function (o) {
	return $.amp.call(this, $.ampResize, arguments);
}

$.extend($.ampResize, {
	init: function (o) {
		o = $.extend({}, {
			on: 'keyup'
		}, o);

		var $canvas = $('<canvas/>').css({position: 'absolute', top: 0, left: -9999});
		$('body').append($canvas);

		if (!$.ampResize.ctx) {
			$.ampResize.ctx = $canvas[0].getContext('2d');
		}

		return this.on(o.on, $.ampResize.update).ampResize('update');
	},

	update: function () {
		$(this).each(function (i, e) {
			var $e = $(e);
			$.ampResize.ctx.font = $e.css('font');
			var val = $e.val();
			$e.css('text-transform') === 'uppercase' ? val = val.toUpperCase() : 0;
			$e.width($.ampResize.ctx.measureText(val).width + 'px');
		})
	}
})

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

$.fn.sortElements = function (comparator) {
	var $this = this;

	if (!comparator) {
		comparator = function (a, b) {
			return +$(a).attr('data-sort-order') > +$(b).attr('data-sort-order') ? 1 : -1;
		}
	}

	[].sort.call($this.children(), comparator).each(function (i, e) {
		$this.append($(e));
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
		callback:  null,
		toggle:    false,
		pushState: true
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
		var title = $this.attr('data-title'), is_url = !$this.attr('href').match(/^[#.]/);
		var $content = is_url ? $($this.attr('data-replace') || 'main.main') : $($this.attr('href'));

		if (is_url) {
			return;
		} else {
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
		}


		if ($tabs.o.pushState) {
			var id = $content.attr('id');
			var url = location.href.replace(/#.*/, '') + (id ? '#' + id : '');

			if (url !== location.href) {
				history.pushState({url: url}, title || $this.text(), url);
			}
		}

		if (title) {
			document.title = title;
		}

		if (typeof $tabs.o.callback === 'function') {
			$tabs.o.callback.call($this, $content);
		}

		return false;
	});

	if (window.location.hash) {
		$t = $tabs.filter('[href=' + window.location.hash + ']');
		$t.length ? $t.click() : $tabs.first().click();
	} else {
		$tabs.first().click();
	}

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

	if (typeof type === 'object') {
		options = msg;
		msg = type;
		type = null;
	}

	if (typeof msg === 'undefined' || msg === null) {
		msg = type;
		type = null;
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

	if (options.clear) {
		(options.inline ? this : $('#message-box')).find('.messages').remove();
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

		if (!$e.length) {
			return false;
		}

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
			$.fn.show_msg.count[type] = ($.fn.show_msg.count[type] || 0) + 1;

			setTimeout(function () {
				if ($.fn.show_msg.count[type]-- >= 1) {
					$box.slideToggle(500, function () {
						$(this).remove();
					});
				}
			}, options.delay);
		}
	});
}

$.fn.show_msg.count = {}

$.show_msg = function (type, msg, options) {
	$('body').show_msg(type, msg, options);
}

$.loading = $.fn.loading = function (params) {
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

function register_confirms() {
	var $confirms = $('[data-confirm], [data-confirm-modal]').use_once();

	$confirms.click(function () {
		var $this = $(this);

		if ($this.prop('disabled')) {
			return false;
		}

		if ($this.is('[data-confirm]') && !$this.hasClass('confirm')) {
			setTimeout(function () {
				$this.removeClass('confirm').loading('stop');
			}, 5000);
			$this.loading({text: $this.attr('data-confirm') || "Confirm?", disable: false}).addClass('confirm');

			return false;
		}

		if ($this.is('[data-confirm-modal]')) {
			$.ampConfirm({
				text:      $this.attr('data-confirm-modal') || "Are you sure you want to continue?",
				onConfirm: function () {
					if ($this.is('[data-ajax]')) {
						$this.hasClass('ajax-call') ? amplo_ajax_cb.call($this) : $.get($this.attr('href'), {}, function (response) {
							$.show_msg(response);
						});
					} else {
						location = $this.loading().attr('href')
					}
				}
			})

			return false;
		}

		if ($this.hasClass('ajax-call')) {
			amplo_ajax_cb.call($this);
			return false;
		}
	});

	$('.action-delete').use_once().click(function () {
		return confirm("Deleting this entry will completely remove all data associated from the system. Are you sure?");
	});
}

function register_ajax_calls(is_ajax) {
	$('form').use_once('data-loading-set').submit(function () {
		$(this).find('button[data-loading]').loading();
	});

	$((is_ajax ? '[data-if-ajax],' : '') + '[data-ajax]').use_once('ajax-call').not('[data-confirm], [data-confirm-modal]').amplo_ajax();
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

$.ampFormEditor = $.fn.ampFormEditor = function () {
	return $.amp.call(this, $.ampFormEditor, arguments);
}

$.extend($.ampFormEditor, {
	init: function (o) {
		return this.use_once('form-editor-enabled').each(function (i, e) {
			var $fe = $(e);
			var $form = $fe.is('form') ? $fe : $fe.find('form');

			$fe.find('.edit-form').click($.ampFormEditor.edit);
			$fe.find('.toggle-form').click($.ampFormEditor.toggle);
			$fe.find('.cancel-form').click($.ampFormEditor.read);

			$form.find('[data-disable]').focus($.ampFormEditor._disableField);
			$form.not('[data-noajax]').submit($.ampFormEditor.submitForm);
		});
	},

	edit: function () {
		var $form = $(this).closest('.form-editor').removeClass('read').addClass('edit');
		$form.find('[readonly]').attr('data-readonly', 1).removeAttr('readonly').trigger('editing');
		return false;
	},

	toggle: function () {
		var $form = $(this).closest('.form-editor');
		$form.ampFormEditor($form.hasClass('read') ? 'edit' : 'read');
		return false;
	},

	read: function () {
		var $form = $(this).closest('.form-editor').removeClass('edit').addClass('read');
		$form.find('[data-readonly]').attr('readonly', '');
		$form.find('[data-disable]').blur().trigger('reading');
		return false;
	},

	submitForm: function () {
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

				$form.ampFormEditor('read');
			}
		}, 'json');

		return false;
	},

	_disableField: function () {
		if ($(this).closest('.form-editor-enabled').hasClass('read')) {
			$(this).blur();
		}
	}
});

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
		var $replace = ajax_cb !== 'iframe' ? $(ajax_cb) : null;

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
		}, 'json').always(function () {
			$btns.loading('stop');
		});
	});
}

$.fn.liveForm = function (params) {
	params = $.extend({}, {
		callback: null
	}, params);

	return this.use_once('live-form-enabled').each(function (i, e) {
		var $form = $(e);
		$form.find('[name]').change(function () {
			$(this).closest('form').submit();
		});

		$form.submit(function () {
			$.post($form.attr('action'), $form.serialize(), params.callback, 'json');
			return false;
		});
	});
}

function no_parent_scroll(e) {
	if (e.touches) {
		//e.preventDefault();
	} else {
		var t = this, d = e.originalEvent.wheelDelta;

		if ((d > 0 && t.scrollTop <= 0) || (d < 0 && ((t.scrollTop + $(t).outerHeight()) >= t.scrollHeight))) {
			e.preventDefault();
		}
	}
}

function content_loaded(is_ajax) {
	$('select.amp-select').ampSelect();
	$('input[data-amp-resize]').ampResize();

	$('.no-parent-scroll').use_once('stop-scroll-prop').on('mousewheel DOMMouseScroll scroll touchmove', no_parent_scroll)

	var $forms = $('form');

	$forms.find('[name=username], [name=name], [name=email], [name=password], [name=confirm]').prop('autocorrect', false).attr('autocorrect', 'off');

	if ($ac.defer_scripts) {
		var scripts = '';

		$('script').each(function (i, e) {
			if ($(e).attr('type') === 'text/defer-javascript') {
				if ($(e).attr('src')) {
					$.error('External script ' + $(e).attr('src') + ' cannot be loaded synchronously with defer_scripts enabled. Use $.getScript() to load asynchronously or use $this->document->addScript() in your PHP Controller class.');
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

	if ($ac.show_msg_delay && $('.messages').length) {
		setTimeout(function () {
			$('.messages').slideToggle(500, function () {
				$(this).remove()
			});
		}, $ac.show_msg_delay);
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

	.click(function (e) {
		var $n = $(e.target);

		if ($n.is('a[data-loading]')) {
			$n.loading();
		}

		if ($n.is('a.cancel, a.back') && $n.closest('#colorbox').length) {
			$.colorbox.close();
			return false;
		}

		if (($at = $n.closest('[data-amp-toggle]:not(.amp-toggle)')).length) {
			$at.ampToggle({content: $at.attr('data-amp-toggle') || $at, toggleClass: 'active'}).click();
		}

		if (($lm = $n.closest('.link-menu')).length) {
			if ($lm.is('.on-click')) {
				$lm.toggleClass('active');
			} else if ($lm.is('.on-expand') && $n.is('.expand')) {
				$lm.toggleClass('active');
				return false;
			}
		}

		// Multistate Checkboxes
		if ($n.is('[data-multistate]')) {
			var val = $n.val();
			var states = $n.attr('data-multistate').split(';');

			if (!$n.prop('checked')) {
				for (var s = 0; s < states.length; s++) {
					if (states[s] === val) {
						if (s < states.length - 1) {
							$n.val(states[s + 1]);
						} else {
							$n.val(states[0]);
							return true;
						}
					}
				}
				return false;
			}
		}
	})

	.keyup(function (e) {
		var $n = $(e.target);

		if (!$.pageUnloading && e.keyCode === 13 && $n.is('input[type=text], input[type=password]') && $n.closest('form').length) {
			$n.closest('form').submit();
			return false;
		}
	})

	.keydown(function (e) {
		var $n = $(e.target), $form;

		if (e.ctrlKey && e.keyCode === 83 && ($form = $('form.ctrl-save')).length) {
			$form.submit_ajax_form({
				callback: function (response) {
					//Redirect from new form to edit form
					if (response.data) {
						for (var id in response.data) {
							var regx = new RegExp(id + '=\\d+');

							if (!location.href.match(regx)) {
								location = location.href.replace(/#.*/, '') + (location.href.indexOf('?') > 0 ? '&' : '?') + id + '=' + response.data[id];
							}
						}
					}

					$form.show_msg(response);

					if (!response.error && $form.closest('#colorbox').length) {
						$.colorbox.close();
					}
				}
			});
			e.preventDefault();
			return false;
		}
	})

	.ajaxComplete(function () {
		content_loaded(true);
	});

$(window).on('beforeunload', function () {
	$.pageUnloading = true;
})

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

if (!window.console) console = {
	log: function (m) {
		$.ampAlert(m)
	}
};
