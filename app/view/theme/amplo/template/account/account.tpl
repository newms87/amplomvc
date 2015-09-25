<?= $is_ajax ? '' : call('header'); ?>
<?= area('left') . area('right'); ?>

<section class="account-page content <?= $path === 'account' ? 'account-menu' : ''; ?>">
	<header class="row account-details section-style-a">
		<div class="wrap">
			<div class="side-menu col xs-12 xs-center lg-3 lg-left top">
				<h1 class="lg-hidden">{{My Account}}</h1>
				<? if (has_links('account_menu')) { ?>
					<? foreach (get_links('account_menu') as $link) { ?>
						<a href="<?= $link['href']; ?>" class="menu-tab <?= $path === $link['path'] ? 'active' : ''; ?>" title="<?= $link['title']; ?>" data-tab="#<?= $link['name']; ?>">{{<?= $link['display_name']; ?>}}</a>
					<? } ?>
				<? } else { ?>
					<a href="<?= site_url('account/details'); ?>" class="menu-tab <?= $path === 'account/details' ? 'active' : ''; ?>" data-tab="#account-details">{{My Details}}</a>
				<? } ?>
			</div>

			<div class="content-box col xs-12 lg-9 top">
				<div id="<?= slug($path); ?>" class="content">
					<?= $content; ?>
				</div>
			</div>
		</div>
	</header>
</section>

<script type="text/javascript">
	var $account = $('.account-page');

	$account.find('.side-menu a').not('.load-page').click(function () {
		var $r-> = $(this);

		if ($r->.is('.active')) {
			if (screen_width <= 1024) {
				$account.addClass('account-menu');
				$r->.removeClass('active');
			}
		} else {
			$account.find('.side-menu .active').removeClass('active');
			$r->.addClass('active');
			$account.removeClass('account-menu');
			$account.find('.content-box').addClass("loading");

			var action = $r->.attr('href');

			$.post(action, {}, function (response) {
				$account.find('.content-box').html(response).removeClass('loading');
				history.pushState({url: action}, $(response).find('h1').html(), action);
			});
		}
		return false;
	});
</script>

<?= $is_ajax ? '' : call('footer'); ?>
