<div class="widget-chart">
	<? $chart_id = uniqid('chart-'); ?>
	<canvas id="<?= $chart_id; ?>" class="chart-canvass"></canvas>

	<script type="text/javascript">
		if (typeof init_chart !== 'function') {
			function init_chart($chart, data, discrete, options, type) {
				if (typeof Chart == 'undefined') {
					if (!$('body').data('chart-loaded')) {
						var script = document.createElement('script');
						script.type = 'text/javascript';
						script.src = '<?= URL_RESOURCES . 'js/chartjs/chart.js'; ?>';
						document.body.appendChild(script);
						var script = document.createElement('script');
						script.type = 'text/javascript';
						script.src = '<?= URL_RESOURCES . 'js/chartjs/barext.js'; ?>';
						document.body.appendChild(script);
						$('body').data('chart-loaded', true);
					}

					return setTimeout(function () {
						init_chart($chart, data, discrete, options, type)
					}, 100);
				}

				Chart.defaults.global.responsive = true;

				$chart.renderChart(type, data, discrete, options);
			}
		}

		init_chart($('#<?= $chart_id; ?>'), <?= json_encode($chart_data); ?>, <?= json_encode($discrete); ?>, <?= json_encode($options); ?>, "<?= $type; ?>");

		$.fn.renderChart = function (type, data, discrete, options) {
			var is_discrete = {
				Pie: 1,
				Doughnut: 1
			}

			return this.each(function (i, e) {
				var the_chart = new Chart(e.getContext('2d'));

				if (the_chart[type + 'Ext']) {
					type += 'Ext';
				}

				the_chart.data = data;
				the_chart.discrete = discrete;
				the_chart.orig_options = options;

				$(e).data('chart', the_chart);

				if (!the_chart[type]) {
					return;
				}

				//Render Chart
				the_chart.chart = the_chart[type](is_discrete[type] ? discrete : data, options);
			});
		}
	</script>

</div>

