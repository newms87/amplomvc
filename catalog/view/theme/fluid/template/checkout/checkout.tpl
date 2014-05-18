<?= call('common/header'); ?>
<?= area('left'); ?>
<?= area('right'); ?>

<section id="checkout-page" class="content">

	<header class="row top-row">
		<div class="wrap">
			<?= breadcrumbs(); ?>

			<h1><?= _l("Checkout"); ?></h1>
		</div>
	</header>

	<?= area('top'); ?>

	<? if (!$is_logged && !$is_guest) {
		$step = 'login';
	} elseif (($has_shipping && !$shipping_address_id) || !$payment_address_id) {
		$step = 'address';
	} elseif (($has_shipping && !$shipping_key) || !$payment_key) {
		$step = 'methods';
	} else {
		$step = 'confirmation';
	} ?>

	<form id="checkout-form" action="<?= site_url('checkout/add_order'); ?>" class="form <?= $step; ?>" method="post">

		<div class="row checkout-row">
			<div class="wrap">
				<? if ($step === 'login') { ?>
					<?= block('account/login', null, array('template' => 'block/account/login')); ?>
				<? } else { ?>
					<? if ($is_guest) { ?>
						<?= call('checkout/guest'); ?>
					<? } else { ?>
						<div class="checkout-delivery col xs-12 md-6">
							<div id="shipping-address" class="address-list">
								<h3><?= _l("Delivery Address"); ?></h3>
								<? $build = array(
									'name'   => 'shipping_address_id',
									'data'   => format_all('address', $shipping_addresses),
									'select' => $shipping_address_id,
									'key'    => 'address_id',
									'value'  => 'formatted',
								); ?>

								<?= build('ac-radio', $build); ?>

								<a href="<?= site_url('account/address/form', 'select=shipping_address_id'); ?>" class="add-address colorbox"><?= _l("Add New Address"); ?></a>
							</div>
							<a class="change-address"><?= _l("Change Address"); ?></a>
						</div>

						<div class="checkout-payment col xs-12 md-6">
							<div id="payment-address" class="address-list">
								<h3><?= _l("Billing Address"); ?></h3>
								<? $build = array(
									'name'   => 'payment_address_id',
									'data'   => format_all('address', $payment_addresses),
									'select' => $payment_address_id,
									'key'    => 'address_id',
									'value'  => 'formatted',
								); ?>

								<?= build('ac-radio', $build); ?>

								<a href="<?= site_url('account/address/form', 'select=payment_address_id'); ?>" class="add-address colorbox"><?= _l("Add New Address"); ?></a>
							</div>
							<a class="change-address"><?= _l("Change Address"); ?></a>
						</div>
					<? } ?>
				<? } ?>
			</div>
		</div>

		<div class="row checkout-methods">
			<div class="wrap">
				<?= call('checkout/methods'); ?>
			</div>
		</div>

		<div class="checkout-submit-row row">
			<div class="wrap">
				<div class="checkout-submit col xs-12">
					<div class="form-item submit">
						<button data-loading="Submitting..."><?= _l("Continue Checkout"); ?></button>
					</div>
				</div>
			</div>
		</div>
	</form>

	<div class="row checkout-confirmation">
		<div class="wrap">
			<? if ($step === 'confirmation') { ?>
				<?= call('checkout/confirmation'); ?>
			<? } ?>
		</div>
	</div>

	<?= area('bottom'); ?>

</section>

<script type="text/javascript">
	var $co_form = $('#checkout-form');
	$co_form.submit(function () {
		var $this = $(this);
		var $button = $this.find('button');
		$button.loading();
		$co_form.loading();

		$.post($this.attr('action'), $this.serialize(), function (response) {
			$button.loading('stop');
			$co_form.loading('stop');

			if (!response) {
				response = {error: "<?= _l("There was a problem checking you out. Please try again."); ?>"}
			}

			if (response.success) {
				$co_form.removeClass("methods address").addClass("confirmation");
				$co_form.loading();
				$button.loading();

				$.post("<?= site_url('checkout/confirmation'); ?>", {}, function (response) {
					$co_form.loading('stop');
					$button.loading('stop');
					$('#checkout-page').find('.checkout-confirmation .wrap').html(response);
				}, 'html');

				$co_form.find('.checkout-submit').ac_msg(response);
			} else {
				$co_form.find('.checkout-submit').ac_errors(response);
			}
		}, 'json');

		return false;
	});

	$('.address-list label').click(function () {
		var $this = $(this);

		if ($this.hasClass('checked')) {
			return;
		}

		$this.closest('.address-list').find('label').removeClass('checked');
		$this.addClass('checked');

		if ($('[name=shipping_address_id]:checked').length && $('[name=payment_address_id]:checked').length) {
			$co_form.find('button').loading({text: "<?= _l("Loading"); ?>"});
			var $co_methods = $co_form.find('.checkout-methods .wrap');
			$co_methods.children().fadeOut();
			$co_methods.loading();

			$.post("<?= site_url('checkout/methods'); ?>", $co_form.serialize(), function(response) {
				$co_form.find('button').loading('stop');
				$co_form.addClass('methods');
				$co_methods.html(response);
			});
		}
	});

	$('.change-address').click(function () {
		$('#checkout-form').removeClass('methods confirmation').addClass('address');
	});

	$('.change-method').click(function () {
		$('#checkout-form').removeClass('confirmation').addClass('methods');
	});
</script>

<?= call('common/footer'); ?>
