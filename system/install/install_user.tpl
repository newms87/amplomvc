<!DOCTYPE html>
<? if (isset($_SERVER['HTTP_USER_AGENT']) && !strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6')) echo '<?xml version="1.0" encoding="UTF-8"?>'. "\n"; ?>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en" xml:lang="en">
<head>
<title><?= $text_title; ?></title>

<link rel="stylesheet" media="screen" type="text/css" href="system/install/install.css" />
</head>
<body>
<div id="container">
	<div id="header">
		<div id="logo">
			<img src="<?= $logo; ?>" title="<?= $name; ?>" alt="<?= $name; ?>" />
			<div id="slogan"><?= $text_slogan; ?></div>
		</div>
	</div>

	<div id="content_holder">
		<h1><?= $text_setup; ?></h1>

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
				<label for="username"><?= $entry_username; ?></label>
				<input id="username" type="text" name="username" value="<?= $username; ?>" />
			</div>
			<div class="install_item">
				<label for="email"><?= $entry_email; ?></label>
				<input id="email" type="text" name="email" value="<?= $email; ?>" />
			</div>
			<div class="install_item">
				<label for="password"><?= $entry_password; ?></label>
				<input id="password" type="password" name="password" value="<?= $password; ?>" />
			</div>
			<div class="install_item">
				<label for="confirm"><?= $entry_confirm; ?></label>
				<input id="confirm" type="password" name="confirm" value="<?= $confirm; ?>" />
			</div>
			<div class="install_item">
				<input type="hidden" name="action" value="user_setup" />
				<input type="submit" class="button" value="<?= $button_submit_user; ?>" />
			</div>
		</form>
	</div>
</div>
