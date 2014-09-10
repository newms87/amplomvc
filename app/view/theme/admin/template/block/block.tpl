<?= IS_AJAX ? '' : call('admin/common/header'); ?>
<div class="section">
	<?= breadcrumbs(); ?>

	<form id="form" class="box" action="<?= $save; ?>" method="post" enctype="multipart/form-data">
		<div class="heading">
			<h1><img src="<?= theme_url('image/module.png'); ?>" alt=""/> <?= _l("Blocks"); ?></h1>

			<div class="buttons">
				<button class="save button"><?= _l("Save"); ?></button>
				<a href="<?= site_url('admin/block'); ?>" class="cancel button"><?= _l("Cancel"); ?></a>
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

	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= IS_AJAX ? '' : call('admin/common/footer'); ?>
