<!DOCTYPE html>
<? if (isset($_SERVER['HTTP_USER_AGENT']) && !strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6')) echo '<?xml version="1.0" encoding="UTF-8"?>'. "\n"; ?>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en" xml:lang="en">
<head>
<title><?= _("Amplocart Installation"); ?></title>

<link rel="stylesheet" media="screen" type="text/css" href="system/install/install.css" />
</head>
<body>
<div id="container">
	<div id="header">
		<div id="logo">
			<img src="<?= $logo; ?>" title="<?= $name; ?>" alt="<?= $name; ?>" />
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
				<label><?= _l("Database Type:"); ?></label>
				<select name="db_type">
					<? foreach ($db_types as $key => $name) { ?>
						<option value="<?= $key; ?>" <?= $key === $db_type ? 'selected="selected"' : ''; ?>><?= $name; ?></option>
					<? } ?>
				</select>
				<div class="help_icon_box"><span class="help_icon"><span class="help_icon_popup"><?= _l("If you do not know what this is, just choose MySQL!"); ?></span></span></div>
			</div>
			<div class="install_item">
				<label for="db_host"><?= _l("Database Host:"); ?></label>
				<input id="db_host" type="text" name="db_host" value="<?= $db_host; ?>" size="40"/>
			</div>
			<div class="install_item">
				<label for="db_name"><?= _l("Database Name:"); ?></label>
				<input id="db_name" type="text" name="db_name" value="<?= $db_name; ?>" />
			</div>
			<div class="install_item">
				<label for="db_username"><?= _l("Database Username:"); ?></label>
				<input id="db_username" type="text" name="db_username" value="<?= $db_username; ?>" />
			</div>
			<div class="install_item">
				<label for="db_password"><?= _l("Database Password:"); ?></label>
				<input id="db_password" type="text" name="db_password" value="<?= $db_password; ?>" />
			</div>
			<div class="install_item">
				<label for="db_prefix"><?= _l("Database Prefix:"); ?></label>
				<input id="db_prefix" type="text" name="db_prefix" value="<?= $db_prefix; ?>" size="3"/>
			</div>
			<div class="install_item">
				<input type="hidden" name="action" value="db_setup" />
				<input type="submit" class="button" value="<?= _l("Setup Database"); ?>" />
			</div>
		</form>
	</div>
</div>

