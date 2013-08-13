<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
<div id="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $content_top; ?>

	<h1><?= $head_title; ?></h1>
	<form action="<?= $action; ?>" method="post" enctype="multipart/form-data">
		<h2><?= $text_password; ?></h2>
		<div class="section">
			<table class="form">
				<tr>
					<td class="required"> <?= $entry_password; ?></td>
					<td><input type="password" autocomplete='off' name="password" value="<?= $password; ?>" />
						<? if ($error_password) { ?>
						<span class="error"><?= $error_password; ?></span>
						<? } ?></td>
				</tr>
				<tr>
					<td class="required"> <?= $entry_confirm; ?></td>
					<td><input type="password" autocomplete='off' name="confirm" value="<?= $confirm; ?>" />
						<? if ($error_confirm) { ?>
						<span class="error"><?= $error_confirm; ?></span>
						<? } ?></td>
				</tr>
			</table>
		</div>
		<div class="buttons">
			<div class="left"><a href="<?= $back; ?>" class="button"><?= $button_back; ?></a></div>
			<div class="right"><input type="submit" value="<?= $button_continue; ?>" class="button" /></div>
		</div>
	</form>
	
	<?= $content_bottom; ?>
</div>

<?= $footer; ?>