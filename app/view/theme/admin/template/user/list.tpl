<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section user-account-list">
	<div class="box">
		<div class="heading">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<div class="buttons col xs-12 md-6 md-right">
				<? if (user_can('w', 'admin/user/form')) { ?>
					<a href="<?= site_url('admin/user/form'); ?>" class="button">{{Add User}}</a>
				<? } ?>
			</div>
		</div>

		<div class="section row">
			<? if (!empty($batch_action) && user_can('w', 'admin/user/batch_action')) { ?>
				<div class="batch-action row right padding-bottom">
					<?= block('widget/batch_action', null, $batch_action); ?>
				</div>
			<? } ?>

			<?= block('widget/views', null, array(
				'path'  => 'admin/user/listing',
				'group' => 'Users',
			)); ?>
		</div>
	</div>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
