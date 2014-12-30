<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

	<title><?= _strip_tags(page_info('title')); ?></title>

	<? foreach (page_meta() as $key => $value) { ?>
		<meta name="<?= $key; ?>" content="<?= $value; ?>"/>
	<? } ?>

	<base href="<?= IS_SSL ? HTTPS_SITE : HTTP_SITE; ?>"/>

	<? if ($canonical_link = page_info('canonical_link')) { ?>
		<link href="<?= $canonical_link; ?>" rel="canonical"/>
	<? } ?>

	<? if ($head_icon = option('config_icon')) { ?>
		<? foreach ($head_icon as $size => $icon) { ?>
			<? if ($size === 'ico') { ?>
				<link href="<?= image($icon); ?>" rel="apple-touch-icon icon shortcut"/>
			<? } elseif ($size !== 'orig') { ?>
				<link href="<?= image($icon); ?>" rel="apple-touch-icon" sizes="<?= $size; ?>"/>
			<? } ?>
		<? } ?>
	<? } ?>

	<? foreach (page_info('styles') as $style) { ?>
		<link rel="<?= $style['rel']; ?>" type="text/css" href="<?= $style['href']; ?>" media="<?= $style['media']; ?>"/>
	<? } ?>

	<? foreach (page_info('scripts') as $type => $script_types) {
		if ($type === 'local') {
			?>
			<script type="text/javascript">
				<? foreach ($script_types as $script_local) { ?>
				<?= $script_local . "\n"; ?>
				<? } ?>
			</script>
		<?
		} else {
		foreach ($script_types as $script_src) { ?>
			<script type="text/javascript" src="<?= $script_src; ?>"></script>
		<? }
		}
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

	<? $google_analytics = option('config_google_analytics'); ?>
	<? if ($google_analytics) { ?>
		<!-- Google Analytics Tracker -->
		<script type="text/javascript">
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', '<?= $google_analytics; ?>']);
			_gaq.push(['_trackPageview']);

			(function () {
				var ga = document.createElement('script');
				ga.type = 'text/javascript';
				ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0];
				s.parentNode.insertBefore(ga, s);
			})();
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
