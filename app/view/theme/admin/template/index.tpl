<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section admin-index">
	<?= $is_ajax ? '' : breadcrumbs(); ?>

	<div class="box">
		<div class="row heading left">
			<h1><img src="<?= theme_url('image/home.png'); ?>" alt=""/> {{Amplo Dashboard}}</h1>
		</div>

		<div class="row section overview-section padding">
			<h2>{{Welcome to Amplo MVC!}}</h2>
		</div>
	</div>
</div>
</div>


<?= $is_ajax ? '' : call('admin/footer'); ?>
