<?= _call('common/header'); ?>
<?= _area('left'); ?>
<?= _area('right'); ?>

<section id="user-login" class="content">
	<header class="login-top row">
		<div class="wrap">
			<?= _breadcrumbs(); ?>

			<h1><?= _l("Account Login"); ?></h1>
		</div>
	</header>

	<?= _area('top'); ?>

	<div class="login-page row">
		<div class="wrap">
			<div class="col xs-12 md-6">
				<div id="one-click" class="login">
					<a href="<?= $gp_login; ?>" class="gp-login large" title="<?= _l("Login with Google+"); ?>"></a>
					<a href="<?= $fb_login; ?>" class="fb-login large" title="<?= _l("Login with Facebook"); ?>"></a>
				</div>

				<div class="register form">
					<h2><?= _l("New Customer"); ?></h2>

					<div class="form-item text">
						<?= _l("By creating an account you will be able to shop faster, be up to date on an order's status<br /> and keep track of the orders you have previously made."); ?>
					</div>

					<div class="form-item submit">
						<a href="<?= $register; ?>" class="button"><?= _l("Register"); ?></a>
					</div>
				</div>
			</div>

			<div class="col xs-12 md-6">
				<h2><?= _l("Returning Customer"); ?></h2>

				<form action="<?= $login; ?>" method="post" enctype="multipart/form-data" class="form">
					<div class="form-item">
						<input id="login-email" placeholder="<?= _l("Username / Email"); ?>" type="text" name="username" value="<?= $username; ?>"/>
					</div>
					<div class="form-item">
						<input id="login-password" placeholder="<?= _l("Password"); ?>" type="password" name="password" value=""/>
						<br />
						<a href="<?= $forgotten; ?>" class="small-text"><?= _l("Forgotten Password"); ?></a>
					</div>
					<div class="form-item submit">
						<input type="submit" value="<?= _l("Login"); ?>" class="button"/>
					</div>
				</form>
			</div>
		</div>
	</div>

	<?= _area('bottom'); ?>
</section>

<?= _call('common/footer'); ?>
