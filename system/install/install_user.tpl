<!DOCTYPE html>
<? if (isset($_SERVER['HTTP_USER_AGENT']) && !strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6')) {
	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
} ?>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en" xml:lang="en">
	<head>
		<title><?= _l("AmploCart Installation"); ?></title>

		<link rel="stylesheet" media="screen" type="text/css" href="system/install/install.css"/>
	</head>
	<body>
		<div id="container">
			<div id="header">
				<div id="logo">
					<img src="<?= $logo; ?>" title="<?= $name; ?>" alt="<?= $name; ?>"/>

					<div id="slogan"><?= _l("This is the AmploCart Installation page. We'll get you setup quick!"); ?></div>
				</div>
			</div>

			<div id="content_holder">
				<h1><?= _l("Please fill out the following information to setup AmploCart"); ?></h1>

				<div id="notification">
					<? if ($error_msg) { ?>
						<div class="message_box warning"><?= $error_msg; ?></div>
					<? } ?>

					<? if ($success_msg) { ?>
						<div class="message_box success"><?= $success_msg; ?></div>
					<? } ?>
				</div>

				<form action="" method="post">
					<div class="install_item">
						<label for="username"><?= _l("Username:"); ?></label>
						<input id="username" type="text" name="username" value="<?= $username; ?>"/>
					</div>
					<div class="install_item">
						<label for="email"><?= _l("Email:"); ?></label>
						<input id="email" type="text" name="email" value="<?= $email; ?>"/>
					</div>
					<div class="install_item">
						<label for="password"><?= _l("Password:"); ?></label>
						<input id="password" type="password" name="password" value="<?= $password; ?>"/>
					</div>
					<div class="install_item">
						<label for="confirm"><?= _l("Confirm:"); ?></label>
						<input id="confirm" type="password" name="confirm" value="<?= $confirm; ?>"/>
					</div>
					<div class="install_item">
						<input type="hidden" name="action" value="user_setup"/>
						<input type="submit" class="button" value="<?= _l("Continue"); ?>"/>
					</div>
				</form>
			</div>
		</div>
