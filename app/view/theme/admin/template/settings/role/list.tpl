<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>

	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> {{User Roles}}</h1>

			<? if (user_can('w', 'admin/user/role/form')) { ?>
				<? if (!empty($batch_action)) { ?>
					<div class="batch_actions">
						<?= block('widget/batch_action', null, $batch_action); ?>
					</div>
				<? } ?>

				<div class="buttons">
					<a href="<?= site_url('admin/settings/role/form'); ?>" class="button">{{Add Role}}</a>
				</div>
			<? } ?>
		</div>

		<div class="section">
			<?= block('widget/views', null, array(
				'group' => 'User Roles',
				'path'  => 'admin/settings/role/listing',
			)); ?>
		</div>
	</div>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
