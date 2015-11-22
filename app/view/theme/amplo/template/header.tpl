<!DOCTYPE html>
<html dir="<?= language_info('direction'); ?>" lang="<?= language_info('code'); ?>">
<head>
	<?= head(); ?>
</head>

<body class="<?= page_info('body_class'); ?> <?= $show_admin_bar ? 'admin-bar' : ''; ?>">
<? option('ga_code') ? include_once(theme_dir('template/common/ga.tpl')) : ''; ?>
<? option('track_statcounter') ? include_once(theme_dir('template/common/statcounter.tpl')) : ''; ?>

<section id="container">
	<header class="main-header row top-row">
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
							<span class="left-text">{{Try
								<a href="<?= site_url('account/details', 'register_phone'); ?>">Text-to-Scope</a>}}</span> |
							{{Need Help?}}
							<a class="phone" href="tel:<?= preg_replace("/[^\\d]/", '', option('site_phone')); ?>">1-877-MY-SCOPE</a>
						</div>
					</div>
				</div>
			</div>

			<script>

				if (screen_width > 1024) {
					$(window).scroll(function () {
						$('.banner-bar-row').toggleClass('hide', $(window).scrollTop() > 20);
					}).scroll();
				}

				/*
				 function cycle_banner() {
				 var $show = $('.banner-bar .slide.show');
				 var $next = $show.removeClass('show').next();

				 if (!$next.length) {
				 $next = $('.banner-bar .slide:first');
				 }

				 $next.addClass('show');

				 setTimeout(cycle_banner, 5000);
				 }

				 setTimeout(cycle_banner, 5000);
				 */
			</script>

		</div>

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
				<div class="mobile-menu-toggle lg-hidden" onclick="$(this).toggleClass('active')">
					<b class="fa fa-reorder mm-show"></b>
					<b class="fa fa-remove mm-hide"></b>
				</div>

				<div class="mobile-menu horizontal no-parent-scroll accordian">
					<a href="<?= site_url(); ?>" class="link-menu mobile-order-link lg-hidden">
						<div class="parent">{{Order Now}}</div>
					</a>

					<?
					$r->document->removeLink('polyscope', 'company');

					$r->document->addLink('polyscope', array(
						'name'         => 'about-scope',
						'parent'       => 'about',
						'display_name' => '{{About}}',
						'href'         => site_url('page/about'),
						'sort_order'   => 0,
					));

					$polyscope_links = get_links('polyscope');

					$polyscope_links['about']['show_on'] = 'click';

					$options = array(
						'cache' => true,
						'index' => 'site_id',
					);

					$child_sites = $r->Model_Site->getRecords(array('site_id' => 'ASC'), null, $options);

					$child_sites['proscope'] = array(
						'name'    => 'ProScope',
						'ssl'     => site_url('scope/proscope'),
						'site_id' => 20,
					);

					unset($child_sites[option('site_id')]);

					foreach ($child_sites as $key => &$site) {
						$scope_name = strtolower($site['name']);
						$img        = image_srcset(build_srcset(DIR_THEMES . $scope_name . '/image/logo.png', 3, null, 30));

						$site['display_name'] = <<<HTML
							<img $img class="lg-visible" />
							<div class="scope-icon-name lg-hidden">
								<b class="amp-sprite si-$scope_name-icon"></b>
								<span class="text">$site[name]</span>
							</div>
HTML;
						$site['href']         = $site['ssl'];

						if ($site['name'] === 'BlueprintScope') {
							unset($child_sites[$key]);
						}
					}

					$polyscope_links['products'] = array(
						'display_name' => '{{Products}}',
						'children'     => $child_sites,
						'sort_order'   => 1.1,
						'show_on'      => 'click',
						'options'      => array(
							'sort' => 'site_id',
						),
					);

					$polyscope_links['help'] = array(
						'display_name' => '{{Help}}',
						'href'         => site_url('page/faq'),
						'sort_order'   => 11,
					);

					echo build_links($polyscope_links, array('class' => 'horizontal'));
					?>
				</div>
			</div>

			<div class="site-logo col lg-hidden xs-6">
				<a href="<?= site_url(); ?>">
					<?= $logo_html; ?>
				</a>
			</div>

			<div class="nav nav-account col xs-3 lg-4 right">
				<div class="link-menu account-home arrow-top align-right popup">
					<? if (is_logged()) { ?>
						<div class="parent" data-amp-toggle=".nav-account .account-home">
							<span class="text lg-visible">{{<?= customer_info('first_name') ? "Hi, " . customer_info('first_name') : "Welcome Back"; ?>!}}</span>
							<i class="fa fa-home"></i>
						</div>

						<div class="children">
							<? $account_links = array(
								'my-details' => array(
									'display_name' => '{{My Details}}',
									'path'         => 'account/details',
								),
								'scopes'     => array(
									'display_name' => '{{My Scopes}}',
									'path'         => 'account/scopes',
								),
								'payments'   => array(
									'display_name' => '{{My Payments}}',
									'path'         => 'account/payment',
								),
								'prodocs'    => array(
									'display_name' => '{{ProDocs}}',
									'path'         => 'doc',
									'class'        => 'dropdown',
									'show_on'      => 'expand',
									'hover'        => false,
									'children'     => array(
										'documents'        => array(
											'display_name' => '{{Documents}}',
											'path'         => 'doc',
										),
										'settings'         => array(
											'display_name' => '{{Settings}}',
											'path'         => 'doc/settings',
										),
										'template-manager' => array(
											'display_name' => '{{Templates}}',
											'path'         => 'doc/template_manager',
										),
										'tutorial'         => array(
											'display_name' => '{{Tutorials}}',
											'path'         => 'doc/tutorial',
										),
									),
								),
								'logout'     => array(
									'display_name' => '{{Log Out}}',
									'path'         => 'customer/logout',
									'#title'       => '{{Log Out }}' . customer_info('username') . ' (' . customer_info('customer_id') . ')',
								),
							);

							if (!PRODOC_STATUS) {
								unset($account_links['prodocs']);
							}

							if (customer_info('role') !== 'Leader') {
								unset($account_links['payments']);
							}

							?>

							<?= build_links($account_links, array(
								'sort' => false,
							)); ?>
						</div>
					<? } else { ?>
						<a class="link-login" href="<?= site_url('customer/login'); ?>">{{Log In}}</a>
						<a class="link-register sm-visible" href="<?= site_url('customer/login', 'register'); ?>">{{Register}}</a>
					<? } ?>
				</div>

				<a class="order-link link lg-visible" href="<?= site_url(); ?>">{{Order Now}}</a>
			</div>
		</div>
	</header>

	<main class="main">

		<script type="text/javascript">
			$(document).ready(function () {
				_ffix();
				window.addEventListener('resize', _ffix, true);
			}).on('scroll', _cfix);

			function _ffix() {
				$('main.main').css('padding-bottom', $('footer.site-footer').outerHeight());
			}
			function _cfix() {
				_cfix.$m.css('padding-top', (_cfix.h = Math.max(_cfix.$h.outerHeight(), _cfix.h || 0)) + parseInt(_cfix.$h.css('top')));
				$('body').toggleClass('scroll-top', $(document).scrollTop() <= 0);
			}

			_cfix.$h = $('header.main-header'), _cfix.$m = $('main.main');

			_cfix();
		</script>

		<? if (show_area('above')) { ?>
			<section class="area-above row">
				<div class="wrap">
					<?= area('above'); ?>
				</div>
			</section>
		<? } ?>
