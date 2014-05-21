<?= call('common/header'); ?>
<?= area('left'); ?><?= area('right'); ?>
	<div class="content">
		<?= breadcrumbs(); ?>
		<?= area('top'); ?>

		<h1><?= _l("My Account Information"); ?></h1>

		<form action="<?= $action; ?>" method="post" enctype="multipart/form-data">
			<h2><?= _l("Your Personal Details"); ?></h2>

			<div class="section">
				<table class="form">
					<tr>
						<td class="required"> <?= _l("First Name:"); ?></td>
						<td><input type="text" name="firstname" value="<?= $firstname; ?>"/>
							<? if (_l("First Name must be between 1 and 32 characters!")) { ?>
								<span class="error"><?= _l("First Name must be between 1 and 32 characters!"); ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td class="required"> <?= _l("Last Name:"); ?></td>
						<td><input type="text" name="lastname" value="<?= $lastname; ?>"/>
							<? if (_l("Last Name must be between 1 and 32 characters!")) { ?>
								<span class="error"><?= _l("Last Name must be between 1 and 32 characters!"); ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td class="required"> <?= _l("E-Mail:"); ?></td>
						<td><input type="text" name="email" value="<?= $email; ?>"/>
							<? if (_l("E-Mail Address does not appear to be valid!")) { ?>
								<span class="error"><?= _l("E-Mail Address does not appear to be valid!"); ?></span>
							<? } ?></td>
					</tr>
				</table>
			</div>
			<div class="buttons">
				<div class="left"><a href="<?= $back; ?>" class="button"><?= _l("Back"); ?></a></div>
				<div class="right">
					<input type="submit" value="<?= _l("Continue"); ?>" class="button"/>
				</div>
			</div>
		</form>

		<?= area('bottom'); ?>
	</div>
<?= call('common/footer'); ?>
