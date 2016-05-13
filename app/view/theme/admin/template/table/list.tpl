<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<div class="box">
		<div class="row heading left">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<div class="buttons col xs-12 md-6 md-right">
				<? if (!empty($model['form_path']) && user_can('r', $model['form_path'])) { ?>
					<a href="<?= site_url($model['form_path']); ?>" class="button">{{New <?= $model['title']; ?>}}</a>
				<? } ?>
			</div>
		</div>

		<div class="section row">
			<? if (!empty($model['batch_action_path']) && !empty($batch_action) && user_can('w', $model['batch_action_path'])) { ?>
				<div class="batch-action row right padding-bottom">
					<?= block('widget/batch_action', null, $batch_action); ?>
				</div>
			<? } ?>

			<?=
			block('widget/views', null, array(
				'path'  => $model['listing_path'],
				'group' => $model['listing_group'],
			)); ?>
		</div>
	</div>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
