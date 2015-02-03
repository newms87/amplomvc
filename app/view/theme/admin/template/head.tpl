<head>
	<!-- NO-AMPLO-DEFER -->
	<title><?= _strip_tags(page_info('title')); ?></title>
	<base href="<?= site_url('admin'); ?>"/>

	<? if ($admin_icon = option('admin_icon')) { ?>
		<? foreach ($admin_icon as $size => $icon) { ?>
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

</head>
