var $ac = $ac || {}

//Similar to LESS screen sizing
var screen_width = (window.innerWidth > 0) ? window.innerWidth : screen.width;
var screen_lg = screen_width >= 1200,
	screen_md = screen_width >= 768 && screen_width < 1200,
	screen_sm = screen_width >= 480 && screen_width < 768,
	screen_xs = screen_width < 480;

$('body').toggleClass('webkit', /AppleWebKit/.test(navigator.userAgent));

$(document)
	.ready(function() {
		$('.ui-autocomplete-input').on("autocompleteselect", function(e, ui) {
			if (!ui.item.value && ui.item.href) {
				window.open(ui.item.href);
			}
		});

		content_loaded();

		$('body').removeClass('is-loading');

		if (msg = $.cookie('message')) {
			$('body').show_msg(msg);
			$.cookie('message', null);
		}
	})

	.click(function(e) {
		var $n = $(e.target);

		if ($n.is('a[data-loading]')) {
			$n.loading();
		}

		if ($n.is('a.cancel, a.back') && $n.closest('#colorbox').length) {
			$.colorbox.close();
			return false;
		}

		if (($onClick = $n.closest('[data-amp-toggle], .on-click')).length) {
			if ($onClick.is('[data-amp-toggle]:not(.amp-toggle)')) {
				$onClick.ampToggle({
					content: $onClick.attr('data-amp-toggle') || $onClick,
				}).click();
			} else {
				$onClick.toggleClass('is-active');

				if ($onClick.is('.link-menu')) {
					$onClick.toggleClass('active');
				}
			}
		}

		if ($n.is('.expand')) {
			$n.closest('.on-expand').toggleClass('active');
			return false;
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

	.keyup(function(e) {
		var $n = $(e.target);

		if (!$.pageUnloading && e.keyCode === 13 && $n.is('input[type=text], input[type=password]') && $n.closest('form').length) {
			$n.closest('form').submit();
			return false;
		}
	})

	.keydown(function(e) {
		var $n = $(e.target), $form;

		if (e.ctrlKey && e.keyCode === 83 && ($form = $('form.ctrl-save')).length) {
			var $reloadOnNew = $form.attr('data-reload-on-new') !== 'false';

			$form.trigger('ctrl-save-submit');

			$form.submit_ajax_form({
				callback: function(response) {

					//Redirect from new form to edit form
					if (response.data) {
						for (var id in response.data) {
							if (typeof response.data[id] !== 'object') {
								var regx = new RegExp(id + '=\\d+');

								if ($reloadOnNew && !location.href.match(regx)) {
									return location = location.href.replace(/#.*/, '').replace(regx, '') + (location.href.indexOf('?') > 0 ? '&' : '?') + id + '=' + response.data[id];
								}
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

	.change(function(e) {
		var $t = $(e.target);

		if ($t.is('[data-sync-field]')) {
			$('[data-sync-listener]').html($t.val());
		}
	})

	.ajaxComplete(function() {
		content_loaded(true);
	});

Function.prototype.loop = function(time, count) {
	var fn = this;
	setTimeout(function() {
		(fn(count = (+count || 0) - 1) === false || !count) ? 0 : fn.loop(time, count)
	}, time);
}

String.prototype.toSlug = function(sep) {
	return this.toLowerCase().replace(/\s/, sep || '-').replace(/[^a-z0-9-_]/, '');
}

String.prototype.repeat = function(times) {
	return (new Array(times + 1)).join(this);
};

String.prototype.str_replace = function(find, replace) {
	var str = this;
	for (var i = 0; i < find.length; i++) {
		str = str.replace(find[i], replace[i]);
	}
	return str;
};

String.prototype.toCurrency = Number.prototype.toCurrency = function(params) {
	var n = parseFloat(this);
	params = $.extend({}, $ac.currency, params);

	return (n < 0 ? params.neg : params.pos) + params.symbol_left + Math.abs(n).formatNumber() + params.symbol_right;
}

String.prototype.formatNumber = Number.prototype.formatNumber = function(params) {
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

Number.prototype.roundFloat = function(p) {
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

		$[fn] = function() {
			autoload_js_file.call(this, url, arguments, 'base')
		}

		$.fn[fn] = function() {
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
					.done(function() {
						if (load_count++ >= url.length) {
							for (var l in $ac.alq[al]) {
								var q = $ac.alq[al][l];
								(type === 'fn' ? $.fn[q.fn] : $[q.fn]).apply(q.me, q.args);
							}

							$(document).trigger(al);
						}
					}).always(function(jqXHR, status, msg) {
					if (status !== 'success') {
						$.error('There was an error loading the autoloaded file: ' + url + ": " + msg);
						$.error(jqXHR);
					}
				});
			}
		}
	}

	$ac.alq[al].push({fn: fn, me: this, args: args, type: type});

	return this;
}

$.fn.use_once = function(label) {
	label = label || 'activated';
	return this.not('.' + label).addClass(label);
}

$.fn.scrollTo = function(target, options) {
	target = $(target);

	if (!target.length) {
		return false;
	}

	var $this = this;
	var $header = $('header.site-header');

	options = $.extend({}, {
		offset:   $header.css('position') === 'fixed' ? -$header.outerHeight() : 0,
		callback: null
	}, options);

	var top = target.offset().top + options.offset;
	this.stop();

	$this.animate({scrollTop: top}, {
		duration: 1000, complete: function(e) {
			if (typeof options.callback == 'function') {
				options.callback(e);
			}
		}
	});
}

//Extend amp protocol for jQuery plugins
$.fn.setOptions = function(o) {
	return this.each(function() {
		$(this).data('o', $.extend(true, {}, $(this).data('o'), o));
	})
}

$.fn.getOptions = function() {
	return this.data('o');
}

$.ampExtend = function(a, m) {
	if (typeof a !== 'string') {
		for (var i in $) {
			if ($[i] === a) {
				a = i;
			}
		}
	}

	$.fn[a] = function(o) {
		var p = $[a];
		var o = arguments[0];

		if (p[o]) {
			return p[o].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof o === 'object' || !o) {
			return p.init.apply(this, arguments);
		} else {
			$.error('Method ' + o + ' does not exist for jQuery plugin ' + p.name);
		}
	}

	if (typeof $[a] !== 'function') {
		$[a] = $.fn[a];
	}

	return $.extend($[a], {
		init: function(o) {
			$.error('Override the init method for the ampExtend plugin ' + a);
		},
	}, m);
}

//ampNestedForm jQuery Plugin
$.ampExtend($.ampNestedForm = function() {}, {
	init: function(o) {
		var $forms = this.use_once().addClass('amp-nested-form');

		o = $.extend({}, {
			onSubmit:      null,
			onDone:        null,
			onFail:        null,
			onAlways:      null,
			disableFields: true,
			fields:        null
		}, o)

		return $forms.each(function() {
			var $form = $(this);

			if (!o.fields) {
				o.fields = $form.find('[name]')
			}

			if (o.disableFields) {
				$form.closest('form').submit(function() {
					o.fields.prop('disabled', true);
					setTimeout(function() {
						o.fields.prop('disabled', false);
					}, 500);
				})
			}

			$form
				.setOptions(o)
				.keyup(function(e) {
					if (e.keyCode === 13) {
						$(this).closest('.amp-nested-form').submit();
						e.stopPropagation();
						return false;
					}
				});

			$form.click(function(e) {
				if ($(e.target).is('button, input[type=submit]')) {
					$(this).submit();
					e.stopPropagation();
					return false;
				}
			})

			$form.submit(function(e) {
				var $form = $(this).closest('.amp-nested-form');
				var o = $form.getOptions();

				var is_valid = o.onSubmit ? o.onSubmit.call($form) !== false : true;

				if (is_valid) {
					$form.find('button').loading();

					$.post($form.attr('data-action'), $form.find('[name]').serialize(), function(r, status) {
							if (typeof o.onDone === 'function') {
								o.onDone.call($form, r, status)
							}
						})
						.fail(function(jqXHR, status, error) {
							if (typeof o.onFail === 'function') {
								o.onFail.call($form, jqXHR, status, error);
							}
						})
						.always(function(jqXHR, status, error) {
							$form.find('button').loading('stop');

							if (typeof o.always === 'function') {
								o.always.call($form, jqXHR, status, error);
							}
						})
				}

				e.stopPropagation();
				return false;
			})
		})
	},

	onSubmit: function(callback) {
		this.setOptions({onSubmit: callback});
	},

	onDone: function(callback) {
		this.setOptions({onDone: callback});
	},

	onAlways: function(callback) {
		this.setOptions({onAlways: callback});
	},

	onFail: function(callback) {
		this.setOptions({onFail: callback});
	}
})

//ampFormat jQuery Plugin
$.ampExtend($.ampFormat = function() {}, {
	init: function(o) {
		o = $.extend({}, {
			type:     'float',
			unsigned: false
		}, o);

		this.setOptions(o);

		this.keypress(function(e) {
			return $(this).ampFormat('validate', e);
		})

		return this;
	},

	validate: function(e, type) {
		var o = this.getOptions();

		var type = typeof type === 'string' ? type : o.type,
			char = String.fromCharCode(e.keyCode);

		switch (type) {
			case 'float':
				return o.unsigned ? !!char.match(/[0-9\.]/) : !!char.match(/[0-9\.\-]/);

			case 'integer':
				return o.unsigned ? !!char.match(/[0-9]/) : !!char.match(/[0-9\-]/);
		}
	}
})

//ampDelay jQuery Plugin
$.ampExtend($.ampDelay = function() {}, {
	init: function(o) {
		o = $.extend({}, {
			delay:    1000,
			callback: null,
			on:       null
		}, o);

		o.count = 0;

		this.setOptions(o);

		if (!o.on) {
			this.ampDelay('countdown');
		} else {
			this.on(o.on, function() {
				$(this).ampDelay('countdown');
			})
		}

		return this;
	},

	countdown: function() {
		var $this = this;
		var o = $this.getOptions()

		o.count++;

		setTimeout(function() {
			if (--o.count <= 0) {
				o.callback.call($this);
			}
		}, o.delay)
	},
})

//ampToggle jQuery Plugin
$.ampExtend($.ampToggle = function() {}, {
	init: function(o) {
		if (!o) {
			$.error("ampToggle parameter error: content must be an existing DOM element");
			return this;
		}

		o = $.extend({}, {
			toggle:              this,
			content:             this,
			hideContent:         null,
			activeClass:         'is-active',
			activeContentClass:  'is-active',
			dormantClass:        'is-dormant',
			dormantContentClass: 'is-dormant',
			start:               'dormant',
			acceptParent:        '',
			blurOnModal:         false,
			onShow:              null,
			onHide:              null
		}, o);

		o.toggle = $(o.toggle || this);
		o.content = $(o.content || this);

		o.toggle.setOptions(o);
		o.content.setOptions(o);

		//Hide content when dormant if set in options or toggle is not a child of content
		if (o.hideContent || (o.hideContent === null && !o.content.find(this).length)) {
			o.content.addClass('on-active');
		}

		o.content.click(function(e) {
			var $target = $(e.target);

			if ($target.closest('.amp-toggle-off').length) {
				$(this).ampToggle('setDormant');
			} else if ($target.closest('.amp-toggle-on').length) {
				$(this).ampToggle('setActive');
			}
		})

		if (o.toggle.length) {
			o.content.addClass('amp-toggle-content');

			o.toggle.addClass('amp-toggle').click(function(e) {
				$.ampToggle.skipToggle ? $.ampToggle.skipToggle = false : o.toggle.ampToggle(o.toggle.hasClass(o.activeClass) ? 'setDormant' : 'setActive');
				e.stopPropagation();
				return false;
			})

			if (o.start) {
				o.toggle.ampToggle(o.start === 'active' ? 'setActive' : 'setDormant');
			}
		}

		return this;
	},

	_blur: function(e) {
		var $t = $(e.target), o = $.ampToggle.active.getOptions();

		if ($t.closest(o.content).length || ($t.closest('.amp-modal').length && !o.blurOnModal)) {
			!o.content.is('.amp-toggle') || ($.ampToggle.skipToggle = true);
		} else if (!$t.closest(o.toggle).length && !$t.closest(o.acceptParent).length) {
			$.ampToggle.active.ampToggle('setDormant');

			if ($t.hasClass('amp-toggle')) {
				$.ampToggle.skipToggle = true;
			}
		}
	},

	setActive: function() {
		var $this = $(this);
		var o = $this.getOptions();

		o.toggle.addClass(o.activeClass).removeClass(o.dormantClass);
		o.content.addClass(o.activeContentClass).removeClass(o.dormantContentClass);
		$.ampToggle.active = $this;
		setTimeout(function() {
			document.addEventListener('click', $.ampToggle._blur, true);
		}, 100);


		if (typeof o.onShow === 'function') {
			o.onShow.call(this, o);
		}
	},

	setDormant: function() {
		var $this = $(this);
		var o = $this.getOptions();

		o.toggle.removeClass(o.activeClass).addClass(o.dormantClass);
		o.content.removeClass(o.activeContentClass).addClass(o.dormantContentClass);
		$.ampToggle.active = null;
		document.removeEventListener('click', $.ampToggle._blur, true);

		if (typeof o.onHide === 'function') {
			o.onHide.call(this, o);
		}
	}
});

//ampYouTube
$.ampExtend($.ampYouTube = function() {}, {
	players: null,

	init: function(o) {
		o = $.extend({}, {
			width:      null,
			height:     null,
			ratio:      .6,
			playlistId: '',
			videoId:    '',
			paused:     true,
			onChange:   null,
			onReady:    null,
			apiUrl:     'https://www.googleapis.com/youtube/v3/',
			apiKey:     null,
			inModal:    true
		}, o);

		var tag = document.createElement('script');
		tag.src = "https://www.youtube.com/iframe_api";
		var firstScriptTag = $('script')[0];
		firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

		o.width = o.width || this.width();
		o.height = o.height || (o.ratio * o.width) || this.height();

		this.width(o.width);
		this.height(o.height);

		o.id = this.attr('id');

		if (o.inModal) {
			o.modal = this.ampModal({
				onClose: function() {
					$.ampYouTube.getInstance(this.find('iframe').attr('id')).ampYouTube('pause')
				}
			}).ampModal('getBox');
		}

		this.setOptions(o);

		if (!$.ampYouTube.players) {
			$.ampYouTube.players = this;
		} else {
			$.ampYouTube.players.add(this);
		}

		return this;
	},

	getInstance: function(id) {
		return $.ampYouTube.players.filter('#' + id)
	},

	playlistItems: function(callback, params) {
		var o = this.getOptions();

		if (!callback) {
			$.error("You must provide a callback function.");
		} else {
			params = $.extend({}, {
				playlistId: o.playlistId,
				part:       'id,snippet'
			}, params);

			params.key = o.apiKey;

			$.get(o.apiUrl + 'playlistItems', params, null).always(function(response, a, b) {
				callback(response.responseJSON || response);
			})
		}

		return this;
	},

	initPlayer: function() {
		var o = this.getOptions();

		var player = new YT.Player(this.attr('id'), {
			width:   o.width,
			height:  o.height,
			videoId: o.videoId,
			events:  {
				onReady:       $.ampYouTube.onReady,
				onStateChange: $.ampYouTube.onChange
			}
		});

		player.ampO = o;

		o.player = player;
	},

	play: function(id) {
		var o = this.getOptions();

		if (o.inModal) {
			o.modal.ampModal('open')
		}

		if (id) {
			o.player.loadVideoById(id)
		}

		o.player.ampO.playing = true;
		o.player.playVideo();
	},

	pause: function() {
		var o = this.getOptions();

		o.player.ampO.playing = false;
		o.player.pauseVideo();
	},

	playIndex: function(i) {
		var o = this.getOptions();

		o.player.playVideoAt(i);
	},

	onReady: function(e) {
		e.target.loadPlaylist({
			list: e.target.ampO.playlistID
		});

		e.target.ampO.playing = false;

		if (typeof e.target.ampO.onReady === 'function') {
			e.target.ampO.onReady.call(this, e);
		}
	},

	onChange: function(e) {
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
	$.ampYouTube.players.each(function(i, e) {
		$(e).ampYouTube('initPlayer');
	});
}

//periodic Cookie Token Checker
$.ampTokenCheck = function(token, callback, delay) {
	token = token || 'at-' + Math.floor(Math.random() * 99999999);

	if ($.cookie(token)) {
		$.cookie(token, null);
		callback(token);
	} else {
		setTimeout(function() {
			$.ampTokenCheck(token, callback, delay);
		}, delay || 250);
	}

	return token;
}

//ampModal jQuery Plugin
$.ampExtend($.ampModal = function() {}, {
	init: function(o) {
		if (typeof this === 'function' && !o.content) {
			return $.error('Error ampModal: You must specify a value for content.');
		}

		o = $.extend({}, {
			context:     null,
			title:       '',
			class:       '',
			content:     null,
			buttons:     {},
			shadow:      true,
			shadowClose: true,
			onAction:    null,
			onOpen:      null,
			onClose:     null,
			show:        false,
			width:       null,
			height:      null
		}, o);

		o.content = o.content === null ? this : o.content;
		o.context = o.context === null ? o.content.parent() : o.context;

		if (!(o.context = $(o.context)).length) {
			o.context = $('body');
		}

		return $(o.content).use_once('amp-modal-enabled').setOptions(o).each(function(i, e) {
			var $e = $(e),
				$box = $('<div />').addClass('amp-modal').addClass(o.class).setOptions(o),
				$contentBox = $('<div />').addClass('amp-modal-content-box'),
				$content = $('<div />').addClass('amp-modal-content'),
				$title = $('<div/>').addClass('amp-modal-title');

			o.context.append($box);

			!o.width || $contentBox.css('width', o.width);
			!o.height || $contentBox.css('height', o.height);

			$content
				.append(o.title ? $title.html(o.title) : '')
				.append($e)

			if (!$.isEmptyObject(o.buttons)) {
				var $buttons = $('<div/>').addClass('amp-modal-buttons');

				for (var b in o.buttons) {
					btn = o.buttons[b];

					if (!btn.action) {
						o.buttons[b].action = function() {
							$(this).ampModal('close');
						}
					}

					var $btn = $('<button/>')
						.addClass('amp-modal-button ' + b)
						.attr('data-action', b)
						.data('action-callback', btn.action)
						.append(btn.label || b)
						.appendTo($buttons)

					if (btn.attr) {
						$btn.attr(btn.attr);
					}

					if (btn.action) {
						$btn.click(function(e) {
							var $btn = $(this);
							return $btn.data('action-callback').call(this, $btn.closest('.amp-modal'), e)
						})
					}

					if (typeof o.onAction === 'function') {
						$btn.click(function(e) {
							o.onAction.call(this, $(this).closest('.amp-modal'), $(this).attr('data-action'), e);
						})
					}
				}

				$content.append($buttons);
			}

			$box
				.append($('<div/>').addClass('align-middle'))
				.append($contentBox.append($content))

			if (o.shadow) {
				var $shadow = $('<div/>').addClass('shadow-box').appendTo($box);

				if (o.shadowClose) {
					$shadow.click($.ampModal.close)
				}
			}

			if (o.show) {
				$box.ampModal('open');
			}
		});
	},

	open: function() {
		var $this = $(this).closest('.amp-modal').addClass('active')
		var o = $this.getOptions() || {};

		if (typeof o.onOpen === 'function') {
			o.onOpen.call($this);
		}

		return $this;
	},

	close: function() {
		var $this = $(this).closest('.amp-modal').removeClass('active')
		var o = $this.getOptions() || {};

		if (typeof o.onClose === 'function') {
			o.onClose.call($this);
		}

		return $this;
	},

	getBox: function() {
		return this.closest('.amp-modal');
	}
});

$.ampAlert = $.fn.ampAlert = function(o) {
	o = $.extend({}, {
		title:       'Warning!',
		class:       'amp-modal-alert',
		content:     $('<div/>').addClass('amp-alert'),
		text:        typeof o === 'string' ? o : 'This warning was generated by Amplo MVC',
		onClose:     function() {
			this.remove()
		},
		shadowClose: false,
		buttons:     {
			ok: {
				label: 'Ok',
			},
		},
		show:        true
	}, o)

	o.content = $(o.content).append(o.text);

	return o.content.ampModal(o);
}

$.ampConfirm = $.fn.ampConfirm = function(o) {
	o = $.extend({}, {
		title:       'Are you sure?',
		class:       'amp-modal-confirm',
		content:     $('<div/>').addClass('amp-confirm'),
		context:     $('body'),
		text:        'Are you sure you want to continue?',
		onConfirm:   null,
		onCancel:    null,
		onClose:     function() {
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
		},
		show:        true
	}, o)

	if (o.content && o.text) {
		o.content = $(o.content).append(o.text);
	}

	if (o.buttons.confirm && !o.buttons.confirm.action) {
		o.buttons.confirm.action = function() {
			if (typeof o.onConfirm === 'function') {
				o.onConfirm.call(this, $(this).closest('.amp-modal'));
			}

			$(this).ampModal('close');
		}
	}

	if (o.buttons.cancel && !o.buttons.cancel.action) {
		o.buttons.cancel.action = function() {
			if (typeof o.onCancel === 'function') {
				o.onCancel.call(this, $(this).closest('.amp-modal'));
			}

			$(this).ampModal('close');
		}
	}

	return o.content.ampModal(o);
}

//ampSelect jQuery Plugin
$.ampExtend($.ampSelect = function() {}, {
	init: function(o) {
		o = $.extend({}, {}, o);

		return this.use_once('amp-select-enabled').each(function(i, e) {
			o.select = $(e).removeClass('amp-select');

			var $ampSelect = $("<div />").addClass('amp-select');

			if (o.select.is('.amp-select-checkboxes')) {
				o.checkboxes = true;
			}

			//Save Options to Instance
			$ampSelect.setOptions($.extend({}, o, {
				placeholder: o.select.attr('data-placeholder') || 'Select Items...'
			}));

			//Build Template
			o.select.before($ampSelect);
			$ampSelect.append(o.select);

			if (o.checkboxes) {
				$ampSelect.ampSelect('initCheckboxes');
			} else {
				$ampSelect.ampSelect('initSelectModal');
			}

			$ampSelect.ampSelect('assignSelect', o.select);
		});
	},

	initCheckboxes: function() {
		var $box = $('<div/>').addClass('amp-select-box amp-select-checkboxes'),
			$options = $('<div/>').addClass('amp-select-options no-parent-scroll');

		return this.append($box.append($options));
	},

	initSelectModal: function() {
		var $ampSelect = this;
		var o = $ampSelect.getOptions();

		var $selected = $("<div />")
			.addClass(o.select.attr('class').replace('amp-select-enabled', 'amp-selected'))
			.append($('<div/>').addClass('align-middle'))
			.append($('<div/>').addClass('value'))
			.append($('<div/>').addClass('amp-select-button').append($('<div />').addClass('align-middle no-ws-hack')).append($('<div />').addClass('amp-select-button-icon fa fa-ellipsis-h')));

		var $box = $('<div/>').addClass('amp-select-box amp-select-modal'),
			$options = $('<div/>').addClass('amp-select-options no-parent-scroll'),
			$checkall = $('<label/>').addClass('amp-select-checkall checkbox white').append($('<input/>').attr('type', 'checkbox')).append($('<span/>').addClass('label')),
			$actions = $('<div/>').addClass('amp-select-actions'),
			$done = $('<a/>').addClass('amp-select-done button').html('Done'),
			$title = $('<div/>').addClass('amp-select-title').append($('<div/>').addClass('text').html(o.select.attr('data-label') || 'Select one or more items'));

		$ampSelect.append($selected);

		//Events
		$checkall.find('input').change($.ampSelect.checkall);

		//Actions
		$selected.click($.ampSelect.open);
		$done.click($.ampSelect.close);

		//Setup Box
		$box.append($options).append($actions.append($done))

		$box.ampModal({
			title:   $title.prepend($checkall),
			context: $ampSelect
		});

		if ($selected.is(':visible')) {
			$selected.width($selected.width())
		}

		return this;
	},

	open: function() {
		$(this).closest('.amp-select').find('.amp-modal').ampModal('open');
	},

	close: function() {
		$(this).closest('.amp-select').find('.amp-modal').ampModal('close');
	},

	checkall: function(checked) {
		var $ampSelect = $(this).closest('.amp-select');
		$ampSelect.find('.amp-option input').prop('checked', typeof checked === 'boolean' ? checked : $ampSelect.find('.amp-select-checkall input').is(':checked')).first().change();
	},

	sortable: function(s) {
		var $ampSelect = $(this).closest('.amp-select');
		var o = $ampSelect.getOptions();

		$ampSelect.find('.amp-select-options').sortable(o.sortable || {});
	},

	assignSelect: function($select) {
		var $ampSelect = $(this).closest('.amp-select');
		var $options = $ampSelect.find('.amp-select-options');
		var o = $ampSelect.getOptions();

		$options.children().remove();

		$select.find('option').each(function(j, o) {
			var $o = $(o);

			$options.append(
				$('<label />').addClass('amp-option checkbox')
					.append($('<input/>').attr('type', 'checkbox').attr('value', $o.attr('value')).prop('checked', $o.is(':selected')))
					.append($('<span/>').addClass('label').html($o.html()))
			);
		});

		$options.find('.amp-option input').change($.ampSelect.update)
		$select.change($.ampSelect.refresh);

		if (o.sortable) {
			$ampSelect.ampSelect('sortable', typeof o.sortable === 'object' ? o.sortable : {});
		}

		$ampSelect.ampSelect('update')
	},

	update: function(trigger_change) {
		var $ampSelect = $(this).closest('.amp-select');
		var o = $ampSelect.getOptions();
		var value = [], placeholder = '';

		$ampSelect.find('.amp-option input').each(function(i, o) {
			$o = $(o);
			if ($o.is(':checked')) {
				value.push($o.attr('value'));
				placeholder += (placeholder ? ', ' : '') + $o.siblings('.label').html()
			}
		})

		var $select = $ampSelect.find('select').val(value);
		$ampSelect.find('.amp-selected .value').html(placeholder || o.placeholder);

		if (trigger_change) {
			$select.change();
		}
	},

	refresh: function() {
		var $ampSelect = $(this).closest('.amp-select');
		var $options = $ampSelect.find('.amp-select-options');
		var $select = $ampSelect.find('select');

		$options.find('input').prop('checked', false);
		var values = $select.val();

		for (var s in values) {
			$options.find('[value=' + values[s] + ']').prop('checked', true)
		}

		$ampSelect.ampSelect('update', false);
	}
})

//Add the date/time picker to the elements with the special classes
$.ac_datepicker = function(params) {
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
			$.getScript($ac.site_url + 'system/resources/js/jquery/ui/datetimepicker.js', function() {
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

	return this.each(function(i, e) {
		type = params.type ||
		$(e).hasClass('datepicker') ? 'datepicker' :
			$(e).hasClass('timepicker') ? 'timepicker' : 'datetimepicker';

		$(e)[type](params);
	});
}

//Apply a filter form to the URL
$.fn.apply_filter = function(url) {
	var filter_list = this.find('[name]');

	if (filter_list.length) {
		filter_list.each(function(i, e) {
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

$.ampExtend($.ampResize = function() {}, {
	init: function(o) {
		o = $.extend({}, {
			on: 'keyup change'
		}, o);

		var $canvas = $('<canvas/>').css({position: 'absolute', top: 0, left: -9999});
		$('body').append($canvas);

		if (!$.ampResize.ctx) {
			$.ampResize.ctx = $canvas[0].getContext('2d');
		}

		return this.on(o.on, $.ampResize.update).ampResize('update');
	},

	update: function() {
		$(this).each(function(i, e) {
			var $e = $(e);
			$.ampResize.ctx.font = $e.css('font');
			var val = $e.val();
			$e.css('text-transform') === 'uppercase' ? val = val.toUpperCase() : 0;
			$e.width($.ampResize.ctx.measureText(val).width + 'px');
		})
	}
})

//A jQuery Plugin to update the sort orders columns (or any column needing to be indexed)
$.fn.update_index = function(column) {
	column = column || '.sort_order';

	return this.each(function(i, ele) {
		count = 0;
		$(ele).find(column).each(function(i, e) {
			$(e).val(count++);
		});
	});
}

$.fn.sortElements = function(comparator) {
	var $this = this;

	if (!comparator) {
		comparator = function(a, b) {
			return +$(a).attr('data-sort-order') > +$(b).attr('data-sort-order') ? 1 : -1;
		}
	}

	[].sort.call($this.children(), comparator).each(function(i, e) {
		$this.append($(e));
	});

	return this;
}

$.fn.overflown = function(dir, tolerance) {
	return this.each(function(i, e) {
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

$.ampExtend($.ampTabs = function() {}, {
	init: function(o) {
		o = $.extend({}, {
			onShow:    null,
			toggle:    false,
			pushState: true,
			show:      this.filter('[href=' + location.hash + ']')
		}, o);

		o.tabs = this;

		o.tabs.click($.ampTabs.show);

		o.tabs.setOptions(o);

		if (o.show !== false) {
			o.show.length ? o.show.ampTabs('show') : o.tabs.first().ampTabs('show')
		}

		return this;
	},

	show: function() {
		var $this = $(this);
		var o = $this.getOptions();

		//Follow link if it is a URL
		if (!$this.attr('href') || !$this.attr('href').match(/^[#.]/)) {
			return;
		}

		var title = $this.attr('data-title');
		var $content = $($this.attr('href'));

		if (typeof o.toggle === 'function' ? o.toggle.call(o.tabs, $this) : o.toggle) {
			$this.toggleClass('active');
			$content.toggleClass('hidden', $this.hasClass('active'));
		} else {
			o.tabs.removeClass('active');

			o.tabs.each(function(i, e) {
				$($(e).attr('href')).addClass('hidden');
			});

			$this.addClass('active');
			$content.removeClass('hidden');
		}

		if (o.pushState) {
			var id = $content.attr('id');
			var url = location.href.replace(/#.*/, '') + (id ? '#' + id : '');

			if (url !== location.href) {
				history.pushState({url: url}, title || $this.text(), url);
			}
		}

		if (title) {
			document.title = title;
		}

		if (typeof o.onShow === 'function') {
			o.onShow.call($this, $content);
		}

		return false;
	}
});

$.fn.show_msg = function(type, msg, o) {
	var $context = $(this);

	if (type === 'clear') {
		return $context.find('.message').hide_msg({slideOut: false});
	}

	//Data types are not messages
	if (type === 'data') {
		return;
	}

	if (typeof type === 'object') {
		o = msg;
		msg = type;
		type = null;
	}

	if (typeof msg === 'undefined' || msg === null) {
		msg = type;
		type = null;
	}

	if (!msg) {
		return this;
	}

	o = $.extend({
		style:       'stacked',
		inline:      !!$ac.show_msg_inline,
		append:      true,
		append_list: false,
		delay:       false,
		close:       true,
		clear:       true,
		flagErrors:  true
	}, o);

	if (o.clear) {
		(o.inline ? $context : $('#message-box')).find('.message').hide_msg({slideOut: false});
	}

	if (typeof msg === 'object') {
		for (var m in msg) {
			o.clear = false;

			if (o.flagErrors && type === 'error') {
				$context.find('[name=' + m + '], [data-msg-error=' + m + ']').addClass('has-error');
			}

			$context.show_msg(type || m, msg[m], o);
		}
		return $context;
	}

	return $context.each(function(i, e) {
		var $e = o.inline ? $(e) : $('#message-box');

		if (!$e.length) {
			return false;
		}

		var $box = $e.find('.messages.' + type);

		if (!$box.length) {
			$box = $('<div />').addClass('messages ' + type + ' ' + o.style);

			if (o.close) {
				$box.append($('<div />').addClass('close').click(function() {
					$(this).closest('.messages').find('.message').hide_msg();
				}));
			}

			if (o.append) {
				$e.append($box);
			} else {
				$e.prepend($box);
			}
		}

		var $msg = $('<div />').addClass('message').html(msg);

		if (o.append_list) {
			$box.append($msg);
		} else {
			$box.prepend($msg);
		}

		if (o.delay) {
			setTimeout(function() {
				$msg.hide_msg();
			}, o.delay);
		}

		$box.removeClass('hidden');
		$('body').trigger('amp-show-msg', $msg);
	});
}

$.show_msg = function(type, msg, o) {
	$('body').show_msg(type, msg, o);
}

$.fn.hide_msg = function(o) {
	var $msgs = $(this).closest('.message');

	o = $.extend({}, {
		slideOut: true,
	}, o);

	$msgs.each(function(){
		var $this = $(this);
		var $box = $this.closest('.messages');

		if (o.slideOut) {
			$this.slideToggle(500, function() {
				$('body').trigger('amp-hide-msg', $this.remove());
				$box.toggleClass('hidden', !$box.children('.message').length)
			});
		} else {
			$('body').trigger('amp-hide-msg', $this.remove());
			$box.toggleClass('hidden', !$box.children('.message').length)
		}
	})

	return this;
}

$.loading = $.fn.loading = function(params) {
	return this.each(function(i, e) {
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
					setTimeout(function() {
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

$.fn.ac_zoneselect = function(params, callback) {
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

	params.listen.change(function() {
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

jQuery.fn.serializeObject = function() {
	var arrayData, objectData;
	arrayData = this.serializeArray();
	objectData = {};

	$.each(arrayData, function() {
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

$.cookie = function(key, value, o) {
	if (arguments.length > 1) {
		o = $.extend({}, {
			raw:     false,
			domain:  null,
			path:    '/',
			expires: 365,
			secure:  false
		}, o);

		if (value === null) {
			o.expires = -1;
		} else if (typeof value === "object") {
			value = JSON.stringify(value);
		}

		if (typeof o.expires === 'number') {
			var d = new Date();
			d.setDate(d.getDate() + o.expires);
			o.expires = d.toUTCString();
		}

		return (document.cookie = [
			encodeURIComponent(key), '=',
			o.raw ? String(value) : encodeURIComponent(String(value)),
			o.expires ? '; expires=' + o.expires : '', // use expires attribute, max-age is not supported by IE
			o.path ? '; path=' + o.path : '',
			o.domain ? '; domain=' + o.domain : '',
			o.secure ? '; secure' : ''
		].join(''));
	}

	// key and possibly options given, get cookie...
	o = value || {};
	var result;

	var decode = function(s) {
		if (o.raw) {
			return s;
		}
		s = decodeURIComponent(s);

		return JSON.parse(s) || s;
	}

	return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? decode(result[1]) : null;
};

function register_confirms() {
	var $confirms = $('[data-confirm], [data-confirm-modal]').use_once();

	$confirms.click(function() {
		var $this = $(this);

		if ($this.prop('disabled')) {
			return false;
		}

		if ($this.is('[data-confirm]') && !$this.hasClass('confirm')) {
			setTimeout(function() {
				$this.removeClass('confirm').loading('stop');
			}, 5000);
			$this.loading({text: $this.attr('data-confirm') || "Confirm?", disable: false}).addClass('confirm');

			return false;
		}

		if ($this.is('[data-confirm-modal]')) {
			$.ampConfirm({
				text:      $this.attr('data-confirm-modal') || "Are you sure you want to continue?",
				onConfirm: function() {
					if ($this.is('[data-ajax]')) {
						$this.hasClass('ajax-call') ? amplo_ajax_cb.call($this) : $.get($this.attr('href'), {}, function(response) {
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

	$('.action-delete').use_once().click(function() {
		return confirm("Deleting this entry will completely remove all data associated from the system. Are you sure?");
	});
}

function register_ajax_calls(is_ajax) {
	$('form').use_once('data-loading-set').submit(function() {
		var $this = $(this);
		$this.find('button[data-loading]').loading();
		$this.find('.has-error').removeClass('has-error');
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

		$colorbox.each(function(i, e) {
			var $e = $(e);
			defaults.photo = $e.hasClass('colorbox-photo');
			$e.colorbox(defaults);
		});
	}
}

$.fn.collapsible = function() {
	return this.each(function(i, e) {
		var $c = $(e);

		$c.click(function() {
			$(this).toggleClass('hide');
		});

		$c.find('.collapse, input, select, textarea, a').click(stopProp);
	});
}

function stopProp(e) {
	e.stopPropagation();
}

//ampAccordion jQuery Plugin
$.ampExtend('ampAccordion', {
	count: 0,

	init: function(o) {
		o = $.extend({}, {
			toggle:  '.amp-accordion-toggle',
			content: '.amp-accordion-content',
			flip:    '.amp-accordion-flip',
			show:    null
		}, o);

		return this.addClass('amp-accordion').use_once().each(function(i, e) {
			var $e = $(e).attr('data-amp-accordion', $.ampAccordion.count),
				$toggle = typeof o.toggle === 'object' ? o.toggle : $e.find(o.toggle),
				$content = typeof o.content === 'object' ? o.content : $e.find(o.content),
				$flip = typeof o.flip === 'object' ? o.flip : $e.find(o.flip);

			$content.addClass('amp-accordion-content height-animate-hide').attr('data-amp-accordion-content', $.ampAccordion.count);
			$flip.addClass('amp-accordion-flip');

			$toggle.attr('data-amp-accordion-toggle', $.ampAccordion.count).click(function() {
				$('.amp-accordion[data-amp-accordion=' + $(this).attr('data-amp-accordion-toggle') + ']').ampAccordion('toggle');
			})

			$e.setOptions($.extend({}, o, {
				toggle:  $toggle,
				content: $content,
				flip:    $flip,
			}))

			$e.ampAccordion('toggle', o.show === null ? undefined : o.show);

			$.ampAccordion.count++
		})
	},

	toggle: function(show) {
		var o = this.getOptions();

		var show = typeof show === 'undefined' ? this.hasClass('is-hidden') : show;

		this.toggleClass('is-showing', show).toggleClass('is-hidden', !show);
		o.content.toggleClass('hide', !show);
		o.flip.toggleClass('flip', show);
	}
});

//ampFormEditor jQuery Plugin
$.ampExtend('ampFormEditor', {
	init: function(o) {
		return this.use_once('form-editor-enabled').each(function(i, e) {
			var $fe = $(e);
			var $form = $fe.is('form') ? $fe : $fe.find('form');

			$fe.find('.edit-form').click($.ampFormEditor.edit);
			$fe.find('.toggle-form').click($.ampFormEditor.toggle);
			$fe.find('.cancel-form').click($.ampFormEditor.read);

			$form.find('[data-disable]').focus($.ampFormEditor._disableField);
			$form.not('[data-noajax]').submit($.ampFormEditor.submitForm);
		});
	},

	edit: function() {
		var $form = $(this).closest('.form-editor').removeClass('read').addClass('edit');
		$form.find('[readonly]').attr('data-readonly', 1).removeAttr('readonly').trigger('editing');
		return false;
	},

	toggle: function() {
		var $form = $(this).closest('.form-editor');
		$form.ampFormEditor($form.hasClass('read') ? 'edit' : 'read');
		return false;
	},

	read: function() {
		var $form = $(this).closest('.form-editor').removeClass('edit').addClass('read');
		$form.find('[data-readonly]').attr('readonly', '');
		$form.find('[data-disable]').blur().trigger('reading');
		return false;
	},

	submitForm: function() {
		var $form = $(this);

		var cb = window[$form.attr('data-callback')];

		$form.find('[data-loading]').loading();

		$.post($form.attr('action'), $form.serialize(), function(response) {
			$form.find('[data-loading]').loading('stop');

			$form.show_msg('clear');

			if (response.error) {
				$form.show_msg(response);

				if (typeof cb === 'function') {
					cb.call($form, response);
				}
			} else {
				if ($form.attr('data-reload')) {
					window.location.reload();
				} else if (typeof cb === 'function') {
					cb.call($form, response);
				} else {
					$form.find('[name]').each(function(i, e) {
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
						$form.show_msg(response, {delay: 4000});
					}
				}

				$form.ampFormEditor('read');
			}
		}, 'json');

		return false;
	},

	_disableField: function() {
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

var amplo_ajax_cb = function() {
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

		callback = function(response) {
			if (typeof response === 'object') {
				$.show_msg(response);
			} else {
				$replace.replaceWith(response);
			}
		}
	} else {
		callback = function(response) {
			window[ajax_cb].call($this, response);
		}
	}

	if ($this.is('form')) {
		$this.submit_ajax_form({callback: callback});
	} else {
		$this.loading({text: $this.is('[data-loading]') || 'Loading...'})
		$.get($this.attr('href'), {}, callback)
			.always(function() {
				$this.loading('stop');
			});

	}

	return false;
};

$.fn.amplo_ajax = function() {
	return this.each(function(i, e) {
		var $e = $(e);

		if ($e.is('form')) {
			$e.submit(amplo_ajax_cb);
		} else {
			$e.click(amplo_ajax_cb);
		}
	});
}

$.fn.submit_ajax_form = function(params) {
	params = $.extend({}, {
		callback: null
	}, params);

	return this.each(function(i, e) {
		var $form = $(e);

		var $btns = $form.find('button, input[type=submit], [data-loading]').loading({default_text: 'Submitting...'});

		$.post($form.attr('action'), $form.serialize(), typeof params.callback === 'function' ? params.callback : function(response) {
			$form.show_msg(response);
		}, 'json').always(function() {
			$btns.loading('stop');
		});
	});
}

$.fn.liveForm = function(params) {
	params = $.extend({}, {
		callback: null
	}, params);

	return this.use_once('live-form-enabled').each(function(i, e) {
		var $form = $(e);
		$form.find('[name]').change(function() {
			$(this).closest('form').submit();
		});

		$form.submit(function() {
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

		$('script').each(function(i, e) {
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

	if ($ac.show_msg_delay) {
		if (($msgs = $('.messages .message')).length) {
			setTimeout(function() {
				$msgs.hide_msg()
			}, $ac.show_msg_delay);
		}
	}
}

content_loaded.fn = {};

content_loaded.fn['ajax_calls'] = register_ajax_calls;
content_loaded.fn['confirms'] = register_confirms;
content_loaded.fn['colorbox'] = register_colorbox;

$(window).on('beforeunload', function() {
	$.pageUnloading = true;
	$('body').addClass('is-loading');
})

//Chrome Autofill disable hack
if (navigator.userAgent.toLowerCase().indexOf("chrome") >= 0) {
	$(window).load(function() {
		$('input:-webkit-autofill[autocomplete="off"]').each(function() {
			var $this = $(this);
			if (!$this.attr('value')) {
				$this.val('');
				setTimeout(function() {
					$this.val('');
				}, 200);
			}
		});
	});
}

if (!window.console) console = {
	log: function(m) {
		$.ampAlert(m)
	}
};
