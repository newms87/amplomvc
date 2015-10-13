<!DOCTYPE html>
<html dir="<?= language_info('direction'); ?>" lang="<?= language_info('code'); ?>">

<?= head(); ?>

<body class="<?= page_info('body_class'); ?>">
<div id="container">
	<div id="header" class="header-row row">
		<div class="row header-content">
			<div class="branding col xs-12 md-8 left middle">
				<div class="admin-logo col xs-12 left top">
					<a href="<?= site_url('admin'); ?>">
						<? if ($logo = option('admin_logo', DIR_THEMES . 'amplo/image/amplo-logo.png')) { ?>
							<? if ($logo_srcset = option('admin_logo_srcset')) { ?>
								<img <?= image_srcset(build_srcset($logo, $logo_srcset, option('admin_logo_width'), option('admin_logo_height', 80)), $logo_srcset, option('admin_name'), option('admin_name')); ?> />
							<? } else { ?>
								<img <?= img($logo, option('admin_logo_width'), option('admin_logo_height', 80), option('admin_name'), option('admin_name')); ?> />
							<? } ?>
						<? } else { ?>
							<?= option('site_name'); ?>
						<? } ?>
					</a>
				</div>
			</div>


			<? if (is_logged()) { ?>
				<div class="header-right col xs-12 md-4 xs-center md-right">
					<div class="header-secure">
						<img <?= img(theme_dir('image/lock.png')); ?> />
						{{Logged in as <a href="<?= site_url('admin/user/my-account'); ?>"><?= user_info('username'); ?></a>}}
					</div>

					<? if ($support = option('site_email_support')) { ?>
						<div class="header-support">
							<a href="mailto:<?= $support . '?subject=' . urlencode("Admin Support"); ?>" target="_blank">{{Contact Support}}</a>
						</div>
					<? } ?>
				</div>
			<? } ?>
		</div>

		<section id="message-box" class="message-row row left">
			<? if (empty($disable_messages) && $r->message->has()) { ?>
				<?= render_message(); ?>
			<? } ?>
		</section>
	</div>

	<? if (is_logged()) { ?>
		<div id="menu">
			<div class="nav nav-admin"><?= build_links('admin', array('class' => 'popup')); ?></div>
			<div class="nav nav-right"><?= build_links('right', array('class' => 'popup')); ?></div>
		</div>
	<? } ?>

	<div class="content">
