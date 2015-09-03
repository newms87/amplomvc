<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> {{Sites}}</h1>

			<div class="buttons">
				<a href="<?= site_url('admin/site/form'); ?>" class="button">{{Create New Site}}</a>
			</div>
		</div>

		<div class="section">
			<?= block('widget/views', null, array(
				'path'  => 'admin/site/listing',
				'group' => 'Sites',
			)); ?>
		</div>
	</div>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
