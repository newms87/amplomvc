<div class="widget-chart">
	<? $chart_id = uniqid('chart-'); ?>
	<canvas id="<?= $chart_id; ?>" class="chart-canvass"></canvas>

	<script type="text/javascript">
		if (typeof init_chart !== 'function') {
			function init_chart($chart, data, options, type) {
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

					return setTimeout(function() {init_chart($chart, data, options, type)}, 100);
				}

				Chart.defaults.global.responsive = true;

				var the_chart = new Chart($chart[0].getContext('2d'));

				if (the_chart[type+'Ext']) {
					type += 'Ext';
				}

				the_chart.chart = the_chart[type] ? the_chart[type](data, options) : null;
				the_chart.data = data;
				the_chart.orig_options = options;

				$chart.data('chart', the_chart);
			}
		}

		init_chart($('#<?= $chart_id; ?>'), <?= json_encode($chart_data); ?>, <?= json_encode($options); ?>, "<?= $type; ?>");
	</script>

</div>

