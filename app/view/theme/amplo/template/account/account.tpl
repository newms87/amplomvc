<?= $is_ajax ? '' : call('header'); ?>
<?= area('left') . area('right'); ?>

<section class="account-page content <?= $path === 'account' ? 'account-home' : ''; ?>">
	<header class="row account-details section-style-a">
		<div class="wrap">
			<h1><?= page_info('title'); ?></h1>
			<h1 class="col lg-hidden account-home-title">{{My Account}}</h1>

			<div class="side-menu return-menu col xs-12 xs-center lg-hidden">
				<a href="<?= site_url('account'); ?>" class="menu-tab return"><?= page_info('title'); ?></a>
			</div>

			<div class="side-menu account-menu col xs-12 xs-center lg-3 lg-left top">
				<? if (has_links('account')) { ?>
					<? foreach (get_links('account') as $link) { ?>
						<a href="<?= $link['href']; ?>" class="menu-tab <?= $path === $link['path'] ? 'active' : ''; ?>" data-tab="#my-details"><?= $link['display_name']; ?></a>
					<? } ?>
				<? } else { ?>
					<a href="<?= site_url('account/details'); ?>" class="menu-tab <?= ($path === 'account/details' || $path === 'account') ? 'active' : ''; ?>" data-tab="#my-details">{{My Details}}</a>
				<? } ?>
			</div>

			<div class="content-box col xs-12 lg-9 top">
				<?= $content; ?>
			</div>
		</div>
	</header>
</section>

<?= $is_ajax ? '' : call('footer'); ?>
