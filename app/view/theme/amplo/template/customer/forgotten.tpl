<?= call('common/header'); ?>
<?= area('left'); ?>
<?= area('right'); ?>

<section id="forgotten-page" class="content">
	<header class="row top-row">
		<div class="wrap">
			<?= IS_AJAX ? '' : breadcrumbs(); ?>

			<h1><?= _l("Request a New Pasword"); ?></h1>
		</div>
	</header>

	<?= area('top'); ?>

	<div class="row forgotten-content">
		<div class="wrap">
			<form action="<?= $save; ?>" method="post" class="form full-width" enctype="multipart/form-data">
				<div class="col xs-8 md-6 lg-5 center">
					<h3><?= _l("Enter your Email address below to request a new password for your account."); ?></h3>
					<br />

					<div class="form-item">
						<input type="text" name="email" placeholder="<?= _l("Account Email Address"); ?>" value=""/>
					</div>

					<div class="buttons">
						<div class="left">
							<a href="<?= $back; ?>" class="button"><?= _l("Cancel"); ?></a>
						</div>
						<div class="right">
							<input type="submit" value="<?= _l("Request Reset"); ?>" class="button"/>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>

	<?= area('bottom'); ?>
</section>

<?= call('common/footer'); ?>
