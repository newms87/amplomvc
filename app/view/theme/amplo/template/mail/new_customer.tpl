<?= call('mail/header', $header); ?>

<p style="margin-top: 0px; margin-bottom: 20px;">
	<?= _l("Thank you for registering with %s!", $store['name']); ?>
</p>

<? if (option('config_account_approval')) { ?>
	<p>{{Your account must be approved before you can login. You will be notified once your account has been approved.}}</p>
<? } else { ?>
	<? if (!empty($reset_password)) { ?>
		<p>
			{{Thank you for signing up with us! If you would like to access your account directly, please }}
			<a href="<?= $reset_password; ?>">{{Create a Password}}</a>
		</p>
	<? } else { ?>
		<p>
			{{You may login to your account using your username and password you just setup!}}
			<a href="<?= $login; ?>">{{Login Here.}}</a>
		</p>
	<? } ?>
<? } ?>

<p style="margin-top: 0px; margin-bottom: 20px;">{{Please reply to this email if you have any questions.}}</p>

<?= call('mail/footer'); ?>
