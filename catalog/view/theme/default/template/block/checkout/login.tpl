<div id="checkout_register" class="left">
	<div class='janrain_login'><?= $rpx_login; ?></div>
	<h2><?= $text_new_customer; ?></h2>

	<p><?= $text_checkout; ?></p>
	<label for="register">
		<input type="radio" name="account" value="register" id="register" checked="checked"/>
		<b><?= $text_register; ?></b>
	</label>
	<br/>
	<? if ($guest_checkout) { ?>
		<label for="guest">
			<input type="radio" name="account" value="guest" id="guest"/>
			<b><?= $text_guest; ?></b>
		</label>
		<br/>
	<? } ?>
	<br/>

	<p><?= $text_register_account; ?></p>
	<input type="button" value="<?= $button_continue; ?>" id="button-account" class="button" onclick="submit_checkout_item($(this));"/>
</div>
<div id="checkout_login" class="right">
	<h2><?= $text_returning_customer; ?></h2>

	<p><?= $text_i_am_returning_customer; ?></p>

	<form action="<?= $validate_login; ?>" method="post">
		<label for="username"><?= $entry_username; ?></label><br/>
		<input id="username" type="text" name="username" value="<?= $username; ?>"/><br/><br/>
		<label for="password"><?= $entry_password; ?></label><br/>
		<input id="password" type="password" name="password" value=""/><br/><br/>
		<input type="submit" class="button" value="<?= $button_submit; ?>"/><br/>
		<br/><a href="<?= $url_forgotten; ?>"><?= $text_forgotten; ?></a>
	</form>
</div>