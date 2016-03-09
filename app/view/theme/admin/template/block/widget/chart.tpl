<div class="widget-chart">
	<? $chart_id = uniqid('chart-'); ?>
	<canvas id="<?= $chart_id; ?>" class="chart-canvass"></canvas>

	<script type="text/javascript">
		$('#<?= $chart_id; ?>').renderChart("<?= $type; ?>", <?= json_encode($chart_data); ?>, <?= json_encode($discrete); ?>, <?= json_encode($options); ?>);
	</script>

</div>

