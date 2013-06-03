<?= $header; ?>
<? if ($error_warning) { ?>
<div class="message_box warning"><?= $error_warning; ?></div>
<? } ?>
<?= $column_left; ?><?= $column_right; ?>
<div class="content"><?= $content_top; ?>
	<?= $this->builder->display_breadcrumbs(); ?>
	<h1><?= $heading_title; ?></h1>
	<form action="<?= $action; ?>" method="post" enctype="multipart/form-data">
		<h2><?= $text_your_details; ?></h2>
		<div class="content">
			<table class="form">
				<tr>
					<td><span class="required"></span> <?= $entry_firstname; ?></td>
					<td><input type="text" name="firstname" value="<?= $firstname; ?>" />
						<? if ($error_firstname) { ?>
						<span class="error"><?= $error_firstname; ?></span>
						<? } ?></td>
				</tr>
				<tr>
					<td><span class="required"></span> <?= $entry_lastname; ?></td>
					<td><input type="text" name="lastname" value="<?= $lastname; ?>" />
						<? if ($error_lastname) { ?>
						<span class="error"><?= $error_lastname; ?></span>
						<? } ?></td>
				</tr>
				<tr>
					<td><span class="required"></span> <?= $entry_email; ?></td>
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
	<?= $content_bottom; ?></div>
<?= $footer; ?>