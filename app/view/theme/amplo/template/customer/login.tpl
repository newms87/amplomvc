<?= $is_ajax ? '' : call('header'); ?>
<?= area('left'); ?>
<?= area('right'); ?>

<section id="customer-login" class="content">
	<header class="login-top row">
		<div class="wrap">
			<?= $is_ajax ? '' : breadcrumbs(); ?>

			<h1>{{Customer Sign In}}</h1>
		</div>
	</header>

	<?= area('top'); ?>

	<div class="login-page row">
		<div class="wrap">
			<div class="col xs-12 sm-6 top text-center login-col">
				<div class="login-box box">
					<h2>{{Log In}}</h2>

					<form action="<?= site_url('customer/authenticate'); ?>" class="login-form form" method="post" enctype="multipart/form-data" <?= $redirect === null ? '' : 'data-if-ajax="#customer-login"'; ?>>
						<div class="form-item">
							<input type="text" placeholder="{{email}}" name="username" value="<?= $username; ?>"/>
						</div>
						<div class="form-item">
							<input type="password" placeholder="{{password}}" name="password" value=""/>
						</div>

						<? if (!empty($medias)) { ?>
							<div class="one-click">
								<h3>{{Or Log In With...}}</h3>
								<? foreach ($medias as $media) { ?>
									<a href="<?= $media['url']; ?>" class="social-login sprite <?= $media['name'] . ' ' . $size; ?>"></a>
								<? } ?>
							</div>
						<? } ?>

						<div class="form-item submit">
							<div class="forgotten">
								<a href="<?= site_url('customer/forgotten'); ?>">{{Forgot Password?}}</a>
							</div>

							<button data-loading="{{Please Wait...}}">{{Log In}}</button>
						</div>
					</form>
				</div>
			</div>

			<div class="col xs-12 sm-6 top text-center register-col">
				<div class="register-box box">
					<h2>{{Create My Account}}</h2>

					<form action="<?= site_url('customer/register'); ?>" class="register-form form" method="post" enctype="multipart/form-data" <?= $redirect === null ? '' : 'data-if-ajax="#customer-login"'; ?>>
						<div class="form-item">
							<input type="text" placeholder="{{name}}" name="name" value="<?= _post('name'); ?>"/>
						</div>
						<div class="form-item">
							<input type="text" placeholder="{{email}}" name="email" value="<?= _post('email'); ?>"/>
						</div>
						<div class="form-item">
							<input type="password" placeholder="{{password}}" name="password" value=""/>
						</div>

						<div class="form-item submit">
							<button data-loading="{{Please Wait...}}">{{Create Account}}</button>
						</div>

						<? if (!empty($medias)) { ?>
							<div class="one-click">
								<h3>{{Or Sign Up With...}}</h3>
								<? foreach ($medias as $media) { ?>
									<a href="<?= $media['url']; ?>" class="social-login sprite <?= $media['name'] . ' ' . $size; ?>"></a>
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
	$('#customer-login .login-page .wrap').ac_msg('error', <?= json_encode($this->message->fetch('error')); ?>);
</script>

<?= $is_ajax ? '' : call('footer'); ?>
