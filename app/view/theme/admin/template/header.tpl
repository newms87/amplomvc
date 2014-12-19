<!DOCTYPE html>
<html dir="<?= language_info('direction'); ?>" lang="<?= language_info('code'); ?>">
	<head>
		<title><?= page_info('title'); ?></title>
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

		<? foreach ($styles as $style) { ?>
			<link rel="<?= $style['rel']; ?>" type="text/css" href="<?= $style['href']; ?>" media="<?= $style['media']; ?>" />
		<? } ?>

		<? foreach ($scripts as $type => $script_types) {
			if ($type === 'local') { ?>
				<script type="text/javascript">
					<? foreach ($script_types as $script_local) { ?>
					<?= $script_local . "\n"; ?>
					<? } ?>
				</script>
			<? } else {
			foreach ($script_types as $script_src) { ?>
				<script type="text/javascript" src="<?= $script_src; ?>"></script>
			<? }
			}
		} ?>

	</head>

	<body class="<?= $body_class; ?>">
		<div id="container">
			<div id="header">
				<div class="div1">
					<div class="div2">
						<a href="<?= site_url('admin'); ?>">
							<img src="<?= image(option('admin_logo'), option('admin_logo_width'), option('admin_logo_height')); ?>" title="{{Administration}}"/>
						</a>
					</div>

					<? if (is_logged()) { ?>
						<div class="div3">
							<img src="<?= theme_url('image/lock.png'); ?>" alt="" id="header_secure_lock"/><?= _l("You are logged in as <span>%s</span>", $user['username']); ?>
						</div>

						<? if (option('site_email_support')) { ?>
							<div class="div3" style="clear:right">
								<a href="mailto:<?= option('site_email_support') . '?subject=' . urlencode("Admin Support"); ?>" target="_blank"></a>
							</div>
						<? } ?>
					<? } ?>
				</div>
				<? if (is_logged()) { ?>
					<div id="menu" class="links clearfix">
						<div class="left"><?= links('admin'); ?></div>
						<div class="right"><?= links('right'); ?></div>
					</div>
				<? } ?>
			</div>

			<div class="content">
				<? if (empty($disable_messages)) { ?>
					<?= render_message(); ?>
				<? } ?>

