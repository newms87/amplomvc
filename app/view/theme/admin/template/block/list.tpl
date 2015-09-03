<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> {{Blocks}}</h1>

			<div class="buttons">
				<a href="<?= site_url('admin/block/add-block'); ?>" class="button">{{Insert}}</a>
			</div>
		</div>
		<div class="section">
			<?= block('widget/views', null, array(
				'path'  => 'admin/block/listing',
				'group' => 'Blocks',
			)); ?>
		</div>
	</div>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
