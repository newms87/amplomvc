<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<form action="<?= $action; ?>" method="post" class="box">
		<div class="heading">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<div class="buttons col xs-12 md-6 md-right">
				<button data-loading="{{Please Wait...}}">{{Reset}}</button>
			</div>
		</div>

		<div class="section">
			<h2>{{Enter the e-mail address associated with your account. Click submit to have a password reset link e-mailed to you.}}</h2>

			<div class="form-item">
				<input type="text" name="email" value="<?= $email; ?>" placeholder="{{Enter Email}}"/>
			</div>
		</div>
	</form>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
