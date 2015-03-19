$.fn.amplo_slider = function (opts, callback) {
	$.extend(opts, {
		boundEdge: true
	});

	this.each(function(i,slider) {
		var $slider = $(slider);
		var $slides = $slider.children(),
			$viewport = $slider.closest('.viewport'),
			$as = $slider.closest('.amplo-slider');


		if (!$viewport.length) {
			$viewport = $('<div />').addClass('viewport').insertBefore($slider).append($slider);
		}

		if (!$as.length) {
			$as = $('<div />').addClass('amplo-slider').insertBefore($viewport).append($viewport);
		}

		$slider.d = {
			view_width: $viewport.width(),
			x:          0,
			y:          0,
			width:      0,
			current:    0,
			slides:     []
		}

		$slides.each(function (i, e) {
			$slider.d.slides[i] = {
				x: -$(e).position().left,
				width: $(e).outerWidth()
			};
		})

		last = $slider.d.slides[$slider.d.slides.length-1];
		$slider.width(-last.x + last.width);

		$slider.d.edge = $slider.width() - $slider.d.view_width;

		for (var i in $slider.d.slides) {
			var s = $slider.d.slides[i];

			if (-s.x + s.width > $slider.d.edge) {
				$slider.d.edge_index = i;
				break;
			}
		}

		$slider.nextSlide = function () {
			if ($slider.d.is_edge) {
				$slider.slideTo(0);
			} else {
				$slider.slideTo($slider.d.current >= $slider.d.slides.length ? 0 : $slider.d.current + 1);
			}
		}

		$slider.prevSlide = function () {
			if ($slider.d.is_edge) {
				$slider.slideTo($slider.d.edge_index - 1);
			} else {
				$slider.slideTo($slider.d.current > 0 ? $slider.d.current - 1 : $slider.d.slides.length - 1);
			}
		}

		$slider.slideTo = function (i) {
			if (i >= $slider.d.slides.length) {
				i = 0;
			}

			var x = $slider.d.slides[i].x;

			if (($slider.d.is_edge = opts.boundEdge && -x > $slider.d.edge)) {
				x = -$slider.d.edge;
			}

			$slider.d.x = x;
			$slider.d.current = i;
			$slider.css({left: x});
		}

		if (typeof callback === 'function') {
			callback.call($slider, $as);
		}
	});

	return this;
}
