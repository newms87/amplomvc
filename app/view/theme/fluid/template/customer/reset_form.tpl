<?= $is_ajax ? '' : call('header'); ?>

<section id="reset-password-page" class="content">
	<header class="row top-row">
		<div class="wrap">
			<?= $is_ajax ? '' : breadcrumbs(); ?>

			<h1>{{Reset Your Password}}</h1>
		</div>
	</header>

	<div class="row reset-password">
		<div class="wrap">
			<div class="col xs-8 md-6 lg-5 center">
				<form action="<?= $save; ?>" class="form full-width" method="post" enctype="multipart/form-data">
					<h2>{{Enter your new Password:}}</h2>

					<div class="form-item">
						<input type="password" autocomplete="off" placeholder="{{New Password}}" name="password" value=""/>
					</div>
					<div class="form-item">
						<input type="password" name="confirm" placeholder="{{Confirm}}" value=""/>
					</div>

					<div class="buttons">
						<div class="left">
							<a href="<?= $cancel; ?>" class="button">{{Cancel}}</a>
						</div>
						<div class="right">
							<button class="button">{{Change Password}}</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>

<script type="text/javascript">
	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= $is_ajax ? '' : call('footer'); ?>
