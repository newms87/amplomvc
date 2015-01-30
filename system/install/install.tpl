<!DOCTYPE html>
<html>
<head>
	<title><?php echo _l("Amplo MVC Installation"); ?></title>

	<link rel="stylesheet" media="screen" type="text/css" href="<?php echo URL_SITE . 'system/install/install.css'; ?>"/>
</head>
<body>
<div id="container">
	<div id="header">
		<div id="logo">
			<img src="<?php echo URL_SITE . 'app/view/theme/amplo/image/amplo-vlogo.png'; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>"/>

			<div id="slogan"><?php echo _l("This is the Amplo MVC Installation page. We'll get you setup quick!"); ?></div>
		</div>
	</div>

	<div id="content_holder">
		<div id="notification">
			<?php if ($msg['error']) { ?>
				<div class="message warning"><?php echo $msg['error']; ?></div>
			<?php } ?>

			<?php if ($msg['success']) { ?>
				<div class="message success"><?php echo $msg['success']; ?></div>
			<?php } ?>
		</div>

		<form action="" method="post">
			<h2><?php echo _l("Database Configuration"); ?></h2>

			<div class="install_item">
				<label><?php echo _l("Database Type:"); ?></label>
				<select name="db_driver">
					<?php foreach ($db_drivers as $key => $name) { ?>
						<option value="<?php echo $key; ?>" <?php echo $key === $db_driver ? 'selected="selected"' : ''; ?>><?php echo $name; ?></option>
					<?php } ?>
				</select>

				<div class="help_icon_box">
					<span class="help_icon"><span class="help_icon_popup"><?php echo _l("If you do not know what this is, just choose MySQL!"); ?></span></span>
				</div>
			</div>
			<div class="install_item">
				<label for="db_host"><?php echo _l("Database Host:"); ?></label>
				<input id="db_host" type="text" name="db_host" value="<?php echo $db_host; ?>" size="40"/>
			</div>
			<div class="install_item">
				<label for="db_name"><?php echo _l("Database Name:"); ?></label>
				<input id="db_name" type="text" name="db_name" value="<?php echo $db_name; ?>"/>
			</div>
			<div class="install_item">
				<label for="db_username"><?php echo _l("Database Username:"); ?></label>
				<input id="db_username" type="text" name="db_username" value="<?php echo $db_username; ?>"/>
			</div>
			<div class="install_item">
				<label for="db_password"><?php echo _l("Database Password:"); ?></label>
				<input id="db_password" type="text" name="db_password" value="<?php echo $db_password; ?>"/>
			</div>
			<div class="install_item">
				<label for="db_prefix"><?php echo _l("Database Prefix:"); ?></label>
				<input id="db_prefix" type="text" name="db_prefix" value="<?php echo $db_prefix; ?>" size="3"/>
			</div>

			<h2><?php echo _l("Admin User Registration"); ?></h2>

			<div class="install_item">
				<label for="username"><?php echo _l("Username:"); ?></label>
				<input id="username" type="text" name="username" value="<?php echo $username; ?>"/>
			</div>
			<div class="install_item">
				<label for="email"><?php echo _l("Email:"); ?></label>
				<input id="email" type="text" name="email" value="<?php echo $email; ?>"/>
			</div>
			<div class="install_item">
				<label for="password"><?php echo _l("Password:"); ?></label>
				<input id="password" type="password" name="password" value="<?php echo $password; ?>"/>
			</div>
			<div class="install_item">
				<label for="confirm"><?php echo _l("Confirm:"); ?></label>
				<input id="confirm" type="password" name="confirm" value="<?php echo $confirm; ?>"/>
			</div>
			<div class="install_item">
				<input type="submit" class="button" value="<?php echo _l("Install Amplo MVC"); ?>"/>
			</div>
		</form>
	</div>
</div>

</body>
</html>
