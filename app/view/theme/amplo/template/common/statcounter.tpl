<? $statcounter = option('config_statcounter'); ?>
<? if (!empty($statcounter['project'])) { ?>
	<!-- Stat Counter Tracker -->
	<script type="text/javascript">
		var sc_project = "<?= $statcounter['project']; ?>";
		var sc_invisible = 1;
		var sc_security = "<?= $statcounter['security']; ?>";

		$(document).ready(function () {
			$.getScript('http://www.statcounter.com/counter/counter.js');
		});
	</script>
	<noscript>
		<div class="statcounter">
			<a title="tumblr tracker" href="http://statcounter.com/tumblr/" target="_blank">
				<img class="statcounter" src="http://c.statcounter.com/<?= $statcounter['project']; ?>/0/<?= $statcounter['security']; ?>/1/" alt="tumblr tracker"/>
			</a>
		</div>
	</noscript>
<? } ?>
