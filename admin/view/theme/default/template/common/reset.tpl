<?= $header; ?>

<div class="section clear">
	<?= $this->breadcrumb->render(); ?>

	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'user.png'; ?>" alt=""/> <?= _l("Reset Your Password"); ?></h1>

			<div class="buttons">
				<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>

		<div class="section">
			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="reset">
				<h2><?= _l("Enter your new Password:"); ?></h2>
				<table class="form">
					<tr>
						<td><?= _l("Password:"); ?></td>
						<td><input type="password" autocomplete='off' name="password" value="<?= $password; ?>"/></td>
					</tr>
					<tr>
						<td><?= _l("Password Confirmation:"); ?></td>
						<td><input type="password" name="confirm" value="<?= $confirm; ?>"/></td>
					</tr>
				</table>

				<input type="submit" class="button" value="<?= _l("Change Password"); ?>" />
			</form>
		</div>
	</div>
</div>

<?= $this->builder->js('errors', $errors); ?>

<?= $footer; ?>
