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
	<script src="<?= URL_JS . 'html5shiv.js'; ?>"></script>
	<![endif]-->
</head>
