<?= _call('common/header'); ?>
<div class="section">
	<?= _breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'setting.png'; ?>" alt=""/> <?= _l("Products"); ?></h1>

			<div class="batch_actions">
				<?= _block('widget/batch_action', null, $batch_action); ?>
			</div>
			<div class="buttons">
				<? if (!empty($product_classes)) { ?>
					<div class="insert_classes">
						<? foreach ($product_classes as $product_class) { ?>
							<a href="<?= $product_class['insert']; ?>" class="button"><?= _l("Insert") . ' ' . $product_class['name']; ?></a>
						<? } ?>
					</div>
				<? } else { ?>
					<a href="<?= $insert; ?>" class="button"><?= _l("Insert"); ?></a>
				<? } ?>
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

<?= _call('common/footer'); ?>
