<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
	<div class="content">
		<?= $this->breadcrumb->render(); ?>
		<?= $content_top; ?>

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
							<a href="<?= $download['href']; ?>"><img src="<?= HTTP_THEME_IMAGE . 'download.png'; ?>"
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

		<?= $content_bottom; ?>
	</div>

<?= $footer; ?>
