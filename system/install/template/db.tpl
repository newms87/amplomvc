<!DOCTYPE html>
<html>
	<head>
		<title><?= _l("Amplo MVC Installation"); ?></title>

		<link rel="stylesheet" media="screen" type="text/css" href="system/install/install.css"/>
	</head>
	<body>
		<div id="container">
			<div id="header">
				<div id="logo">
					<img src="<?= $logo; ?>" title="<?= $name; ?>" alt="<?= $name; ?>"/>

					<div id="slogan"><?= _l("This is the Amplo MVC Installation page. We'll get you setup quick!"); ?></div>
				</div>
			</div>

			<div id="content_holder">
				<h1><?= _l("Please fill out the following information to setup Amplo MVC"); ?></h1>

				<div id="notification">
					<?php if ($error_msg) { ?>
						<div class="message warning"><?= $error_msg; ?></div>
					<?php } ?>

					<?php if ($success_msg) { ?>
						<div class="message success"><?= $success_msg; ?></div>
					<?php } ?>
				</div>

				<form action="" method="post">
					<div class="install_item">
						<label><?= _l("Database Type:"); ?></label>
						<select name="db_driver">
							<?php foreach ($db_drivers as $key => $name) { ?>
								<option value="<?= $key; ?>" <?= $key === $db_driver ? 'selected="selected"' : ''; ?>><?= $name; ?></option>
							<?php } ?>
						</select>

						<div class="help_icon_box"><span class="help_icon"><span class="help_icon_popup"><?= _l("If you do not know what this is, just choose MySQL!"); ?></span></span></div>
					</div>
					<div class="install_item">
						<label for="db_host"><?= _l("Database Host:"); ?></label>
						<input id="db_host" type="text" name="db_host" value="<?= $db_host; ?>" size="40"/>
					</div>
					<div class="install_item">
						<label for="db_name"><?= _l("Database Name:"); ?></label>
						<input id="db_name" type="text" name="db_name" value="<?= $db_name; ?>"/>
					</div>
					<div class="install_item">
						<label for="db_username"><?= _l("Database Username:"); ?></label>
						<input id="db_username" type="text" name="db_username" value="<?= $db_username; ?>"/>
					</div>
					<div class="install_item">
						<label for="db_password"><?= _l("Database Password:"); ?></label>
						<input id="db_password" type="text" name="db_password" value="<?= $db_password; ?>"/>
					</div>
					<div class="install_item">
						<label for="db_prefix"><?= _l("Database Prefix:"); ?></label>
						<input id="db_prefix" type="text" name="db_prefix" value="<?= $db_prefix; ?>" size="3"/>
					</div>
					<div class="install_item">
						<input type="hidden" name="action" value="db_setup"/>
						<input type="submit" class="button" value="<?= _l("Setup Database"); ?>"/>
					</div>
				</form>
			</div>
		</div>

	</body>
</html>