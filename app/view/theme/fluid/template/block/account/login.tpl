<div id="block-account-login" class="block-content">
	<? if (!empty($medias)) { ?>
	<div class="col xs-12 sm-6 md-4 text-center">
		<div class="one-click large">
			<h2><?= _l("Connect With..."); ?></h2>
			<? foreach ($medias as $media) { ?>
				<a href="<?= $media['url']; ?>" class="social-login <?= $media['name'] . ' ' . $size; ?>"></a>
			<? } ?>
		</div>
	</div>
	<? } ?>

	<div class="col <?= !empty($medias) ? 'xs-12 sm-6 md-4' : 'xs-12 sm-6'; ?> text-center">
		<div class="login-register">
			<h2><?= _l("Register a New Account"); ?></h2>

			<p><?= _l("Shop faster and track orders by registering!"); ?></p>

			<a href="<?= site_url('customer/registration'); ?>" class="button medium"><?= "Register"; ?></a>
		</div>
	</div>

	<div class="col <?= !empty($medias) ? 'xs-12 sm-6 md-4' : 'xs-12 sm-6'; ?> text-center">
		<h2><?= _l("Returning Customer"); ?></h2>

		<form action="<?= site_url('customer/login'); ?>" class="form" method="post" enctype="multipart/form-data">
			<div class="form-item">
				<input type="text" placeholder="<?= _l("Username / Email"); ?>" name="username" value="<?= $username; ?>" />
			</div>
			<div class="form-item">
				<input type="password" placeholder="<?= _l("Password"); ?>" name="password" value="" />
				<div class="forgotten">
					<a href="<?= site_url('customer/forgotten'); ?>"><?= _l("Forgotten Password"); ?></a>
				</div>
			</div>

			<div class="form-item submit">
				<button data-loading="<?= _l("Please Wait..."); ?>"><?= _l("Log In"); ?></button>
			</div>
		</form>
	</div>
</div>
