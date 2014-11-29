<?= call('header'); ?>
<?= area('left'); ?>
<?= area('right'); ?>

<section id="user-login" class="content">
	<header class="login-top row">
		<div class="wrap">
			<?= IS_AJAX ? '' : breadcrumbs(); ?>

			<h1><?= _l("Account Login"); ?></h1>
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

<?= call('footer'); ?>
