<div class="login_header">
	<div id="one_click">
		<? if (!empty($gp_login)) { ?>
		<a href="<?= $gp_login; ?>" class="gp_login small" title="{{Sign in with Google+}}"></a>
		<? } ?>

		<? if (!empty($fb_login)) { ?>
		<a href="<?= $fb_login; ?>" class="fb_login small" title="{{Sign in with Facebook}}"></a>
		<? } ?>
	</div>

	<form action="<?= site_url('customer/authenticate'); ?>" method="post" class="login-form">
		<div class="email">
			<input type="text" value="<?= $username; ?>" name="username" placeholder="{{Username / Email}}"/>
		</div>
		<div class="password">
			<input type="password" value="" name="password" placeholder="*********"/>
		</div>
		<input type="submit" style="position:absolute; left:-9999px"/>
	</form>
</div>

<script type="text/javascript">
	//If browser does not support placeholder attribute emulate one!
	if (!('placeholder' in document.createElement('input'))) {
		$('.login-form div input').focus(function () {
			if ($(this).hasClass('empty_val')) {
				$(this).removeClass('empty_val').val('');
			}
		})
			.blur(function () {
				if (!$(this).val()) {
					$(this).addClass('empty_val');
					$(this).val($(this).attr('placeholder'));
				}
			})
			.trigger('blur');
	}
</script>
