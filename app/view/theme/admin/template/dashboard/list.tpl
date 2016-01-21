<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<div class="box">
		<div class="heading">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<div class="buttons col xs-12 md-6 md-right">
				<a href="<?= site_url('admin/dashboard/form'); ?>" class="button">{{Add Dashboard}}</a>
			</div>
		</div>

		<div class="section row">
			<div class="dashboards">
				<? foreach ($dashboards as $dashboard) { ?>
					<div class="dashboard">
						<a href="<?= site_url('admin/dashboard/view', 'dashboard_id=' . $dashboard['dashboard_id']); ?>" class="view">
							<h2><?= $dashboard['title']; ?></h2>
						</a>
						<a class="button remove" data-confirm-modal="{{Are you sure you want to remove this dashboard?}}" href="<?= site_url('admin/dashboard/remove', 'dashboard_id=' . $dashboard['dashboard_id']); ?>">{{X}}</a>
					</div>
				<? } ?>
				<div class="dashboard">
					<a href="<?= site_url('admin/dashboard/view'); ?>" class="view add-dashboard">
						<h2>{{+}}</h2>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
