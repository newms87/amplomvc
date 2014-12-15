<?= $is_ajax ? '' : call('admin/header'); ?>

<section id="admin-login" class="content">
	<header class="row top-row">
		<div class="wrap">
			<h1><img src="<?= theme_url('image/lockscreen.png'); ?>" alt=""/> {{Admin Login}}</h1>
		</div>
	</header>

	<div class="content-row row">
		<div class="wrap">
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
			</form>
		</div>
	</div>

	<? if ($is_ajax) { ?>
		<?= render_message(); ?>
	<? } else { ?>
		<script type="text/javascript">
			$('#admin-login form').submit(function() {
				$(this).find('[data-loading]').loading();
			});
		</script>
	<? } ?>
</section>

<?= $is_ajax ? '' : call('admin/footer'); ?>
