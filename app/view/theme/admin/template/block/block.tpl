<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<form id="form" class="box" action="<?= $save; ?>" method="post" enctype="multipart/form-data">
		<div class="heading">
			<div class="breadcrumbs">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<h1><img src="<?= theme_url('image/module.png'); ?>" alt=""/> {{Blocks}}</h1>

			<div class="buttons">
				<button class="save button">{{Save}}</button>
				<a href="<?= site_url('admin/block'); ?>" class="cancel button">{{Cancel}}</a>
			</div>
		</div>

		<div class="section">
			<div id="tabs" class="htabs">
				<a href="#tab-settings">{{Settings}}</a>
				<a href="#tab-instances">{{Instances}}</a>
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
	$('#tabs a').ampTabs();


</script>

<?= $is_ajax ? '' : call('admin/footer'); ?>
