<div id="checkout_signin" class="login_content section">
	<div class="left">
		<div id="one_click" class="section login">
			<h2><?= _l("Connect With..."); ?></h2>
			<a href="<?= $gp_login; ?>" class="gp_login large"></a>
			<a href="<?= $fb_login; ?>" class="fb_login large"></a>
		</div>

		<div id="checkout_register" class="section">
			<h2><?= _l("Register a New Account") . (!empty($guest_checkout) ? _l(" or checkout as a Guest") : ''); ?></h2>

			<p><?= _l("By creating an account you will be able to shop faster, be up to date on an order's status, and keep track of the orders you have previously made."); ?></p>

			<a href="<?= $register; ?>" class="button medium"><?= "Register"; ?></a>

			<? if (!empty($guest_checkout)) { ?>
				<a href="<?= $guest_checkout; ?>" class="button medium"><?= _l("Guest Checkout"); ?></a>
			<? } ?>
		</div>
	</div>

	<div class="right">
		<h2><?= _l("Returning Customer"); ?></h2>

		<form action="<?= $login; ?>" method="post" enctype="multipart/form-data">
			<div class="section">
				<label for="login_email"><?= _l("Username or Email"); ?></label>
				<br />
				<input id="login_email" type="text" name="username" value="<?= $username; ?>"/>
				<br/>
				<br/>
				<label for="login_password"><?= _l("Password"); ?></label>
				<br />
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
