<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>

<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $content_top; ?>

	<h1><?= _l("Request a New Pasword"); ?></h1>

	<form action="<?= $save; ?>" method="post" enctype="multipart/form-data">
		<h3><?= _l("Enter your Email address below to request a new password for your account."); ?></h3>

		<div class="section">
			<table class="form">
				<tr>
					<td><?= _l("Your Email:"); ?></td>
					<td><input type="text" name="email" value=""/></td>
				</tr>
			</table>
		</div>

		<div class="buttons">
			<div class="left">
				<a href="<?= $back; ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
			<div class="right">
				<input type="submit" value="<?= _l("Request Password Reset"); ?>" class="button"/>
			</div>
		</div>
	</form>

	<?= $content_bottom; ?>
</div>

<?= $footer; ?>
