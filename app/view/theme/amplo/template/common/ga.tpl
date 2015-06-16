<!-- Google Analytics Tracker -->
<? $ga = option('ga_code'); ?>

<script type="text/javascript">
	(function (i, s, o, g, r, a, m) {
		i['GoogleAnalyticsObject'] = r;
		i[r] = i[r] || function () {
			(i[r].q = i[r].q || []).push(arguments)
		}, i[r].l = 1 * new Date();
		a = s.createElement(o),
			m = s.getElementsByTagName(o)[0];
		a.async = 1;
		a.src = g;
		m.parentNode.insertBefore(a, m)
	})(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

	<? if ($ga_domains = option('ga_domains')) { ?>
	ga('create', '<?= $ga; ?>', 'auto', {'allowLinker': true});
	ga('require', 'linker');
	ga('linker:autoLink', ['<?= implode("','", $ga_domains); ?>']);
	<? } else { ?>
	ga('create', '<?= $ga; ?>', 'auto');
	<? } ?>

	<? if ($ga_experiment_id = option('ga_experiment_id')) { ?>
	ga('set', 'expId', '<?= $ga_experiment_id; ?>');
	ga('set', 'expVar', '<?= $this->google->getExperimentVariation(); ?>');
	<? } ?>

	ga('send', 'pageview', "<?= $_SERVER['REQUEST_URI'] . (strpos($_SERVER['REQUEST_URI'], '?') ? '&' : '?') . 'domain=' . urlencode(DOMAIN); ?>");

	<? if (option('ga_click_tracking')) { ?>
	//Global click tracking
	$(document).click(function (event) {
		var $t = $(event.target);

		if (!$t.is('a') && $t.closest('a').length) {
			$t = $t.closest('a');
		}

		var category = $t.attr('href') || $t.attr('src') || $t.attr('id') || $t[0].nodeName;

		var id = $t.attr('id') || $t.closest('[id]').attr('id') || '',
			cls = $t.attr('class') || $t.closest('[class]').attr('class') || '';

		var label = (id ? '#' + id : '') + (cls ? '.' + cls : '');

		ga('send', 'event', category, 'click', label);
	});
	<? } ?>
</script>
