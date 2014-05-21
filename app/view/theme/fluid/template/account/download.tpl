<?= call('common/header'); ?>
<?= area('left'); ?><?= area('right'); ?>
	<div class="content">
		<?= breadcrumbs(); ?>
		<?= area('top'); ?>

		<h1><?= _l("Account Downloads"); ?></h1>
		<? foreach ($downloads as $download) { ?>
			<div class="download-list">
				<div class="download-id"><b><?= _l("Order ID:"); ?></b> <?= $download['order_id']; ?></div>
				<div class="download-status"><b><?= _l("Size:"); ?></b> <?= $download['size']; ?></div>
				<div class="download-content">
					<div><b><?= _l("Name:"); ?></b> <?= $download['name']; ?><br/>
						<b><?= _l("Date Added:"); ?></b> <?= $download['date_added']; ?></div>
					<div><b><?= _l("Remaining:"); ?></b> <?= $download['remaining']; ?></div>
					<div class="download-info">
						<? if ($download['remaining'] > 0) { ?>
							<a href="<?= $download['href']; ?>"><img src="<?= theme_url('image/download.png'); ?>"
									alt="<?= _l("Download"); ?>"
									title="<?= _l("Download"); ?>"/></a>
						<? } ?>
					</div>
				</div>
			</div>
		<? } ?>
		<div class="pagination"><?= $pagination; ?></div>
		<div class="buttons">
			<div class="right"><a href="<?= $continue; ?>" class="button"><?= _l("Continue"); ?></a></div>
		</div>

		<?= area('bottom'); ?>
	</div>

<?= call('common/footer'); ?>
