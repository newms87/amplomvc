<?= $common_header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>

	<form id="form" class="box" action="<?= $save; ?>" method="post" enctype="multipart/form-data">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'module.png'; ?>" alt=""/> <?= _l("Blocks"); ?></h1>

			<div class="buttons">
				<button class="save button"><?= _l("Save"); ?></button>
				<a href="<?= $cancel; ?>" class="cancel button"><?= _l("Cancel"); ?></a>
			</div>
		</div>

		<div class="section">
			<div id="tabs" class="htabs">
				<a href="#tab-settings"><?= _l("Settings"); ?></a>
				<a href="#tab-instances"><?= _l("Instances"); ?></a>
			</div>

			<div id="tab-settings">
				<?= $block_settings; ?>
			</div>

			<div id="tab-instances">
				<?= $block_instances; ?>
			</div>

		</div>
	</form>
</div>

<script type="text/javascript">
	$('#tabs a').tabs();
</script>

<?= $this->builder->js('errors', $errors); ?>

<?= $common_footer; ?>
