<?= IS_AJAX ? '' : call('admin/header'); ?>

	<div class="section">
		<?= IS_AJAX ? '' : breadcrumbs(); ?>

		<div class="box">
			<div class="heading">
				<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> <?= _l("User Roles"); ?></h1>

				<? if (!empty($batch_action)) { ?>
					<div class="batch_actions">
						<?= block('widget/batch_action', null, $batch_action); ?>
					</div>
				<? } ?>

				<? if (user_can('w', 'admin/user/role/form')) { ?>
					<div class="buttons">
						<a href="<?= site_url('admin/setting/role/form'); ?>" class="button"><?= _l("Insert"); ?></a>
					</div>
				<? } ?>
			</div>

			<div class="section">
				<?= block('widget/views', null, array('group' => 'user_roles', 'view_listing_id' => $view_listing_id)); ?>
			</div>
		</div>
	</div>

<?= IS_AJAX ? '' : call('admin/footer'); ?>
