<?= IS_AJAX ? '' : call('admin/header'); ?>

<div class="section clear">
	<?= IS_AJAX ? '' : breadcrumbs(); ?>

	<div class="box">
		<form action="<?= $action; ?>" method="post" id="forgotten">
		<div class="heading">
			<h1><img src="<?= theme_url('image/user.png'); ?>" alt=""/> <?= _l("Forgot Your Password?"); ?></h1>

			<div class="buttons">
				<button><?= _l("Reset"); ?></button>
				<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>

		<div class="section">
				<p><?= _l("Enter the e-mail address associated with your account. Click submit to have a password reset link e-mailed to you."); ?></p>
				<table class="form">
					<tr>
						<td><?= _l("E-Mail Address:"); ?></td>
						<td><input type="text" name="email" value="<?= $email; ?>"/></td>
					</tr>
				</table>
		</div>
		</form>
	</div>
</div>

<?= IS_AJAX ? '' : call('admin/footer'); ?>
