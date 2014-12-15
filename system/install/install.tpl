<!DOCTYPE html>
<html>
	<head>
		<title>{{Amplo MVC Installation}}</title>

		<link rel="stylesheet" media="screen" type="text/css" href="system/install/install.css"/>
	</head>
	<body>
		<div id="container">
			<div id="header">
				<div id="logo">
					<img src="<?= $logo; ?>" title="<?= $name; ?>" alt="<?= $name; ?>"/>

					<div id="slogan">{{This is the Amplo MVC Installation page. We'll get you setup quick!}}</div>
				</div>
			</div>

			<div id="content_holder">
				<h1>{{Please fill out the following information to setup Amplo MVC}}</h1>

				<div id="notification">
					<?php if ($msg['error']) { ?>
						<div class="message warning"><?= $msg['error']; ?></div>
					<?php } ?>

					<?php if ($msg['success']) { ?>
						<div class="message success"><?= $msg['success']; ?></div>
					<?php } ?>
				</div>

				<form action="" method="post">
					<h2>{{Database Configuration}}</h2>
					<div class="install_item">
						<label>{{Database Type:}}</label>
						<select name="db_driver">
							<?php foreach ($db_drivers as $key => $name) { ?>
								<option value="<?= $key; ?>" <?= $key === $db_driver ? 'selected="selected"' : ''; ?>><?= $name; ?></option>
							<?php } ?>
						</select>

						<div class="help_icon_box"><span class="help_icon"><span class="help_icon_popup">{{If you do not know what this is, just choose MySQL!}}</span></span></div>
					</div>
					<div class="install_item">
						<label for="db_host">{{Database Host:}}</label>
						<input id="db_host" type="text" name="db_host" value="<?= $db_host; ?>" size="40"/>
					</div>
					<div class="install_item">
						<label for="db_name">{{Database Name:}}</label>
						<input id="db_name" type="text" name="db_name" value="<?= $db_name; ?>"/>
					</div>
					<div class="install_item">
						<label for="db_username">{{Database Username:}}</label>
						<input id="db_username" type="text" name="db_username" value="<?= $db_username; ?>"/>
					</div>
					<div class="install_item">
						<label for="db_password">{{Database Password:}}</label>
						<input id="db_password" type="text" name="db_password" value="<?= $db_password; ?>"/>
					</div>
					<div class="install_item">
						<label for="db_prefix">{{Database Prefix:}}</label>
						<input id="db_prefix" type="text" name="db_prefix" value="<?= $db_prefix; ?>" size="3"/>
					</div>

					<h2>{{Admin User Registration}}</h2>
					<div class="install_item">
						<label for="username">{{Username:}}</label>
						<input id="username" type="text" name="username" value="<?= $username; ?>"/>
					</div>
					<div class="install_item">
						<label for="email">{{Email:}}</label>
						<input id="email" type="text" name="email" value="<?= $email; ?>"/>
					</div>
					<div class="install_item">
						<label for="password">{{Password:}}</label>
						<input id="password" type="password" name="password" value="<?= $password; ?>"/>
					</div>
					<div class="install_item">
						<label for="confirm">{{Confirm:}}</label>
						<input id="confirm" type="password" name="confirm" value="<?= $confirm; ?>"/>
					</div>
					<div class="install_item">
						<input type="submit" class="button" value="{{Install Amplo MVC}}"/>
					</div>
				</form>
			</div>
		</div>

	</body>
</html>
