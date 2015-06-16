<!DOCTYPE html>
<html dir="<?= language_info('direction'); ?>" lang="<?= language_info('code'); ?>">

<?= head(); ?>

<? $sprite = theme_sprite('sprite@1x.png'); ?>

<body class="<?= page_info('body_class'); ?> <?= $show_admin_bar ? 'admin-bar' : ''; ?>">
<? option('ga_code') ? include_once(theme_dir('template/common/ga.tpl')) : ''; ?>
<? option('track_statcounter') ? include_once(theme_dir('template/common/statcounter.tpl')) : ''; ?>

<section id="container">
	<? if (!empty($terms_page)) { ?>
		<div class="terms-agreement">
			{{You must agree to the <a href="<?= site_url('page', 'page_id=' . $terms_page['page_id']); ?>"><?= $terms_page['title']; ?></a>. After reviewing click <a href="<?= site_url('customer/agree-to-terms', array('redirect' => $this->url->here())); ?>">I agree</a>}}
		</div>
	<? } ?>

	<? if (option("show-header-banner", false)) { ?>
		<div class="banner-bar-row row">
			<div class="wrap">
				<div class="banner-bar">
					<div class="visit col lg-5 xs-12 xs-center lg-left slide show">{{Visit our booth at IRE (#2750)}}</div>
					<!--<div class="learn col lg-4 xs-hidden slide">{{Learn about our <a href="<?= site_url('scope/year-of-scopes'); ?>">Year of Scopes</a> >}}</div>-->
					<div class="help col lg-7 xs-12 xs-center lg-right slide">
						{{Need Help? <span class="lg-visible">We'd love to talk to you!</span>}}
						<a href="tel:<?= preg_replace("/[^\\d]/", '', option('site_phone')); ?>">1-877-MY-SCOPE</a>
					</div>
				</div>
			</div>

			<script>
				$(window).scroll(function (e) {
					$('.banner-bar-row').toggleClass('hide', $(window).scrollTop() > 20);
				});

				(function cycle_banner() {
					var $show = $('.banner-bar .slide.show');
					var $next = $show.removeClass('show').next();

					if (!$next.length) {
						$next = $('.banner-bar .slide:first');
					}

					$next.addClass('show');
				}).loop(5000);
			</script>
		</div>
	<? } ?>

	<header class="main-header row top-row">
		<? if ($show_admin_bar) { ?>
			<?= block('widget/admin_bar'); ?>
		<? } ?>

		<div class="wrap">
			<? if ($logo = option('site_logo')) { ?>
				<div id="logo" class="col xs-5 sm-4 md-3 lg-2 left top">
					<a href="<?= site_url(); ?>" class="block">
						<? if ($logo_srcset = option('site_logo_srcset')) { ?>
							<img <?= image_srcset(build_srcset($logo, $logo_srcset, option('site_logo_width'), option('site_logo_height')), $logo_srcset, option('site_name'), option('site_name')); ?> />
						<? } else { ?>
							<img <?= img($logo, option('site_logo_width'), option('site_logo_height'), option('site_name'), option('site_name')); ?> />
						<? } ?>
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

			<div class="header-navigation menu-button col xs-7 sm-8 md-9 lg-10 xs-right lg-left top">
				<div class="links-toggle lg-hidden" onclick="$(this).toggleClass('hover')">
					<div class="sprite-img menu">
						<img <?= $sprite; ?> />
					</div>
				</div>

				<nav class="header-nav col xs-12">
					<div id="links-primary" class="nav col xs-8 left middle">
						<div class="links horizontal">
							<?= links('primary'); ?>
						</div>
					</div>

					<div id="links-account" class="nav col xs-4 middle right">
						<div class="links links-simple">
							<? if (is_logged()) { ?>
								<a class="my-account" href="<?= site_url('account'); ?>">{{My Account}}</a>
								<a class="logout" href="<?= site_url('customer/logout'); ?>">{{Log Out}}</a>
							<? } else { ?>
								<a class="login" href="<?= site_url('customer/login'); ?>">{{Log In}}</a>
							<? } ?>
							<a class="lg-hidden close" onclick="$('.links-toggle').removeClass('hover')">{{Close}}</a>
						</div>
					</div>
				</nav>
			</div>

			<? if (option('config_social_media')) { ?>
				<div id="header-social-networks">
					<?= block('extras/social_media'); ?>
				</div>
			<? } ?>
		</div>
	</header>

	<script type="text/javascript">
		$(document).ready(fix_container_header);

		function fix_container_header(){
			$('#container').css({top: $('header.main-header').height()});
		}
	</script>

	<main class="main clearfix">

		<? if ($this->message->has()) { ?>
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
