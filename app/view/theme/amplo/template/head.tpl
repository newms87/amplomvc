<head>
	<!-- NO-AMPLO-DEFER -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

	<title><?= _strip_tags(page_info('title')); ?></title>

	<? foreach (page_meta() as $key => $value) { ?>
		<meta name="<?= $key; ?>" content="<?= $value; ?>"/>
	<? } ?>

	<base href="<?= IS_SSL ? HTTPS_SITE : HTTP_SITE; ?>"/>

	<? if ($canonical_link = page_info('canonical_link')) { ?>
		<link href="<?= $canonical_link; ?>" rel="canonical"/>
	<? } ?>

	<? if ($head_icon = option('site_icon')) { ?>
		<? foreach ($head_icon as $size => $icon) { ?>
			<? if ($size === 'ico') { ?>
				<link href="<?= image($icon); ?>" rel="apple-touch-icon icon shortcut"/>
			<? } elseif ($size !== 'orig') { ?>
				<link href="<?= image($icon); ?>" rel="apple-touch-icon" sizes="<?= $size; ?>"/>
			<? } ?>
		<? } ?>
	<? } ?>

	<?
	$defer   = option('defer_scripts', true);
	$scripts = page_info('scripts');
	$styles  = page_info('styles');

	if (!empty($scripts['local'])) { ?>
		<script type="text/javascript">
			<? foreach ($scripts['local'] as $l) { ?>
			<?= $l . "\n"; ?>
			<? } ?>
		</script>
		<? unset($scripts['local']);
	}

	foreach ($styles as $style) { ?>
		<link rel="<?= $style['rel']; ?>" type="text/css" href="<?= $style['href']; ?>" media="<?= $style['media']; ?>"/>
	<? }

	foreach ($scripts as $type => $script_types) {
		foreach ($script_types as $script_src) { ?>
			<script type="text/javascript" <?= $defer ? 'defer="defer"' : ''; ?> src="<?= $script_src; ?>"></script>
		<? }
	} ?>

	<!--[if IE 9]>
	<link rel="stylesheet" type="text/css" href="<?= theme_url('css/ie9.css'); ?>"/>
	<![endif]-->
	<!--[if IE 8]>
	<link rel="stylesheet" type="text/css" href="<?= theme_url('css/ie8.css'); ?>"/>
	<![endif]-->

	<!--[if lt IE 9]>
	<script src="<?= URL_RESOURCES . 'js/html5shiv.js'; ?>"></script>
	<![endif]-->

	<? if ($ga = option('ga_code')) { ?>
		<!-- Google Analytics Tracker -->
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

			ga('send', 'pageview', {'dimension1': "<?= DOMAIN; ?>"});

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
	<? } ?>

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
</head>
