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

	<? if (!empty($icons)) { ?>
		<? foreach ($icons as $size => $icon) { ?>
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
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', '<?= option('config_google_analytics'); ?>']);
			_gaq.push(['_trackPageview']);

			(function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();
		</script>
	<? } ?>

	<? $statcounter = option('config_statcounter'); ?>

	<? if ($statcounter) { ?>
		<!-- Stat Counter Tracker -->
		<script type="text/javascript">
			var sc_project = "<?= $statcounter['project']; ?>";
			var sc_invisible = 1;
			var sc_security = "<?= $statcounter['security']; ?>";

			$(document).ready(function () {
				$.getScript('http://www.statcounter.com/counter/counter.js');
			});
		</script>
		<noscript>
			<div class="statcounter">
				<a title="tumblr tracker" href="http://statcounter.com/tumblr/" target="_blank">
					<img class="statcounter" src="http://c.statcounter.com/<?= $statcounter['project']; ?>/0/<?= $statcounter['security']; ?>/1/" alt="tumblr tracker"/>
				</a>
			</div>
		</noscript>
	<? } ?>

</head>

<body class="<?= $body_class; ?> <?= $show_admin_bar ? 'admin-bar' : ''; ?>">
<section id="container">
	<header class="main-header row top-row">
		<? if ($show_admin_bar) { ?>
			<?= block('widget/admin_bar'); ?>
		<? } ?>

		<div class="wrap">
			<? if ($logo) { ?>
				<div id="logo">
					<a href="<?= site_url(); ?>" class="block">
						<img src="<?= image($logo, option('config_logo_width'), option('config_logo_height')); ?>" title="<?= $name; ?>" alt="<?= $name; ?>"/>

						<? if (!empty($slogan)) { ?>
							<div id="slogan"><?= $slogan; ?></div>
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
						<a class="button scale login" href="<?= site_url('customer/login'); ?>">{{Log In}}</a>
						<a class="button scale register" href="<?= site_url('customer/registration'); ?>">{{Sign Up}}</a>
					</div>
					<div class="login-content">
						<div class="login-form"><?= block('account/login'); ?></div>
					</div>
				<? } else { ?>
					<div class="login-buttons">
						<a class="button scale my-account" href="<?= site_url('account'); ?>"><?= _l("%s's Account", $customer['firstname']); ?></a>
						<a class="button scale logout" href="<?= site_url('customer/logout'); ?>">{{Log Out}}</a>
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
