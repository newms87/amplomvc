<?= call('admin/common/header'); ?>

<div class="section">
	<?= breadcrumbs(); ?>

	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> <?= _l("Dashboards"); ?></h1>

			<div class="buttons">
				<a href="<?= site_url('admin/dashboard/form'); ?>" class="button"><?= _l("Add Dashboard"); ?></a>
			</div>
		</div>
		<div class="section">
			<div class="dashboards">
			<? foreach ($dashboards as $dashboard) { ?>
				<a href="<?= site_url('admin/dashboard/view', 'dashboard_id=' . $dashboard['dashboard_id']); ?>" class="dashboard">
					<h2><?= $dashboard['name']; ?></h2>
				</a>
			<? } ?>
			</div>
		</div>
	</div>
</div>

<?= call('admin/common/footer'); ?>
