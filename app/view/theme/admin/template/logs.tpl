<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<div class="box">
		<div class="heading">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>
		</div>

		<div class="section row">
			<? if (user_can('w', 'admin/logs/batch_action')) { ?>
				<div class="batch-action row right padding-bottom">
					<?= block('widget/batch_action', null, $batch_action); ?>
				</div>
			<? } ?>

			<?= block('widget/views', null, array(
				'path'  => 'admin/logs/listing',
				'group' => 'System Logs',
			)); ?>
		</div>
	</div>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
