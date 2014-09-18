<?= IS_AJAX ? '' : call('admin/common/header'); ?>

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
					<div class="dashboard">
						<a href="<?= site_url('admin/dashboard/view', 'dashboard_id=' . $dashboard['dashboard_id']); ?>" class="view">
							<h2><?= $dashboard['title']; ?></h2>
						</a>
						<a class="button remove" href="<?= site_url('admin/dashboard/remove', 'dashboard_id=' . $dashboard['dashboard_id']); ?>"><?= _l("X"); ?></a>
					</div>
				<? } ?>
				<div class="dashboard">
					<a href="<?= site_url('admin/dashboard/view'); ?>" class="view add-dashboard">
						<h2><?= _l("+"); ?></h2>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('.dashboard .remove').click(function (){
		if (!confirm("<?= _l("Are you sure you want to remove this dashboard?"); ?>")) {
			return false;
		}
	});
</script>

<?= IS_AJAX ? '' : call('admin/common/footer'); ?>
