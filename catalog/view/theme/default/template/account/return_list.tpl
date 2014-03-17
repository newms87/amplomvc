<?= $common_header; ?>
<?= $area_left; ?><?= $area_right; ?>
	<div class="content">
		<?= $this->breadcrumb->render(); ?>
		<?= $area_top; ?>

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
									src="<?= URL_THEME_IMAGE . 'info.png'; ?>" alt="<?= _l("View"); ?>"
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

		<?= $area_bottom; ?>
	</div>

<?= $common_footer; ?>
