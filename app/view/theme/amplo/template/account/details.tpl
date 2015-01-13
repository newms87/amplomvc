<?= $is_ajax ? '' : call('header'); ?>
<?= area('left') . area('right'); ?>

<? $sprite = theme_sprite('sprite@1x.png'); ?>

<section class="account-page content">
	<header class="row account-details section-style-b">
		<div class="wrap">
			<div class="account-nav col xs-12 xs-center lg-3 lg-left top">
				<a href="<?= site_url('account'); ?>" class="menu-tab <?= $path === 'account' ? 'active' : ''; ?>" data-tab="#my-details">{{My Details}}</a>
				<a href="<?= site_url('account/order_history'); ?>" class="menu-tab <?= $path === 'account' ? 'account/order_history' : ''; ?>" data-tab="#order-history">{{Order History}}</a>
			</div>

			<div class="account-info col xs-12 lg-9 top">
				<h1>{{My Details}}</h1>

				<div class="profile">
					<form action="<?= site_url('account/update'); ?>" method="post" class="form-item read">
						<div class="heading">
							<h3 class="col xs-9 left top">{{Name &amp; Email}}</h3>

							<div class="buttons col xs-3 top right">
								<a class="edit-field readonly">{{Edit}}</a>
								<a class="editing cancel-edit">{{Cancel}}</a>
								<button data-loading="{{Saving...}}" class="editing button">{{Save}}</button>
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
							<br />
							<br />
							<input type="text" name="email" value="<?= $customer['email']; ?>" placeholder="{{Email}}"/>
						</div>
					</form>

					<form action="<?= site_url('account/update'); ?>" method="post" class="form-item read">
						<div class="heading">
							<h3 class="col xs-9 left top">{{Password}}</h3>

							<div class="buttons col xs-3 top right">
								<a class="edit-field readonly">{{Edit}}</a>
								<a class="editing cancel-edit">{{Cancel}}</a>
								<button data-loading="{{Saving...}}" class="editing button">{{Save}}</button>
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
		</div>
	</header>
</section>

<script type="text/javascript">
	var $account_info = $('.account-info');
	$account_info.find('.edit-field').click(function() {
		$(this).closest('form').addClass('edit').removeClass('read');
	});

	$account_info.find('form').submit(function() {
		var $form = $(this);

		$form.find('[data-loading]').loading();

		$.post($form.attr('action'), $form.serialize(), function (response) {
			$form.find('[data-loading]').loading('stop');

			$form.ac_msg('clear');

			if (response.error) {
				$form.ac_msg(response);
			} else {
				$form.find('.input [name]').each(function(i,e) {
					var $e = $(e);
					$form.find('.field.'+$e.attr('name')).html($e.val());
				});

				$form.removeClass('edit').addClass('read');
			}
		});

		return false;
	});

	$account_info.find('.cancel-edit').click(function (){
		$(this).closest('form').removeClass('edit').addClass('read');
	});
</script>

<?= $is_ajax ? '' : call('footer'); ?>
