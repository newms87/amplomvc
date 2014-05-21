<?= call('admin/common/header'); ?>

<section id="admin-login" class="content">
	<header class="row top-row">
		<div class="wrap">
			<h1><img src="<?= theme_url('image/lockscreen.png'); ?>" alt=""/> <?= _l("Admin Login"); ?></h1>
		</div>
	</header>

	<div class="content-row row">
		<div class="wrap">
			<form action="<?= $action ?>" method="post" enctype="multipart/form-data" class="form">
				<div class="form-item username">
					<input type="text" name="username" placeholder="<?= _l("Username / Email"); ?>" value="<?= $username; ?>"/>
				</div>
				<div class="form-item password">
					<input type="password" name="password" placeholder="<?= _l("Password"); ?>" value=""/>
					<br/>
					<a href="<?= site_url('common/forgotten'); ?>" class="forgotten-link"><?= _l("Forgot your Password?"); ?></a>
				</div>
				<div class="form-item submit">
					<button><?= _l("Login"); ?></button>
				</div>
			</form>

			<div class="help">
				<?= _l("Not sure how you got here? Please"); ?>
				<a href="<?= store_url(1); ?>"><br/>
					<?= _l("return to the shop!"); ?></a>
			</div>
		</div>
	</div>
</section>

<?= call('admin/common/footer'); ?>
