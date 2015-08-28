<?= $is_ajax ? '' : call('header'); ?>
<?= area('left') . area('right'); ?>

<section class="account-page content">
	<header class="row account-details section-style-b">
		<div class="wrap">
			<div class="side-menu col xs-12 xs-center lg-3 lg-left top">
				<a href="<?= site_url('account/details'); ?>" class="menu-tab <?= ($path === 'account/details' || $path === 'account') ? 'active' : ''; ?>" data-tab="#my-details">{{My Details}}</a>
				<a href="<?= site_url('account/payment'); ?>" class="menu-tab <?= $path === 'account/payment' ? 'active' : ''; ?>" data-tab="#my-details">{{Payments}}</a>
				<a href="<? site_url('account/scopes'); ?>" class="menu-tab">{{My Scopes}}</a>
			</div>

			<div class="col xs-12 right">

				<div class="col xs-top sm-middle md-bottom xs-12 sm-6 lg-4 xl-1">
					hello<br>
					hello <Br>
					world
				</div>

				<div class="col xs-top sm-middle md-bottom xs-12 sm-6 lg-4 xl-1">
					hello
				</div>

				<div class="col xs-top sm-middle md-bottom xs-12 sm-6 lg-4 xl-1">
					hello<br>
					hello <Br>
					world<br>
					hello<br>
					hello <Br>
					world<br>
					hello<br>
					hello <Br>
					world
				</div>
			</div>

			<div class="tab-content col xs-12 lg-9 top">
				<?= $content; ?>
			</div>
		</div>
		<form action="">
			<button data-loading="{{Loading...}}">Yay!</button>
		</form>
	</header>
</section>

<script type="text/javascript">
	$('form').submit(function() {
		$(this).find('[data-loading]').loading();
		$(this).find('[data-loading]').loading('stop');

	});
</script>

<?= $is_ajax ? '' : call('footer'); ?>
