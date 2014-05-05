<div id="block-account-login" class="block-content">
	<div class="col xs-12 sm-6 md-4 text-center">
		<div class="one-click large">
			<h2><?= _l("Connect With..."); ?></h2>
			<a href="<?= $gp_login; ?>" class="gp-login large"></a>
			<a href="<?= $fb_login; ?>" class="fb-login large"></a>
		</div>
	</div>

	<div class="col xs-12 sm-6 md-4 text-center">
		<div class="login-register">
			<h2><?= _l("Register a New Account"); ?></h2>

			<p><?= _l("Shop faster and track orders by registering!"); ?></p>

			<a href="<?= site_url('customer/registration'); ?>" class="button medium"><?= "Register"; ?></a>
		</div>
	</div>

	<div class="col xs-12 md-4 text-center">
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
				<button><?= _l("Log In"); ?></button>
			</div>
		</form>
	</div>
</div>