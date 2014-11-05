<!DOCTYPE html>
<html dir="<?= $direction; ?>" lang="<?= $lang; ?>">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

		<title><?= $title; ?></title>

		<? if ($description) { ?>
			<meta name="description" content="<?= $description; ?>"/>
		<? } ?>
		<? if ($keywords) { ?>
			<meta name="keywords" content="<?= $keywords; ?>"/>
		<? } ?>

		<? if (!empty($author)) { ?>
			<meta name="author" content="">
		<? } ?>

		<base href="<?= $base; ?>"/>

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

		<!--[if IE 9]>
		<link rel="stylesheet" type="text/css" href="<?= theme_url('css/ie9.css'); ?>"/>
		<![endif]-->
		<!--[if IE 8]>
		<link rel="stylesheet" type="text/css" href="<?= theme_url('css/ie8.css'); ?>"/>
		<![endif]-->

		<!--[if lt IE 9]>
		<script src="<?= URL_RESOURCES . 'js/html5shiv.js'; ?>"></script>
		<![endif]-->

		<? if (option('config_google_analytics')) { ?>
			<!-- Google Analytics Tracker -->
			<script type="text/javascript">
				(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
					(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
					m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
				})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

				<? if (option('config_ga_domains')) { ?>
				ga('create', '<?= option('config_google_analytics'); ?>', 'auto', {'allowLinker': true});
				ga('require', 'linker');
				ga('linker:autoLink', ['<?= implode("','", option('config_ga_domains')); ?>']);
				<? } else { ?>
				ga('create', '<?= option('config_google_analytics'); ?>', 'auto');
				<? } ?>

				<? if (option('config_ga_experiment_id')) { ?>
				ga('set', 'expId', '<?= option('config_ga_experiment_id'); ?>');
				ga('set', 'expVar', '<?= $this->google->getExperimentVariation(); ?>');
				<? } ?>

				ga('send', 'pageview', {'dimension1': "<?= DOMAIN; ?>"});

				<? if (option('config_ga_click_tracking')) { ?>
				//Global click tracking
				$(document).click(function (event) {
					var $t = $(event.target);

					if (!$t.is('a') && $t.closest('a').length) {
						$t = $t.closest('a');
					}

					var category = $t.attr('href') || $t.attr('src') || $t.attr('id') || $t[0].nodeName;

					var id = $t.attr('id') || $t.closest('[id]').attr('id') || '',
						cls = $t.attr('class') || $t.closest('[class]').attr('class') || '';

					var label = (id?'#'+id:'') + (cls?'.'+cls:'');

					ga('send', 'event', category, 'click', label);
				});
				<? } ?>
			</script>
		<? } ?>

		<script type="text/javascript">
			$ac.device_width = window.innerWidth || screen.width;
		</script>
	</head>

<body class="<?= $body_class; ?> <?= $show_admin_bar ? 'admin-bar' : ''; ?>">
<section id="container">
	<header class="main-header row top-row">
		<? if ($show_admin_bar) { ?>
			<?= block('widget/admin_bar'); ?>
		<? } ?>

		<div class="wrap">
			<? if ($logo) { ?>
				<div id="logo" class="col xs-4 xs-left">
					<a href="<?= site_url(); ?>" class="block">
						<? if (option('config_logo_srcset')) { ?>
							<img <?= image_srcset($logo, option('config_logo_srcset'), option('config_logo_width'), option('config_logo_height')); ?> title="<?= $name; ?>" alt="<?= $name; ?>"/>
						<? } else { ?>
							<img src="<?= image($logo, option('config_logo_width'), option('config_logo_height')); ?>" title="<?= $name; ?>" alt="<?= $name; ?>"/>
						<? } ?>
					</a>
				</div>
			<? } ?>

			<? if ($multi_language) { ?>
				<?= block('localisation/language'); ?>
			<? } ?>

			<? if ($multi_currency) { ?>
				<?= block('localisation/currency'); ?>
			<? } ?>

			<div id="links-account" class="links">
				<? if (!is_logged()) { ?>
					<div class="login-buttons">
						<a class="button scale login" href="<?= site_url('customer/login'); ?>"><?= _l("Log In"); ?></a>
						<a class="button scale register" href="<?= site_url('customer/registration'); ?>"><?= _l("Sign Up"); ?></a>
					</div>
					<div class="login-content">
						<div class="login-form"><?= block('account/login'); ?></div>
					</div>
				<? } else { ?>
					<div class="login-buttons">
						<a class="button scale my-account" href="<?= site_url('account'); ?>"><?= _l("%s's Account", $customer['firstname']); ?></a>
						<a class="button scale logout" href="<?= site_url('customer/logout'); ?>"><?= _l("Log Out"); ?></a>
					</div>
				<? } ?>

				<div class="login-links">
					<?= links('account'); ?>
				</div>
			</div>


			<div id="header-social-networks">
				<?= block('extras/social_media'); ?>
			</div>

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
