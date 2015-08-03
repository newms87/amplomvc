$.pageBreaks = $.fn.pageBreaks = function (opts) {
	opts = $.extend({}, {
		width:  null,
		height: null,
		header: true,
		footer: true,
		margin: null,
		resize: false
	}, opts);

	return this.each(function (i, e) {
		var $e = $(e);
		var $pages = $e.find('.page');
		var $first = $pages.first();

		if (!opts.width) {
			opts.width = $first.width();
		}

		if (!opts.height) {
			opts.height = $first.height();
		}

		if (opts.resize) {
			$pages.width(opts.width);
			$pages.height(opts.height);
		}

		if (opts.header) {
			opts.$header = $first.find('.page-header');
		}

		if (opts.footer) {
			opts.$footer = $first.find('.page-footer');
		}

		if (!opts.margin) {
			opts.margin = {
				top:    parseInt($first.css('padding-top')),
				bottom: parseInt($first.css('padding-bottom')),
				left:   parseInt($first.css('padding-left')),
				right:  parseInt($first.css('padding-right'))
			}
		}

		$pages.each(function (p, page) {
			var $p = $(page), max_y = opts.height - opts.margin.bottom;

			var $blocks = $p.find('.page-body').length ? $p.find('.page-body').children() : $p.children();

			$blocks.each(function (b, block) {
				var $b = $(block);
				var bottom = $b.position().top + $b.outerHeight();

				if (bottom > max_y) {
					$.pageBreaks.break($p, $b, opts);
				}
			});
		});

		$.pageBreaks.updateVars.call($e.find('.page'));
	});
};

$.pageBreaks.updateVars = function () {
	var page_count = this.length;

	return this.each(function (i, e) {
		var $e = $(e);
		$e.find('.var-page').html(i + 1);
		$e.find('.var-page-count').html(page_count);
	});
}

$.pageBreaks.break = function ($p, $e, opts) {
	var $page = $("<div />").addClass('page');

	$p.after($page)

	if (opts.$header.length) {
		$page.append(opts.$header.clone());
	}

	while (typeof $e.attr('data-no-break') !== 'undefined') {
		$e = $e.prev();
	}

	$page.append($e.nextAll().add($e))

	if (opts.$footer.length) {
		$page.append(opts.$footer.clone());
	}

	return $page;
}
