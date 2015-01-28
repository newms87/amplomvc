<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>

	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/home.png'); ?>" alt=""/> {{Dashboard}}</h1>
		</div>
		<div class="section">
			<div class="overview">
				<div class="dashboard-heading">{{Overview}}</div>
					<h2>{{Welcome to Amplo MVC}}</h2>
				</div>
			</div>
		</div>
	</div>
</div>


<?= $is_ajax ? '' : call('admin/footer'); ?>
