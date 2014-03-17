<?= $common_header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'setting.png'; ?>" alt=""/> <?= _l("URL Aliases"); ?></h1>

			<div class="batch_actions">
				<?= $this->builder->batchAction('#listing [name="selected[]"]', $batch_actions, $batch_update); ?>
			</div>
			<div class="buttons">
				<a href="<?= $insert; ?>" class="button"><?= _l("Insert"); ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
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
