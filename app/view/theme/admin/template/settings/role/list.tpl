<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<div class="box">
		<div class="heading">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<div class="buttons col xs-12 md-6 md-right">
				<? if (user_can('w', 'admin/settings/role/form')) { ?>
					<a href="<?= site_url('admin/settings/role/form'); ?>" class="button">{{Add Role}}</a>
				<? } ?>
			</div>
		</div>

		<div class="section row">
			<? if (!empty($batch_action) && user_can('w', 'admin/settings/role/batch_action')) { ?>
				<div class="batch-action row right padding-bottom">
					<?= block('widget/batch_action', null, $batch_action); ?>
				</div>
			<? } ?>

			<?= block('widget/views', null, array(
				'group' => 'User Roles',
				'path'  => 'admin/settings/role/listing',
			)); ?>
		</div>
	</div>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
