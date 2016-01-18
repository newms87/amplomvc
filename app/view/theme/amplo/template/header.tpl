<!DOCTYPE html>
<html dir="<?= language_info('direction'); ?>" lang="<?= language_info('code'); ?>">
<head>
	<?= head(); ?>
</head>

<body class="<?= page_info('body_class'); ?> <?= $show_admin_bar ? 'admin-bar' : ''; ?>">
<? option('ga_code') ? include_once(theme_dir('template/common/ga.tpl')) : ''; ?>
<? option('track_statcounter') ? include_once(theme_dir('template/common/statcounter.tpl')) : ''; ?>

<section id="container">
	<header class="site-header row top-row">
		<? if (!empty($terms_page) && is_logged()) { ?>
			<div class="terms-agreement">
				{{Our Terms &amp; Conditions have been updated! To review and accept
				<a href="<?= site_url('page', 'page_id=' . $terms_page['page_id']); ?>">go here</a>.}}
			</div>
		<? } ?>

		<? if (option("show-header-banner", false)) { ?>
			<div class="banner-bar-row row">
				<div class="wrap">
					<div class="banner-bar">
						<div class="col xs-12 md-10 lg-10 xl-8">
							<div class="help col xs-12 slide show">
								<span class="left-text">{{Welcome to Amplo MVC}}</span>
							</div>
						</div>
					</div>
				</div>

				<script>
					if (screen_width > 1024) {
						$(window).scroll(function() {
							$('.banner-bar-row').toggleClass('hide', $(window).scrollTop() > 20);
						}).scroll();
					}
				</script>
			</div>
		<? } ?>

		<? if (!empty($show_admin_bar)) { ?>
			<?= block('widget/admin_bar'); ?>
		<? } ?>

		<section id="message-box" class="message-row row">
			<? if (empty($disable_messages) && $r->message->has()) { ?>
				<?= render_message(); ?>
			<? } ?>
		</section>


		<div class="wrap">
			<? $logo_html = option('site_name');

			if ($logo = option('site_logo')) {
				ob_start(); ?>
				<? if ($logo_srcset = option('site_logo_srcset')) { ?>
					<img <?= image_srcset(build_srcset($logo, $logo_srcset, option('site_logo_width'), option('site_logo_height')), $logo_srcset, option('site_name'), option('site_name')); ?> />
				<? } else { ?>
					<?= img($logo, array(
						'width'  => option('admin_logo_width'),
						'height' => option('admin_logo_height', 80),
						'#title' => option('admin_name'),
						'#alt'   => option('admin_name'),
					)); ?>
				<? } ?>
				<? $logo_html = ob_get_clean();
			} ?>

			<div class="site-logo col lg-visible lg-2 left">
				<a href="<?= site_url(); ?>">
					<?= $logo_html; ?>
				</a>
			</div>

			<div class="nav nav-primary col xs-3 lg-6 left">
				<? if (has_links('primary')) { ?>
					<div class="mobile-menu-toggle lg-hidden" onclick="$(this).toggleClass('active')">
						<b class="fa fa-reorder mm-show"></b>
						<b class="fa fa-remove mm-hide"></b>
					</div>

					<div class="mobile-menu horizontal no-parent-scroll accordian">
						<?= build_links('primary', array('class' => 'horizontal')); ?>
					</div>
				<? } ?>
			</div>

			<div class="site-logo col lg-hidden xs-6">
				<a href="<?= site_url(); ?>">
					<?= $logo_html; ?>
				</a>
			</div>

			<div class="nav nav-account col xs-3 lg-4 right">
				<? if (is_logged()) { ?>
					<div class="link-menu account-home arrow-top align-right popup">
						<div class="parent on-click" data-amp-toggle=".nav-account .account-home">
							<span class="text lg-visible">{{<?= customer_info('first_name') ? "Hi, " . customer_info('first_name') : "Welcome Back"; ?>!}}</span>
							<i class="fa fa-home"></i>
						</div>

						<div class="children">
							<? $account_links = array(
								'my-details' => array(
									'display_name' => '{{My Details}}',
									'path'         => 'account/details',
								),
								'logout'     => array(
									'display_name' => '{{Log Out}}',
									'path'         => 'customer/logout',
									'#title'       => '{{Log Out }}' . customer_info('username') . ' (' . customer_info('customer_id') . ')',
								),
							);
							?>

							<?= build_links($account_links, array(
								'sort' => false,
							)); ?>
						</div>
					</div>
				<? } else { ?>
					<div class="link-menu account-home align-right bar-separator">
						<a class="link link-login" href="<?= site_url('customer/login'); ?>">{{Log In}}</a>
						<a class="link link-register sm-visible" href="<?= site_url('customer/login', 'register'); ?>">{{Register}}</a>
					</div>
				<? } ?>

			</div>
		</div>
	</header>

	<main class="main">
		<script type="text/javascript">
			setTimeout(_ffix,0);
			window.addEventListener('resize', _ffix, true);

			function _ffix() {
				$('#container').css({
					paddingTop:    $('header.site-header').outerHeight(),
					paddingBottom: $('footer.site-footer').outerHeight()
				});
			}

			setTimeout(_isScrollTop,0);
			$(document).on('scroll', _isScrollTop);

			function _isScrollTop() {$('body').toggleClass('scroll-top', $(document).scrollTop() <= 0)}
		</script>

		<? if (show_area('above')) { ?>
			<section class="area-above row">
				<div class="wrap">
					<?= area('above'); ?>
				</div>
			</section>
		<? } ?>
