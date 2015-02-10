<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/error.png'); ?>" alt=""/> {{Permission Denied!}}</h1>
		</div>
		<div class="section">
			<div
				style="border: 1px solid #DDDDDD; background: #F7F7F7; text-align: center; padding: 15px;">{{You do not have permission to access this page, please refer to your system administrator.}}
			</div>
		</div>
	</div>
</div>
<?= $is_ajax ? '' : call('admin/footer'); ?>
