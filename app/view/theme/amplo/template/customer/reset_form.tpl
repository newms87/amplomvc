<?= call('common/header'); ?>

<section id="reset-password-page" class="content">
	<header class="row top-row">
		<div class="wrap">
			<?= IS_AJAX ? '' : breadcrumbs(); ?>

			<h1><?= _l("Reset Your Password"); ?></h1>
		</div>
	</header>

	<div class="row reset-password">
		<div class="wrap">
			<div class="col xs-8 md-6 lg-5 center">
				<form action="<?= $save; ?>" class="form full-width" method="post" enctype="multipart/form-data">
					<h2><?= _l("Enter your new Password:"); ?></h2>

					<div class="form-item">
						<input type="password" autocomplete="off" placeholder="<?= _l("New Password"); ?>" name="password" value=""/>
					</div>
					<div class="form-item">
						<input type="password" name="confirm" placeholder="<?= _l("Confirm"); ?>" value=""/>
					</div>

					<div class="buttons">
						<div class="left">
							<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
						</div>
						<div class="right">
							<button class="button"><?= _l("Change Password"); ?></button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>

<script type="text/javascript">
	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= call('common/footer'); ?>
