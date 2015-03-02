<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>

	<form action="<?= $action; ?>" method="post" class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/user.png'); ?>" alt=""/> {{Forgot Your Password?}}</h1>

			<div class="buttons">
				<button data-loading="{{Please Wait...}}">{{Reset}}</button>
				<a href="<?= $cancel; ?>" class="button">{{Cancel}}</a>
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
