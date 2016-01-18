<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section admin-index">
	<div class="box">
		<div class="row heading left">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>
		</div>

		<div class="row section overview-section padding">
			<h2>{{Amplo MVC Dashboard}}</h2>
		</div>
	</div>
</div>
</div>


<?= $is_ajax ? '' : call('admin/footer'); ?>
