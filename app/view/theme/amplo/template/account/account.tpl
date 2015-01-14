<?= $is_ajax ? '' : call('header'); ?>
<?= area('left') . area('right'); ?>

<section class="account-page content">
	<header class="row account-details section-style-b">
		<div class="wrap">
			<div class="account-nav col xs-12 xs-center lg-3 lg-left top">
				<a href="<?= site_url('account/details'); ?>" class="menu-tab <?= ($path === 'account/details' || $path === 'account') ? 'active' : ''; ?>" data-tab="#my-details">{{My Details}}</a>
				<a href="<?= site_url('account/scopes'); ?>" class="menu-tab">{{My Scopes}}</a>
			</div>

			<div class="tab-content col xs-12 lg-9 top">
				<?= $content; ?>
			</div>
		</div>
	</header>
</section>

<script type="text/javascript">

</script>

<?= $is_ajax ? '' : call('footer'); ?>
