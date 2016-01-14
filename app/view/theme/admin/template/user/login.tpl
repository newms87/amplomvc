<?= $is_ajax ? '' : call('admin/header'); ?>

<section class="row admin-login">
	<div class="col xs-12 sm-10 md-6 lg-4 xl-3">
		<header class="row top-row">
			<h1>
				<i class="fa fa-lock col auto"></i>
				<span class="text col auto padding-left">{{Admin Login}}</span>
			</h1>
		</header>

		<div class="login-form-row row">
			<form action="<?= site_url('admin/user/authenticate') ?>" method="post" enctype="multipart/form-data" class="form" data-if-ajax="#admin-login">
				<div class="form-item username">
					<input type="text" name="username" placeholder="{{Username / Email}}" value="<?= $username; ?>"/>
				</div>
				<div class="form-item password">
					<input type="password" name="password" placeholder="{{Password}}" value=""/>
					<br/>
					<a href="<?= site_url('admin/user/forgotten'); ?>" class="forgotten-link">{{Forgot your Password?}}</a>
				</div>
				<div class="form-item submit">
					<button data-loading="{{Logging You In...}}">{{Login}}</button>
				</div>
		</div>
	</div>


	<? if ($is_ajax) { ?>
		<div class="row message-row">
			<?= render_message(); ?>
		</div>
	<? } ?>
	</div>
</section>

<?= $is_ajax ? '' : call('admin/footer'); ?>
