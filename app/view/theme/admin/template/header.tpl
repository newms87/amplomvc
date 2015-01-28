<!DOCTYPE html>
<html dir="<?= language_info('direction'); ?>" lang="<?= language_info('code'); ?>">

	<?= head(); ?>

	<body class="<?= $body_class; ?>">
		<div id="container">
			<div id="header">
				<div class="div1">
					<div class="div2">
						<a href="<?= site_url('admin'); ?>">
							<img src="<?= image(option('admin_logo'), option('admin_logo_width'), option('admin_logo_height')); ?>" title="{{Administration}}"/>
						</a>
					</div>

					<div class="choose-site">
						<?= option('name'); ?>
					</div>

					<? if (is_logged()) { ?>
						<div class="div3">
							<img src="<?= theme_url('image/lock.png'); ?>" alt="" id="header_secure_lock"/>
							<?= _l("You are logged in as <span>%s</span>", $user['username']); ?>
						</div>

						<? if (option('site_email_support')) { ?>
							<div class="div3" style="clear:right">
								<a href="mailto:<?= option('site_email_support') . '?subject=' . urlencode("Admin Support"); ?>" target="_blank"></a>
							</div>
						<? } ?>
					<? } ?>
				</div>
				<? if (is_logged()) { ?>
					<div id="menu" class="clearfix">
						<div class="admin-nav links"><?= links('admin'); ?></div>
						<div class="right-nav links"><?= links('right'); ?></div>
					</div>
				<? } ?>
			</div>

			<div class="content">
				<? if (empty($disable_messages)) { ?>
					<?= render_message(); ?>
				<? } ?>

