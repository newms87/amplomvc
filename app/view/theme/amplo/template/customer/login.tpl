<?= $is_ajax ? '' : call('header'); ?>
<?= area('left'); ?>
<?= area('right'); ?>

<section id="customer-login" class="login-page content">
	<header class="login-top row">
		<div class="wrap">
			<div class="breadcrumbs">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<h1>{{Customer Account}}</h1>
		</div>
	</header>

	<?= area('top'); ?>

	<div class="login-page row <?= isset($_GET['register']) ? 'registration' : ''; ?>">
		<div class="wrap">
			<div class="col xs-12 lg-6 top text-center login-col account-box">
				<div class="login-box box">
					<h2>{{Log In}}</h2>

					<form action="<?= site_url('customer/authenticate'); ?>" class="login-form form" method="post" enctype="multipart/form-data" <?= $redirect === null ? '' : 'data-if-ajax="#customer-login"'; ?>>
						<div class="form-item">
							<input type="text" autocomplete="username" placeholder="{{email}}" name="username" value="<?= $username; ?>"/>
						</div>
						<div class="form-item">
							<input type="password" autocomplete="current-password" placeholder="{{password}}" name="password" value=""/>
						</div>

						<? if (!empty($medias)) { ?>
							<div class="one-click">
								<h3>{{Or Log In With...}}</h3>
								<? foreach ($medias as $media) { ?>
									<a href="<?= $media['url']; ?>" class="social-login fa <?= 'fa-' . $media['name'] . ' ' . $size; ?>"></a>
								<? } ?>
							</div>
						<? } ?>

						<div class="form-item submit buttons">
							<button data-loading="{{Please Wait...}}">{{Log In}}</button>

							<div class="buttons">
								<div class="col xs-6 left forgotten">
									<a href="<?= site_url('customer/forgotten'); ?>">{{Forgot Password?}}</a>
								</div>
								<div class="switch col xs-6 right">
									<a class="show-register">{{Create Account}}</a>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>

			<div class="col xs-12 lg-6 top text-center register-col account-box">
				<div class="register-box box">
					<h2>{{Create My Account}}</h2>

					<form action="<?= site_url('customer/register'); ?>" class="register-form form" method="post" enctype="multipart/form-data" <?= $redirect === null ? '' : 'data-if-ajax="#customer-login"'; ?>>
						<div class="form-item">
							<input type="text" autocomplete="name" placeholder="{{name}}" name="name" value="<?= _post('name'); ?>"/>
						</div>
						<div class="form-item">
							<input type="text" autocomplete="username" placeholder="{{email}}" name="email" value="<?= _post('email'); ?>"/>
						</div>
						<div class="form-item">
							<input type="password" autocomplete="new-password" placeholder="{{password}}" autocomplete="off" name="password" value=""/>
						</div>

						<? if ($terms_id = option('terms_agreement_page_id')) { ?>
							<div class="form-item terms-agreement">
								<label class="checkbox" for="terms-agreement">
									<input type="checkbox" name="agree" value="1" id="terms-agreement" <?= _post('agree') ? 'checked' : ''; ?> />

									<div class="label">
										{{I have read and agree to the
										<a href="<?= site_url('page', 'page_id=' . $terms_id); ?>">Terms &amp; Conditions</a>}}
									</div>
								</label>
							</div>
						<? } ?>

						<div class="form-item submit buttons">
							<button data-loading="{{Please Wait...}}">{{Create Account}}</button>

							<div class="buttons">
								<div class="switch">
									<a class="show-login">{{Already have an account?}}</a>
								</div>
							</div>
						</div>

						<? if (!empty($medias)) { ?>
							<div class="one-click">
								<h3>{{Or Sign Up With...}}</h3>
								<? foreach ($medias as $media) { ?>
									<a href="<?= $media['url']; ?>" class="social-login fa <?= 'fa-' . $media['name'] . ' ' . $size; ?>"></a>
								<? } ?>
							</div>
						<? } ?>
					</form>
				</div>
			</div>
		</div>
	</div>

	<?= area('bottom'); ?>
</section>

<script type="text/javascript">
	$('.login-page .switch a').click(function () {
		$('.login-page').toggleClass('registration', $(this).is('.show-register'));
	});
</script>

<?= $is_ajax ? '' : call('footer'); ?>
