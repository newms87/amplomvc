<?= _call('common/header'); ?>
<?= _area('left'); ?><?= _area('right'); ?>
	<div class="content">
		<?= _breadcrumbs(); ?>
		<?= _area('top'); ?>

		<h1><?= _l("Product Returns"); ?></h1>
		<? if (!empty($returns)) { ?>
			<? foreach ($returns as $return) { ?>
				<div class="return-list">
					<div class="return-id"><b><?= _l("RMA #:"); ?></b> #<?= $return['rma']; ?></div>
					<div class="return-status"><b><?= _l("Status:"); ?></b> <?= $return['status']['title']; ?></div>
					<div class="return-content">
						<div><b><?= _l("Date Added:"); ?></b> <?= $return['date_added']; ?><br/>
							<b><?= _l("Order ID:"); ?></b> <?= $return['order_id']; ?></div>
						<div><b><?= _l("Customer:"); ?></b> <?= $return['name']; ?></div>
						<div class="return-info"><a href="<?= $return['href']; ?>"><img
									src="<?= theme_url('image/info.png'); ?>" alt="<?= _l("View"); ?>"
									title="<?= _l("View"); ?>"/></a></div>
					</div>
				</div>
			<? } ?>
			<div class="pagination"><?= $pagination; ?></div>
		<? } else { ?>
			<div class="section"><?= _l("You have not made any previous returns!"); ?></div>
		<? } ?>

		<div class="buttons">
			<div class="right"><a href="<?= $continue; ?>" class="button"><?= _l("Continue"); ?></a></div>
		</div>

		<?= _area('bottom'); ?>
	</div>

<?= _call('common/footer'); ?>