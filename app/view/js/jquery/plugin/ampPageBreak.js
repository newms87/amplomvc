//ampPageBreak jQuery Plugin
$.ampExtend($.ampPageBreak = function() {}, {
	init: function(o) {
		o = $.extend({}, {
			width:         null,
			height:        null,
			contentHeight: null,
			header:        true,
			footer:        true,
			margin:        null,
			resize:        false,
			debugLog:      false
		}, o);

		return this.not('.page-broken').addClass('page-broken').each(function(i, e) {
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

			if (!o.contentHeight) {
				o.contentHeight = o.height - o.margin.bottom - o.margin.top;
			}

			$e.find('.amp-dynamic-height').each(function(){
				var $t = $(this);
				$t.height($t.height());
			})

			$pages.each(function(p, page) {
				var $p = $(page), top = 0, bottom = 0;

				var $blocks = $p.find('.page-body').length ? $p.find('.page-body').children() : $p.children();

				!o.debugLog || o.debugLog.append('<BR><BR><b>Page ' + p + '</b>: contentHeight: ' + o.contentHeight + 'px, margin-bottom: ' + o.margin.bottom + 'px, blocks: ' + $blocks.length + '<BR>');

				var $parent = $blocks.parent();

				if ($parent.css('position') === 'static') {
					$parent.css('position', 'relative');
				}

				$blocks.each(function(b, block) {
					var $b = $(block);

					margin = {
						top:    parseInt($b.css('marginTop')) || 0,
						bottom: parseInt($b.css('marginBottom')) || 0,
					}

					top += margin.top;

					bottom = top + $b.outerHeight();

					!o.debugLog || o.debugLog.append('ROW .' + $b.attr('class').replace(/\s/g, '.') + ' (margin: top ' + margin.top + ', bottom ' + margin.bottom + ')  === (top ' + top + 'px + height ' + $b.outerHeight() + 'px) = ' + bottom + 'px' + (bottom > o.contentHeight ? ' - BREAK ROW' : '') + '<BR>');

					if (bottom > o.contentHeight) {
						$.ampPageBreak.break($p, $b, o);
						top = 0;
					} else {
						top = bottom + margin.bottom;
					}
				});
			});

			$.ampPageBreak.updateVars.call($e);
		});
	},

	updateVars: function() {
		var $pages = this.find('.page');
		var page_count = $pages.length;

		return $pages.each(function(i, e) {
			var $e = $(e);
			$e.find('.var-page').html(i + 1);
			$e.find('.var-page-count').html(page_count);
		});
	},

	break: function($p, $e, opts) {
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
