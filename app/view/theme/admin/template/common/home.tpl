<?= IS_AJAX ? '' : call('admin/common/header'); ?>

<div class="section">
	<?= breadcrumbs(); ?>

	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/home.png'); ?>" alt=""/> <?= _l("Dashboard"); ?></h1>
		</div>
		<div class="section">
			<div class="overview">
				<div class="dashboard-heading"><?= _l("Overview"); ?></div>
					<h2><?= _l("Welcome to Amplo MVC"); ?></h2>
				</div>
			</div>
		</div>
	</div>
</div>


<?= IS_AJAX ? '' : call('admin/common/footer'); ?>
