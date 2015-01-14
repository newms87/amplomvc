<div class="account-info">
	<div class="forms">
		<form action="<?= site_url('account/update'); ?>" method="post" class="form-item read col xs-12 xs-center md-left">
			<div class="heading">
				<h3 class="col xs-12 md-7 md-left top">{{Name &amp; Email}}</h3>

				<div class="buttons col xs-12 md-5 md-right top">
					<a class="edit-field readonly">{{Edit}}</a>
					<button data-loading="{{Saving...}}" class="editing button">{{Save}}</button>
					<a class="editing cancel-edit">{{Cancel}}</a>
				</div>
			</div>

			<div class="field name readonly">
				<span class="field firstname"><?= $customer['firstname']; ?></span>
				<span class="field lastname"><?= $customer['lastname']; ?></span>
			</div>
			<div class="field email readonly"><?= $customer['email']; ?></div>

			<div class="input editing">
				<input type="text" name="firstname" value="<?= $customer['firstname']; ?>" placeholder="{{First Name}}"/>
				<input type="text" name="lastname" value="<?= $customer['lastname']; ?>" placeholder="{{Last Name}}"/>
				<br/>
				<br/>
				<input type="text" name="email" value="<?= $customer['email']; ?>" placeholder="{{Email}}"/>
			</div>
		</form>

		<form action="<?= site_url('account/update'); ?>" method="post" class="form-item read col xs-12 xs-center md-left">
			<div class="heading">
				<h3 class="col xs-12 md-7 md-left top">{{Password}}</h3>

				<div class="buttons col xs-12 md-5 md-right top">
					<a class="edit-field readonly">{{Edit}}</a>
					<button data-loading="{{Saving...}}" class="editing button">{{Save}}</button>
					<a class="editing cancel-edit">{{Cancel}}</a>
				</div>
			</div>

			<div class="field password-field readonly">XXXXXXXX</div>

			<div class="input editing">
				<input type="password" name="password" placeholder="{{Password}}"/>
				<input type="password" name="confirm" placeholder="{{Confirm Password}}"/>
			</div>
		</form>

	</div>
</div>

<script type="text/javascript">
	var $account_info = $('.account-info');
	$account_info.find('.edit-field').click(function () {
		$(this).closest('form').addClass('edit').removeClass('read');
	});

	$account_info.find('form').submit(function () {
		var $form = $(this);

		$form.find('[data-loading]').loading();

		$.post($form.attr('action'), $form.serialize(), function (response) {
			$form.find('[data-loading]').loading('stop');

			$form.ac_msg('clear');

			if (response.error) {
				$form.ac_msg(response);
			} else {
				$form.find('.input [name]').each(function (i, e) {
					var $e = $(e);
					$form.find('.field.' + $e.attr('name')).html($e.val());
				});

				$form.removeClass('edit').addClass('read');
			}
		});

		return false;
	});

	$account_info.find('.cancel-edit').click(function () {
		$(this).closest('form').removeClass('edit').addClass('read');
	});
</script>
