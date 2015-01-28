<?= $is_ajax ? '' : call('header'); ?>
<?= area('left'); ?>
<?= area('right'); ?>

<section id="user-login" class="content">
	<header class="login-top row">
		<div class="wrap">
			<?= $is_ajax ? '' : breadcrumbs(); ?>

			<h1>{{Account Login}}</h1>
		</div>
	</header>

	<?= area('top'); ?>

	<div class="login-page row">
		<div class="wrap">
			<?= block('account/login'); ?>
		</div>
	</div>

	<?= area('bottom'); ?>
</section>

<?= $is_ajax ? '' : call('footer'); ?>
