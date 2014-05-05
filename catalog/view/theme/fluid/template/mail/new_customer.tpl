<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=320, target-densitydpi=device-dpi">
	</head>
	<body style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #000000;">
		<div style="width: 680px;">
			<a href="<?= $store['url']; ?>" title="<?= $store['name']; ?>">
				<img src="<?= $logo; ?>" alt="<?= $store['name']; ?>" width="<?= $logo_width; ?>" height="<?= $logo_height; ?>" style="margin-bottom: 20px; border: none;"/>
			</a>

			<p style="margin-top: 0px; margin-bottom: 20px;">
				<?= _l("Thank you for registering with %s!", $store['name']); ?>
			</p>

			<? if (!empty($reset_password)) { ?>
				<p>
					<?= _l("Thank you for signing up with us! If you would like to access your account directly, please "); ?>
					<a href="<?= $reset_password; ?>"><?= _l("Create a Password"); ?></a>
				</p>
			<? } else { ?>
				<p>
					<?= _l("You may login to your account using your username and password you just setup!"); ?>
					<a href="<?= $login; ?>"><?= _l("Login Here."); ?></a>
				</p>
			<? } ?>

			<p style="margin-top: 0px; margin-bottom: 20px;"><?= _l("Please reply to this email if you have any questions."); ?></p>
		</div>
	</body>
</html>
