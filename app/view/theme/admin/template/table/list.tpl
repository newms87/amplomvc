<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1>
				<i class="fa fa-pencil"></i>
				{{<?= $model['title']; ?> Listings}}
			</h1>

			<? if (user_can('w', $model['path'] . '/batch_action')) { ?>
				<div class="batch-action">
					<?= block('widget/batch_action', null, $batch_action); ?>
				</div>
			<? } ?>

			<? if (user_can('r', $model['path'] . '/form')) { ?>
				<div class="buttons">
					<a href="<?= site_url($model['path'] . '/form'); ?>" class="button">{{New <?= $model['title']; ?>}}</a>
				</div>
			<? } ?>
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
