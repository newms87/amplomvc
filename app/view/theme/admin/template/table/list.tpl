<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1>
				<i class="fa fa-pencil"></i>
				{{<?= $model['title']; ?> Listings}}
			</h1>
		</div>
		<div class="section">
			<?=
			block('widget/views', null, array(
				'path'  => $model['path'] . '/listing',
				'group' => slug($model['path']),
			)); ?>
		</div>
	</div>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
