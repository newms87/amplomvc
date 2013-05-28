<?= $header; ?>
<? if ($success) { ?>
<div class="message_box success"><?= $success; ?></div>
<? } ?>
<? if ($error_warning) { ?>
<div class="message_box warning"><?= $error_warning; ?></div>
<? } ?>
<?= $column_left; ?><?= $column_right; ?>
<div id="content"><?= $content_top; ?>
	<?= $this->builder->display_breadcrumbs();?>
	<h1><?= $heading_title; ?></h1>
	<?= $text_description; ?>
	<div class="login-content">
		<div class="left">
			<h2><?= $text_new_affiliate; ?></h2>
			<div class="content"><?= $text_register_account; ?> <a href="<?= $register; ?>" class="button"><?= $button_continue; ?></a></div>
		</div>
		<div class="right">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data">
				<h2><?= $text_returning_affiliate; ?></h2>
				<div class="content">
					<p><?= $text_i_am_returning_affiliate; ?></p>
					<b><?= $entry_email; ?></b><br />
					<input type="text" name="email" value="" />
					<br />
					<br />
					<b><?= $entry_password; ?></b><br />
					<input type="password" autocomplete='off' name="password" value="" />
					<br />
					<a href="<?= $forgotten; ?>"><?= $text_forgotten; ?></a><br />
					<br />
					<input type="submit" value="<?= $button_login; ?>" class="button" />
					<? if ($redirect) { ?>
					<input type="hidden" name="redirect" value="<?= $redirect; ?>" />
					<? } ?>
				</div>
			</form>
		</div>
	</div>
	<?= $content_bottom; ?></div>
<?= $footer; ?>