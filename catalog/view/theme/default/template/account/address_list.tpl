<?= $this->call('common/header'); ?>
<?= $this->area->render('left'); ?><?= $this->area->render('right'); ?>
	<div class="content">
		<?= $this->breadcrumb->render(); ?>
		<?= $this->area->render('top'); ?>

		<h1><?= _l("Address Book"); ?></h1>

		<h2><?= _l("Address Book Entries"); ?></h2>
		<? foreach ($addresses as $result) { ?>
			<div class="section">
				<table style="width: 100%;">
					<tr>
						<td><?= $result['address']; ?></td>
						<td style="text-align: right;"><a href="<?= $result['update']; ?>"
								class="button"><?= _l("Edit"); ?></a> &nbsp; <a
								href="<?= $result['delete']; ?>" class="button"><?= _l("Delete"); ?></a></td>
					</tr>
				</table>
			</div>
		<? } ?>
		<div class="buttons">
			<div class="left"><a href="<?= $back; ?>" class="button"><?= _l("Back"); ?></a></div>
			<div class="right"><a href="<?= $insert; ?>" class="button"><?= _l("New Address"); ?></a></div>
		</div>

		<?= $this->area->render('bottom'); ?>
	</div>
<?= $this->call('common/footer'); ?>
