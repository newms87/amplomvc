<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> {{Views}}</h1>

			<div class="buttons">
				<a class="button" href="<?= site_url('admin/view/form'); ?>">{{Create View}}</a>
			</div>
		</div>
		<div class="section">
			<?= block('widget/views', null, array(
				'group'        => 'views',
				'path'         => 'admin/view/listing',
				'view_listing' => 'view_listings'
			)); ?>
		</div>
	</div>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
