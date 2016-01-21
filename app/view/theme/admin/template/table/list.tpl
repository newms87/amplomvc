<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<div class="box">
		<div class="heading">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<div class="buttons col xs-12 md-6 md-right">
				<? if (user_can('r', $model['path'] . '/form')) { ?>
					<a href="<?= site_url($model['path'] . '/form'); ?>" class="button">{{New <?= $model['title']; ?>}}</a>
				<? } ?>
			</div>
		</div>

		<div class="section row">
			<? if (user_can('w', $model['path'] . '/batch_action')) { ?>
				<div class="batch-action row right padding-bottom">
					<?= block('widget/batch_action', null, $batch_action); ?>
				</div>
			<? } ?>

			<?=
			block('widget/views', null, array(
				'path'  => $model['path'] . '/listing',
				'group' => slug($model['path']),
			)); ?>
		</div>
	</div>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
