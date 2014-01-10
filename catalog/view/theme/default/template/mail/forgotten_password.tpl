<html>
	<head></head>
	<body>
		<p><?= _l("A new password was requested for your account with us at %s.", $store_name); ?></p>
		<br />
		<br />
		<p><?= _l("To reset your password please visit this link:"); ?></p>
		<a href="<?= $reset; ?>"><?= $reset; ?></a>
		<br />
		<br />
		<p><?= _l("If you did not request for you password to be reset, please ignore this email."); ?></p>
	</body>
</html>
