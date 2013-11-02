<div class="login-header">
	<form action="<?= $action; ?>" method="post" class="login-form">
		<div class="email">
			<input type="text" value="<?= $email; ?>" name="email" default="<?= $entry_email; ?>"/>
		</div>
		<div class="password">
			<input type="password" value="" name="password" default="*********"/>
		</div>
		<input type="submit" style="position:absolute; left:-9999px"/>
	</form>
</div>

<script type="text/javascript">
	$('.login-form div input').focus(function () {
		if ($(this).hasClass('empty_val')) {
			$(this).removeClass('empty_val').val('');
		}
	})
		.blur(function () {
			if (!$(this).val()) {
				$(this).addClass('empty_val');
				$(this).val($(this).attr('default'));
			}
		})
		.trigger('blur');
</script>
