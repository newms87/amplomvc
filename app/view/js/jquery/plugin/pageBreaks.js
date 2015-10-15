$.pageBreaks = $.fn.pageBreaks = function (o) {
	var p = $.pageBreaks, o = arguments[0];

	if (p[o]) {
		return p[o].apply(this, Array.prototype.slice.call(arguments, 1));
	} else if (typeof o === 'object' || !o) {
		return p.init.apply(this, arguments);
	} else {
		$.error('Method ' + o + ' does not exist for jQuery plugin ' + p.name);
	}
}

$.extend($.pageBreaks, {
	init: function (o) {
		o = $.extend({}, {
			width:    null,
			height:   null,
			header:   true,
			footer:   true,
			margin:   null,
			resize:   false,
			debugLog: false
		}, o);

		return this.not('.page-broken').addClass('page-broken').each(function (i, e) {
			var $e = $(e);
			var $pages = $e.find('.page');
			var $first = $pages.first();

			if (o.debugLog) {
				o.debugLog = $("<div>").addClass('debug-log').appendTo($e)
				$e.addClass('pb-debug');
			}

			if (!o.width) {
				o.width = $first.width();
			}

			if (!o.height) {
				o.height = $first.height();
			}

			if (o.resize) {
				$pages.width(o.width);
				$pages.height(o.height);
			}

			if (o.header) {
				o.$header = $first.find('.page-header');
			}

			if (o.footer) {
				o.$footer = $first.find('.page-footer');
			}

			if (!o.margin) {
				o.margin = {
					top:    parseInt($first.css('padding-top')),
					bottom: parseInt($first.css('padding-bottom')),
					left:   parseInt($first.css('padding-left')),
					right:  parseInt($first.css('padding-right'))
				}
			}

			$pages.each(function (p, page) {
				var $p = $(page), max_y = o.height - o.margin.bottom;

				var $blocks = $p.find('.page-body').length ? $p.find('.page-body').children() : $p.children();

				!o.debugLog || o.debugLog.append('<BR><BR>BREAK ' + p + ': ' + o.height + ' - ' + o.margin.bottom + ' == ' + max_y + ' --- ' + $blocks.length + ' rows<BR>');

				$blocks.each(function (b, block) {
					var $b = $(block);
					var bottom = $b.position().top + $b.outerHeight();

					!o.debugLog || o.debugLog.append('ROW ' + $b.attr('class') + ' :: ' + $b.position().top + ' + ' + $b.outerHeight() + ' === ' + bottom + ' / ' + max_y + (bottom > max_y ? ' - break' : '') + '<BR>');

					if (bottom > max_y) {
						$.pageBreaks.break($p, $b, o);
					}
				});
			});

			$.pageBreaks.updateVars.call($e);
		});
	},

	updateVars: function () {
		var $pages = this.find('.page');
		var page_count = $pages.length;

		return $pages.each(function (i, e) {
			var $e = $(e);
			$e.find('.var-page').html(i + 1);
			$e.find('.var-page-count').html(page_count);
		});
	},

	break: function ($p, $e, opts) {
		var $page = $('<div />').addClass('page');
		var $body = $('<div />').addClass('page-body');

		if (opts.$header.length) {
			$page.append(opts.$header.clone());
		}

		$page.append($body);

		while (typeof $e.attr('data-no-break') !== 'undefined') {
			$e = $e.prev();
		}

		$body.append($e.nextAll().add($e))

		if (opts.$footer.length) {
			$page.append(opts.$footer.clone());
		}

		$p.parent().append($page)

		return $page;
	},
});
