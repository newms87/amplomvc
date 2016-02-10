$.ampExtend($.ampSlider = function() {}, {
	init: function(o) {
		o = $.extend({}, {
			boundEdge: true,
			onReady:   null,
			onSlide:   null
		}, o);

		return this.each(function() {
			var $slider = $(this).addClass('amp-slider'), $children;

			if (!(o.slideList = $slider.find('.amp-slide-list')).length) {
				o.slideList = $('<div />').addClass('amp-slide-list')

				$children = $slider.children()
			} else {
				$children = o.slideList.children();
			}

			if (!$children.length) {
				$.error("There are no slides in the slider parent element.");
				return;
			}

			$children.each(function() {
				$(this).width($(this).width());
			})

			o.slideList.append($children);

			if (!(o.viewport = $slider.find('.amp-viewport')).length) {
				o.viewport = $('<div />').addClass('amp-viewport');
			}

			//if elements not already in context, append to correct parent
			o.slideList.closest(o.viewport).length || o.viewport.append(o.slideList);
			o.viewport.closest($slider).length || $slider.append(o.viewport);

			$slider.setOptions(o);

			$(window).resize(function() {
				$slider.ampSlider('reset')
			});

			$slider.ampSlider('reset')

			if (o.onReady) {
				o.onReady.call($slider);
			}
		});
	},

	reset: function() {
		var o = this.getOptions();

		$.extend(o, {
			x:       0,
			y:       0,
			width:   0,
			current: 0,
			slides:  []
		})

		o.slideList.children().each(function(i, e) {
			o.slides[i] = {
				x:     -$(e).position().left,
				width: $(e).outerWidth()
			};
		})

		last = o.slides[o.slides.length - 1];
		o.slideList.width(-last.x + last.width);

		o.edge = o.slideList.width() - o.viewport.width();

		this.find('.amp-control').toggleClass('hidden', o.edge <= 0);

		for (var i in o.slides) {
			var s = o.slides[i];

			if (-s.x + s.width > o.edge) {
				o.edge_index = i;
				break;
			}
		}

		return this;
	},

	nextSlide: function(i) {
		var o = this.getOptions();

		if (o.is_edge) {
			this.ampSlider('slideTo', 0)
		} else {
			this.ampSlider('slideTo', o.current >= o.slides.length ? 0 : o.current + (i || 1));
		}

		return this;
	},

	prevSlide: function(i) {
		var o = this.getOptions();

		if (o.is_edge) {
			this.ampSlider('slideTo', o.edge_index - 1);
		} else {
			this.ampSlider('slideTo', o.current > 0 ? o.current - (i || 1) : o.slides.length - 1);
		}

		return this;
	},

	slideTo: function(i) {
		var o = this.getOptions();

		if (i >= o.slides.length || i < 0) {
			i = 0;
		}

		var x = o.slides[i].x;

		if ((o.is_edge = o.boundEdge && -x > o.edge)) {
			x = -o.edge;
		}

		o.x = x;
		o.previous = o.current;
		o.current = i;
		o.slideList.css({left: x});

		if (o.onSlide) {
			o.onSlide.call(this, o.slideList.children().get(o.current), o);
		}

		return this;
	}
})
