<?= '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?= $direction; ?>" lang="<?= $lang; ?>" xml:lang="<?= $lang; ?>">
	<head>
		<title><?= $title; ?></title>
		<base href="<?= $base; ?>"/>
		<? if ($description) { ?>
			<meta name="description" content="<?= $description; ?>"/>
		<? } ?>
		<? if ($keywords) { ?>
			<meta name="keywords" content="<?= $keywords; ?>"/>
		<? } ?>
		<? if ($canonical_link) { ?>
			<link href="<?= $canonical_link; ?>" rel="canonical"/>
		<? } ?>

		<? if (option('config_icon')) { ?>
			<? foreach (option('config_icon') as $size => $icon) { ?>
				<? if ($size === 'ico') { ?>
					<link href="<?= image($icon); ?>" rel="apple-touch-icon icon shortcut"/>
				<? } elseif ($size !== 'orig') { ?>
					<link href="<?= image($icon); ?>" rel="apple-touch-icon" sizes="<?= $size; ?>"/>
				<? } ?>
			<? } ?>
		<? } ?>

		<?= $styles; ?>
		<?= $scripts; ?>

	</head>
	<body class="<?= $body_class; ?>">
		<div id="container">
			<div id="header">
				<div class="div1">
					<div class="div2">
						<a href="<?= site_url('admin'); ?>">
							<img src="<?= image(option('config_admin_logo')); ?>" title="<?= _l("Administration"); ?>"/>
						</a>
					</div>

					<? if (is_logged()) { ?>
						<div class="div3">
							<img src="<?= theme_url('image/lock.png'); ?>" alt="" id="header_secure_lock"/><?= _l("You are logged in as <span>%s</span>", $user['username']); ?>
						</div>

						<? if (option('config_email_support')) { ?>
							<div class="div3" style="clear:right">
								<a href="mailto:<?= option('config_email_support') . '?subject=' . urlencode("Admin Support"); ?>" target="_blank"></a>
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

