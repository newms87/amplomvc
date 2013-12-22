<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
	<div class="content">
		<?= $this->breadcrumb->render(); ?>
		<?= $content_top; ?>

		<h1><?= $head_title; ?></h1>
		<?= $text_description; ?>
		<div class="login_content">
			<div class="left">
				<h2><?= $text_new_affiliate; ?></h2>

				<div class="section"><?= $text_register_account; ?> <a href="<?= $register; ?>"
				                                                       class="button"><?= $button_continue; ?></a></div>
			</div>
			<div class="right">
				<form action="<?= $action; ?>" method="post" enctype="multipart/form-data">
					<h2><?= $text_returning_affiliate; ?></h2>

					<div class="section">
						<p><?= $text_i_am_returning_affiliate; ?></p>
						<b><?= $entry_email; ?></b><br/>
						<input type="text" name="email" value=""/>
						<br/>
						<br/>
						<b><?= $entry_password; ?></b><br/>
						<input type="password" autocomplete="off" name="password" value=""/>
						<br/>
						<a href="<?= $forgotten; ?>"><?= $text_forgotten; ?></a><br/>
						<br/>
						<input type="submit" value="<?= $button_login; ?>" class="button"/>
						<? if ($redirect) { ?>
							<input type="hidden" name="redirect" value="<?= $redirect; ?>"/>
						<? } ?>
					</div>
				</form>
			</div>
		</div>

		<?= $content_bottom; ?>
	</div>

<?= $footer; ?>
