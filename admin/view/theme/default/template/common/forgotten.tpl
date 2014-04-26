<?= _call('common/header'); ?>

<div class="section clear">
	<?= _breadcrumbs(); ?>

	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'user.png'; ?>" alt=""/> <?= _l("Forgot Your Password?"); ?></h1>

			<div class="buttons">
				<a onclick="$('#forgotten').submit();" class="button"><?= _l("Reset"); ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>

		<div class="section">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="forgotten">
				<p><?= _l("Enter the e-mail address associated with your account. Click submit to have a password reset link e-mailed to you."); ?></p>
				<table class="form">
					<tr>
						<td><?= _l("E-Mail Address:"); ?></td>
						<td><input type="text" name="email" value="<?= $email; ?>"/></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>

<?= _call('common/footer'); ?>
