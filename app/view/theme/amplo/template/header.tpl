<!DOCTYPE html>
<html dir="<?= language_info('direction'); ?>" lang="<?= language_info('code'); ?>">

	<?= head(); ?>

	<body class="<?= $body_class; ?> <?= $show_admin_bar ? 'admin-bar' : ''; ?>">
		<section id="container">
			<header class="main-header row top-row">
				<? if ($show_admin_bar) { ?>
					<?= block('widget/admin_bar'); ?>
				<? } ?>

		<div class="wrap">
			<? if ($logo = option('config_logo')) { ?>
				<div id="logo" class="col xs-5 sm-4 md-2 left">
					<a href="<?= site_url(); ?>" class="block">
						<img src="<?= image($logo, option('config_logo_width'), option('config_logo_height')); ?>" title="<?= option('config_name'); ?>" alt="<?= option('config_name'); ?>"/>
					</a>
				</div>
			<? } ?>

					<? if ($slogan = option('config_slogan')) { ?>
						<div id="slogan"><?= $slogan; ?></div>
					<? } ?>

					<? if (option('config_multi_language')) { ?>
						<?= block('localisation/language'); ?>
					<? } ?>

					<? if (option('config_multi_currency')) { ?>
						<?= block('localisation/currency'); ?>
					<? } ?>

					<div id="links-account" class="links">
						<? if (!is_logged()) { ?>
							<div class="login-buttons">
								<a class="button scale login" href="<?= site_url('customer/login'); ?>">{{Log In}}</a>
								<a class="button scale register" href="<?= site_url('customer/registration'); ?>">{{Sign Up}}</a>
							</div>
							<div class="login-content">
								<div class="login-form"><?= call('customer/login', array('template' => 'customer/login_header'), true); ?></div>
							</div>
						<? } else { ?>
							<div class="login-buttons">
								<a class="button scale my-account" href="<?= site_url('account'); ?>"><?= _l("%s's Account", $customer['firstname']); ?></a>
								<a class="button scale logout" href="<?= site_url('customer/logout'); ?>">{{Log Out}}</a>
							</div>
						<? } ?>

						<? if (has_links('account')) { ?>
							<div class="login-links">
								<?= links('account'); ?>
							</div>
						<? } ?>
					</div>

					<? if (option('config_social_media')) { ?>
						<div id="header-social-networks">
							<?= block('extras/social_media'); ?>
						</div>
					<? } ?>

					<? if (has_links('secondary')) { ?>
						<div id="links-secondary" class="links">
							<?= links('secondary'); ?>
						</div>
					<? } ?>

					<? if (has_links('primary')) { ?>
						<nav id="links-primary" class="links">
							<?= links('primary'); ?>
						</nav>
					<? } ?>
				</div>
			</header>

			<main class="main clearfix">

				<? if (empty($disable_messages) && $this->message->has()) { ?>
					<section class="message-row row">
						<div class="wrap">
							<?= render_message(); ?>
						</div>
					</section>
				<? } ?>

				<? if (show_area('above')) { ?>
					<section class="area-above row">
						<div class="wrap">
							<?= area('above'); ?>
						</div>
					</section>
				<? } ?>
