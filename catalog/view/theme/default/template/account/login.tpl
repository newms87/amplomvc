<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
<div id="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $content_top; ?>
	
	<h1><?= $heading_title; ?></h1>
	<div class="login-content">
		<div class="left">
			<h2><?= $text_new_customer; ?></h2>
			<div class="section">
				<p><b><?= $text_register; ?></b></p>
				<p><?= $text_register_account; ?></p>
				<a href="<?= $register; ?>" class="button"><?= $button_continue; ?></a></div>
		</div>
		<div class="right">
			<h2><?= $text_returning_customer; ?></h2>
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data">
				<div class="section">
					<p><?= $text_i_am_returning_customer; ?></p>
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
	
	<?= $content_bottom; ?>
</div>
	
<?= $footer; ?>