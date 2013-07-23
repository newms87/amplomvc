<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
<div id="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $content_top; ?>
	
	<h1><?= $heading_title; ?></h1>
	<form action="<?= $action; ?>" method="post" enctype="multipart/form-data">
		<h2><?= $text_your_details; ?></h2>
		<div class="section">
			<table class="form">
				<tr>
					<td class="required"> <?= $entry_firstname; ?></td>
					<td><input type="text" name="firstname" value="<?= $firstname; ?>" />
						<? if ($error_firstname) { ?>
						<span class="error"><?= $error_firstname; ?></span>
						<? } ?></td>
				</tr>
				<tr>
					<td class="required"> <?= $entry_lastname; ?></td>
					<td><input type="text" name="lastname" value="<?= $lastname; ?>" />
						<? if ($error_lastname) { ?>
						<span class="error"><?= $error_lastname; ?></span>
						<? } ?></td>
				</tr>
				<tr>
					<td class="required"> <?= $entry_email; ?></td>
					<td><input type="text" name="email" value="<?= $email; ?>" />
						<? if ($error_email) { ?>
						<span class="error"><?= $error_email; ?></span>
						<? } ?></td>
				</tr>
			</table>
		</div>
		<div class="buttons">
			<div class="left"><a href="<?= $back; ?>" class="button"><?= $button_back; ?></a></div>
			<div class="right">
				<input type="submit" value="<?= $button_continue; ?>" class="button" />
			</div>
		</div>
	</form>
	
	<?= $content_bottom; ?>
</div>
<?= $footer; ?>