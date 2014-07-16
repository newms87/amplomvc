<div class="widget-chart">
	<? $chart_id = uniqid('chart-'); ?>
	<canvas id="<?= $chart_id; ?>" class="chart-canvass"></canvas>

	<script type="text/javascript">
		if (typeof init_chart !== 'function') {
			function init_chart() {
				if (typeof Chart == 'undefined') {
					var script = document.createElement('script');
					script.type = 'text/javascript';
					script.src = '<?= URL_RESOURCES . 'js/chartjs/chart.js'; ?>';
					document.body.appendChild(script);
					return setTimeout(init_chart, 100);
				}

				Chart.defaults.global.responsive = true;

				var $chart = document.getElementById('<?= $chart_id; ?>').getContext('2d');

				var the_chart = new Chart($chart).Line(<?= json_encode($data); ?>, <?= json_encode($options); ?>);
			}
		}

		init_chart();
	</script>

</div>

