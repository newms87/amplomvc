<?= $this->call('common/header'); ?>
<?= $this->area->render('left'); ?><?= $this->area->render('right'); ?>

<div id="user_login" class="content">
	<?= $this->breadcrumb->render(); ?>

	<?= $this->area->render('top'); ?>

	<h1><?= _l("Account Login"); ?></h1>

	<div class="section login_content">
		<div class="left">
			<div id="one_click" class="login">
				<a href="<?= $gp_login; ?>" class="gp_login large" title="<?= _l("Login with Google+"); ?>"></a>
				<a href="<?= $fb_login; ?>" class="fb_login large" title="<?= _l("Login with Facebook"); ?>"></a>
			</div>

			<div class="register">
				<h2><?= _l("New Customer"); ?></h2>

				<div class="section">
					<p><b><?= _l("Register New Account"); ?></b></p>

					<p><?= _l("By creating an account you will be able to shop faster, be up to date on an order's status, and keep track of the orders you have previously made."); ?></p>
					<a href="<?= $register; ?>" class="button"><?= _l("Register"); ?></a>
				</div>
			</div>
		</div>

		<div class="right">
			<h2><?= _l("Returning Customer"); ?></h2>

			<form action="<?= $login; ?>" method="post" enctype="multipart/form-data">
				<div class="section">
					<label for="login_email"><?= _l("Username or Email"); ?></label>
					<br/>
					<input id="login_email" type="text" name="username" value="<?= $username; ?>"/>
					<br/>
					<br/>
					<label for="login_password"><?= _l("Password"); ?></label>
					<br/>
					<input id="login_password" type="password" autocomplete="off" name="password" value=""/>
					<br/>
					<a href="<?= $forgotten; ?>"><?= _l("Forgotten Password"); ?></a>
					<br/>
					<br/>
					<input type="submit" value="<?= _l("Login"); ?>" class="button"/>
				</div>
			</form>
		</div>
	</div>

	<?= $this->area->render('bottom'); ?>
</div>

<?= $this->call('common/footer'); ?>
