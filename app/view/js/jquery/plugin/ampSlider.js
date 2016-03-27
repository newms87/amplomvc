$.ampExtend($.ampSlider = function() {}, {
	init: function(o) {
		o = $.extend({}, {
			boundEdge:          true,
			onReady:            null,
			onSlide:            null,
			autoPlay:           false,
			transitionDelay:    5000,
			isPlaying:          false,
			stopOnControlClick: true,
			pauseOnHover:       false,
			slidesPerPage:      null,
			slideWidth:         null
		}, o);

		return this.each(function() {
			var $slider = $(this).addClass('amp-slider'), $children;

			if (!(o.slideList = $slider.find('.amp-slide-list')).length) {
				o.slideList = $('<div />').addClass('amp-slide-list')

				$children = $slider.children()

				o.childrenInContext = true;
			} else {
				$children = o.slideList.children();
			}

			if (!$children.length) {
				$.error("There are no slides in the slider parent element.");
				return;
			}

			if (!(o.viewport = $slider.find('.amp-viewport')).length) {
				o.viewport = $('<div />').addClass('amp-viewport');
			}

			//Setup default width
			if (!o.slideWidth && !o.slidesPerPage) {
				$children.each(function() {
					$(this).width($(this).width());
				})
			}

			if (o.childrenInContext) {
				o.slideList.append($children);
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

			if (o.autoPlay) {
				$slider.ampSlider('play');
			}
		});
	},

	play: function() {
		var $slider = this;
		var o = $slider.getOptions();

		o.isPlaying = true;
		$slider.ampSlider('playing');
	},

	playing: function() {
		var $slider = this;
		var o = $slider.getOptions();

		if (!o.isScheduled) {
			o.isScheduled = true;
			setTimeout(function() {
				o.isScheduled = false;

				if (o.isPlaying) {
					$slider.ampSlider('nextSlide').ampSlider('playing');
				}
			}, o.transitionDelay)
		}
	},

	stop: function() {
		var o = this.getOptions();
		o.isPlaying = false;
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

		if (o.slidesPerPage) {
			o.slideWidth = o.viewport.width() / o.slidesPerPage;
		}

		o.slideList.children().each(function(i, e) {
			var $e = $(e);

			$e.width(o.slideWidth || $e.width());

			o.slides[i] = {
				x:     -$e.position().left,
				width: $e.outerWidth()
			};
		})

		last = o.slides[o.slides.length - 1];
		o.slideList.width(-last.x + last.width);

		o.edge = o.slideList.width() - o.viewport.width();

		this.find('.amp-control').use_once().toggleClass('hidden', o.edge <= 0).click(function() {
			var $t = $(this);
			var $slider = $t.closest('.amp-slider');
			var o = $slider.getOptions();

			if ($t.is('.amp-control-prev')) {
				$slider.ampSlider('prevSlide');
			} else if ($t.is('.amp-control-next')) {
				$slider.ampSlider('nextSlide');
			} else if ($t.is('.amp-control-slide')) {
				$slider.ampSlider('slideTo', +$t.attr('data-slide-index'));
			}

			if (o.stopOnControlClick) {
				$slider.ampSlider('stop');
			}
		});

		if (o.pauseOnHover) {
			this.use_once('on-slider-hover').hover(function() {$(this).ampSlider('stop')}, function() {$(this).ampSlider('play')})
		}

		for (var i in o.slides) {
			var s = o.slides[i];

			if (-s.x + s.width > o.edge) {
				o.edge_index = i;
				break;
			}
		}

		this.ampSlider('slideTo', o.current);

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
