var chart_ext_init = (function () {
	var root = this;

	if (typeof root.Chart === 'undefined') {
		return setTimeout(root.chart_ext_init, 300);
	}

	var Chart = root.Chart,
		helpers = Chart.helpers;

	var chart_ext = {
		name:          "BarExt",
		initialize:    function (data) {
			Chart.types[this.baseType()].prototype.initialize.apply(this, arguments);

			var canvas = this.chart.ctx.canvas;
			var me = this;

			var el = function (evt) {
				if (me.inBox(event)) {
					me.options.showValues = !me.options.showValues;
					me.draw();
				}
			}

			canvas.addEventListener('mouseup', el, false);
			this.events.mouseup = el;
		},
		draw:          function (data) {
			Chart.types[this.baseType()].prototype.draw.apply(this, arguments);

			var chart = this.chart,
				ctx = chart.ctx;

			this.drawBox(ctx, chart.width - 30, 10, 20, 20);

			if (this.options.showValues) {
				this.showValues();
			}
		},
		baseType:      function () {
			return this.__proto__.name.replace("Ext", '');
		},
		drawBox:       function (ctx, x, y, width, height) {
			ctx.beginPath();
			ctx.moveTo(x, y);
			ctx.lineTo(x + width, y);
			ctx.lineTo(x + width, y + height);
			ctx.lineTo(x, y + height);
			ctx.lineTo(x, y);
			ctx.closePath();
			ctx.fillStyle = '#8DAD62';
			ctx.fill();
			ctx.strokeStyle = '#8DAD62';
			ctx.stroke();
			var img = new Image();
			img.src = $ac.site_url + 'app/view/theme/admin/image/cmd.png';

			ctx.drawImage(img, x + 2, y + 2, 16, 16);
		},
		inBox:         function (event) {
			var pos = this.getMousePos(event)
			return pos.x > this.chart.width - 30 && pos.x < this.chart.width - 10 && pos.y > 10 && pos.y < 30;
		},
		getMousePos:   function (event) {
			var rect = this.chart.ctx.canvas.getBoundingClientRect();
			return {
				x: event.clientX - rect.left,
				y: event.clientY - rect.top
			};
		},
		showValues:    function (chart) {
			var type = this.baseType();

			var labels = [],
				colors = [];

			chart = chart || this;

			if (type === 'Pie' || type === 'Doughnut') {

				for (var d in chart.chart.discrete) {
					var data = chart.chart.discrete[d];

					labels.push(data.label + ": " + data.value);

					colors.push({
						fill:   data.color,
						stroke: data.highlight
					});
				}

				this.displayLegend.call(this, 'Legend', labels, colors, 5, 110);

			} else {

				for (var d in chart.datasets) {
					bars = chart.datasets[d].bars || chart.datasets[d].points;

					for (var b in bars) {
						bar = bars[b];

						this.displayPopup.call(chart, bar.value, bar.x, bar.y);
					}
				}

				if (chart.datasets.length > 1) {
					for (var d in chart.datasets) {
						var data = chart.datasets[d];

						labels.push(data.label);
						colors.push({fill: data.fillColor, stroke: data.strokeColor});
					}

					this.displayLegend.call(this, 'Legend', labels, colors, chart.chart.width - 50, 10);
				}
			}
		},
		displayPopup:  function (text, x, y) {
			var chart = this;

			new Chart.Tooltip({
				x:            x,
				y:            y,
				xPadding:     chart.options.tooltipXPadding,
				yPadding:     chart.options.tooltipYPadding,
				fillColor:    chart.options.tooltipFillColor,
				textColor:    chart.options.tooltipFontColor,
				fontFamily:   chart.options.tooltipFontFamily,
				fontStyle:    chart.options.tooltipFontStyle,
				fontSize:     chart.options.tooltipFontSize,
				caretHeight:  chart.options.tooltipCaretSize,
				cornerRadius: chart.options.tooltipCornerRadius,
				text:         text,
				chart:        chart.chart
			}).draw();
		},
		displayLegend: function (title, labels, colors, x, y) {
			var chart = this;
			new Chart.MultiTooltip({
				x:                     x,
				y:                     y,
				xPadding:              chart.options.tooltipXPadding,
				yPadding:              chart.options.tooltipYPadding,
				xOffset:               chart.options.tooltipXOffset,
				fillColor:             chart.options.tooltipFillColor,
				textColor:             chart.options.tooltipFontColor,
				fontFamily:            chart.options.tooltipFontFamily,
				fontStyle:             chart.options.tooltipFontStyle,
				fontSize:              chart.options.tooltipFontSize,
				titleTextColor:        chart.options.tooltipTitleFontColor,
				titleFontFamily:       chart.options.tooltipTitleFontFamily,
				titleFontStyle:        chart.options.tooltipTitleFontStyle,
				titleFontSize:         chart.options.tooltipTitleFontSize,
				cornerRadius:          chart.options.tooltipCornerRadius,
				labels:                labels,
				legendColors:          colors,
				legendColorBackground: chart.options.multiTooltipKeyBackground,
				title:                 title,
				chart:                 chart.chart,
				ctx:                   chart.chart.ctx
			}).draw();
		},
		destroy:       function () {
			for (var e in this.events) {
				this.chart.canvas.removeEventListener(e, this.events[e]);
			}
		}
	};

	Chart.defaults.global.tooltipTemplate = "<%= value %>";
	Chart.defaults.global.showValues = true;

	for (var t in Chart.types) {
		var ext = $.extend(true, {}, chart_ext);
		ext.name = t + 'Ext';
		Chart.types[t].extend(ext);
	}

	Chart.chart_ext_loaded = true;
});

chart_ext_init.call(this);

$.fn.renderChart = function (type, data, discrete, options) {
	var is_discrete = {
		Pie:      1,
		Doughnut: 1
	}

	return this.each(function (i, e) {
		var $canvas = $(e);
		var orig_chart = $canvas.data('chart') || {};

		if (orig_chart.chart) {
			orig_chart.chart.destroy();
		}

		var chart = new Chart(e.getContext('2d'));

		chart.data = orig_chart.data || data;
		chart.discrete = orig_chart.discrete || discrete;
		chart.orig_options = orig_chart.orig_options || options;

		//Render Chart
		if (chart[type]) {
			chart.chart = chart[chart[type + 'Ext'] ? type + 'Ext' : type](is_discrete[type] ? chart.discrete : chart.data, chart.orig_options);
		}

		$canvas.data('chart', chart);
	});
}

$.fn.showValues = function (show) {
	return this.each(function (i, e) {
		var chart = $(e).data('chart');

		if (!chart) return;

		chart = chart.chart;

		if (show !== chart.chart.showing_values) {
			chart.chart.showing_values = !chart.chart.showing_values;
			chart.draw()
		}
	});
}
