<div class="widget-chart">
	<? $chart_id = uniqid('chart-'); ?>
	<canvas id="<?= $chart_id; ?>" class="chart-canvass"></canvas>

	<script type="text/javascript">
		if (typeof init_chart !== 'function') {
			function init_chart($canvas, data, discrete, options, type) {
				if (typeof Chart === 'undefined' || !Chart.chart_ext_loaded) {
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
						init_chart($canvas, data, discrete, options, type)
					}, 100);
				}

				Chart.defaults.global.responsive = true;

				$canvas.renderChart(type, data, discrete, options);
			}
		}

		init_chart($('#<?= $chart_id; ?>'), <?= json_encode($chart_data); ?>, <?= json_encode($discrete); ?>, <?= json_encode($options); ?>, "<?= $type; ?>");
	</script>

</div>

