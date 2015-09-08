<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section user-account-list">
	<?= $is_ajax ? '' : breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> {{Users}}</h1>

			<? if (!empty($batch_action) && user_can('w', 'admin/user/batch_action')) { ?>
				<div class="batch_actions">
					<?= block('widget/batch_action', null, $batch_action); ?>
				</div>
			<? } ?>

			<? if (user_can('w', 'admin/user/form')) { ?>
				<div class="buttons">
					<a href="<?= site_url('admin/user/form'); ?>" class="button">{{Add User}}</a>
				</div>
			<? } ?>
		</div>
		<div class="section">
			<?= block('widget/views', null, array(
				'path'  => 'admin/user/listing',
				'group' => 'Users',
			)); ?>
		</div>
	</div>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
