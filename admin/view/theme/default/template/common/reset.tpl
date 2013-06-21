<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'user.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="$('#reset').submit();" class="button"><?= $button_save; ?></a><a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="reset">
				<p><?= $text_password; ?></p>
				<table class="form">
					<tr>
						<td><?= $entry_password; ?></td>
						<td><input type="text" autocomplete='off' name="password" value="<?= $password; ?>" />
							<? if ($error_password) { ?>
							<span class="error"><?= $error_password; ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_confirm; ?></td>
						<td><input type="text" name="confirm" value="<?= $confirm; ?>" />
							<? if ($error_confirm) { ?>
							<span class="error"><?= $error_confirm; ?></span>
							<? } ?></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
<?= $footer; ?>