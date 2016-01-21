<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<div class="box">
		<div class="heading">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<div class="buttons col xs-12 md-6 md-right">
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
