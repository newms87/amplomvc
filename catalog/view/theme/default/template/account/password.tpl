<?= $this->call('common/header'); ?>
<?= $this->area->render('left'); ?><?= $this->area->render('right'); ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $this->area->render('top'); ?>

	<h1><?= _l("Change Password"); ?></h1>

	<form action="<?= $action; ?>" method="post" enctype="multipart/form-data">
		<h2><?= _l("Your Password"); ?></h2>

		<div class="section">
			<table class="form">
				<tr>
					<td class="required"> <?= _l("Password:"); ?></td>
					<td><input type="password" autocomplete="off" name="password" value="<?= $password; ?>"/>
						<? if (_l("Password must be between 4 and 20 characters!")) { ?>
							<span class="error"><?= _l("Password must be between 4 and 20 characters!"); ?></span>
						<? } ?></td>
				</tr>
				<tr>
					<td class="required"> <?= _l("Password Confirm:"); ?></td>
					<td><input type="password" autocomplete="off" name="confirm" value="<?= $confirm; ?>"/>
						<? if (_l("Password confirmation does not match password!")) { ?>
							<span class="error"><?= _l("Password confirmation does not match password!"); ?></span>
						<? } ?></td>
				</tr>
			</table>
		</div>
		<div class="buttons">
			<div class="left"><a href="<?= $back; ?>" class="button"><?= _l("Back"); ?></a></div>
			<div class="right"><input type="submit" value="<?= _l("Continue"); ?>" class="button"/></div>
		</div>
	</form>

	<?= $this->area->render('bottom'); ?>
</div>

<?= $this->call('common/footer'); ?>
