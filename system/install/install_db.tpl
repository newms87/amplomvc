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
				<label><?= $entry_db_type; ?></label>
				<select name="db_type">
					<? foreach ($db_types as $key => $name) { ?>
						<option value="<?= $key; ?>" <?= $key === $db_type ? 'selected="selected"' : ''; ?>><?= $name; ?></option>
					<? } ?>
				</select>
				<div class="help_icon_box"><span class="help_icon"><span class="help_icon_popup"><?= $text_db_type_help; ?></span></span></div>
			</div>
			<div class="install_item">
				<label for="db_host"><?= $entry_db_host; ?></label>
				<input id="db_host" type="text" name="db_host" value="<?= $db_host; ?>" size="40"/>
			</div>
			<div class="install_item">
				<label for="db_name"><?= $entry_db_name; ?></label>
				<input id="db_name" type="text" name="db_name" value="<?= $db_name; ?>" />
			</div>
			<div class="install_item">
				<label for="db_username"><?= $entry_db_username; ?></label>
				<input id="db_username" type="text" name="db_username" value="<?= $db_username; ?>" />
			</div>
			<div class="install_item">
				<label for="db_password"><?= $entry_db_password; ?></label>
				<input id="db_password" type="text" name="db_password" value="<?= $db_password; ?>" />
			</div>
			<div class="install_item">
				<label for="db_prefix"><?= $entry_db_prefix; ?></label>
				<input id="db_prefix" type="text" name="db_prefix" value="<?= $db_prefix; ?>" size="3"/>
			</div>
			<div class="install_item">
				<input type="hidden" name="action" value="db_setup" />
				<input type="submit" class="button" value="<?= $button_submit_db; ?>" />
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">

</script>

