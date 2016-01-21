<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<form action="<?= site_url('admin/user/reset', 'code=' . $code); ?>" method="post" enctype="multipart/form-data" class="box">
		<div class="heading">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<div class="buttons col xs-12 md-6 md-right">
				<button data-loading="{{Please Wait...}}">{{Reset Password}}</button>
			</div>
		</div>

		<div class="section row">

			<h2>{{Enter your new Password:}}</h2>
			<br />

			<div class="form-item">
				<input type="password" autocomplete="off" name="password" placeholder="{{Enter New Password}}"/>
				<br />
				<br />
				<input type="password" autocomplete="off" name="confirm" placeholder="{{Confirm Password}}"/>
			</div>
		</div>
	</form>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
