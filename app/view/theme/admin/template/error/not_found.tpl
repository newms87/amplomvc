<?= $is_ajax ? '' : call('admin/header'); ?>
	<div class="section">
		<?= $is_ajax ? '' : breadcrumbs(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= theme_url('image/error.png'); ?>" alt=""/> {{Page Not Found!}}</h1>
			</div>
			<div class="section">
				<div
					style="border: 1px solid #DDDDDD; background: #F7F7F7; text-align: center; padding: 15px;">{{The page you are looking for could not be found! Please contact your administrator if the problem persists.}}</div>
			</div>
		</div>
	</div>
<?= $is_ajax ? '' : call('admin/footer'); ?>
