<?= $header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'setting.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="batch_actions">
					<?= $this->builder->batch_action('#listing [name="selected[]"]', $batch_actions, $batch_update); ?>
				</div>
				<div class="buttons">
					<? if (!empty($product_classes)) { ?>
						<div class="insert_classes">
							<? foreach ($product_classes as $product_class) { ?>
								<a href="<?= $product_class['insert']; ?>" class="button"><?= $button_insert . ' ' . $product_class['name']; ?></a>
							<? } ?>
						</div>
					<? } else { ?>
						<a href="<?= $insert; ?>" class="button"><?= $button_insert; ?></a>
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

<?= $footer; ?>