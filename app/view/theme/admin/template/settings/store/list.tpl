<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> {{Stores & Settings}}</h1>

			<div class="buttons">
				<a href="<?= site_url('admin/settings/store/form'); ?>" class="button">{{Add Store}}</a>
				<a href="<?= site_url('admin/settings'); ?>" class="button">{{Back}}</a>
			</div>
		</div>
		<div class="section">

			<div class="section">
				<?= block('widget/views', null, array('path' => 'admin/settings/store/listing')); ?>
			</div>
		</div>
	</div>
</div>
<?= $is_ajax ? '' : call('admin/footer'); ?>
