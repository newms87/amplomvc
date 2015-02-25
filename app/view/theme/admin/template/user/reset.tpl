<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>

	<form action="<?= site_url('admin/user/reset', 'code=' . $code); ?>" method="post" enctype="multipart/form-data" class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/user.png'); ?>" alt=""/> {{Reset Your Password}}</h1>

			<div class="buttons">
				<button data-loading="{{Please Wait...}}">{{Reset Password}}</button>
				<a href="<?= site_url('admin/user/login'); ?>" class="button">{{Cancel}}</a>
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
