<?= _call('common/header'); ?>

<div class="section clear">
	<?= _breadcrumbs(); ?>

	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'user.png'; ?>" alt=""/> <?= _l("Reset Your Password"); ?></h1>

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
						<td><input type="password" autocomplete="off" name="password" value=""/></td>
					</tr>
					<tr>
						<td><?= _l("Password Confirmation:"); ?></td>
						<td><input type="password" name="confirm" value=""/></td>
					</tr>
				</table>

				<input type="submit" class="button" value="<?= _l("Change Password"); ?>"/>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= _call('common/footer'); ?>
