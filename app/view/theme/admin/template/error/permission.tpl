<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<div class="box">
		<div class="heading">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>
		</div>

		<div class="section row">
			<div class="page-title row padding-bottom">
				<h1>
					<i class="fa fa-exclamation-circle""></i>
					<div class="col auto">{{Access Not Allowed!}}</div>
				</h1>
			</div>

			<div class="messages error row">{{You do not have permission to access this page, please see the system administrator to request access.}}</div>
		</div>
	</div>
</div>
<?= $is_ajax ? '' : call('admin/footer'); ?>
