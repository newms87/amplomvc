<?= $common_header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= URL_THEME_IMAGE . 'setting.png'; ?>" alt=""/> <?= _l("Order Totals"); ?></h1>

				<div class="buttons">
					<a href="<?= $insert; ?>" class="button"><?= _l("Insert"); ?></a>
				</div>
			</div>
			<div class="section">
				<div class="limits">
					<?= $limits; ?>
				</div>

				<div id="listing">
					<?= $list_view; ?>
				</div>

				<div class="pagination"><?= $pagination; ?></div>
			</div>
		</div>
	</div>

<?= $common_footer; ?>
