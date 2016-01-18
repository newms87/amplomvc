<?= $is_ajax ? '' : call('header'); ?>
<?= area('left'); ?>
<?= area('right'); ?>

<section class="login-page reset-password content">
	<header class="row top-row">
		<div class="wrap">
			<div class="breadcrumbs">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<h1>{{Reset Your Password}}</h1>
		</div>
	</header>

	<?= area('top'); ?>

	<div class="row forgotten-content">
		<div class="wrap">
			<div class="col xs-12 md-10 lg-5 xl-4">
				<form action="<?= site_url('customer/reset_password', 'code=' . $code); ?>" method="post" class="form full-width" enctype="multipart/form-data">
					<p>{{Enter your Email address below to request a new password for your account.}}</p>
					<br/>

					<div class="form-item">
						<input type="password" autocomplete="off" name="password" placeholder="{{Enter New Password}}" value=""/>
					</div>

					<div class="form-item">
						<input type="password" name="confirm" placeholder="{{Confirm Password}}" value=""/>
					</div>

					<div class="buttons submit">
						<button data-loading="{{Changing...}}">{{Change Password}}</button>

						<div class="cancel buttons">
							<a href="<?= site_url('customer/login'); ?>">{{Cancel}}</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<?= area('bottom'); ?>
</section>

<?= $is_ajax ? '' : call('footer'); ?>
