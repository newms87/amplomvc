<?= $is_ajax ? '' : call('admin/header'); ?>
	<div class="section">
		<?= $is_ajax ? '' : breadcrumbs(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> {{Users}}</h1>

				<? if (user_can('w', 'admin/user/batch_action')) { ?>
					<div class="batch_actions">
						<?= block('widget/batch_action', null, $batch_action); ?>
					</div>
					<div class="buttons">
						<a href="<?= site_url('admin/user/form'); ?>" class="button">{{Add User}}</a>
					</div>
				<? } ?>
			</div>
			<div class="section">
				<?= $listing; ?>
			</div>
		</div>
	</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
