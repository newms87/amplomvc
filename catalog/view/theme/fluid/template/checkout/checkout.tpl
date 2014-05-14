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

	<? if ((!$has_shipping || $shipping_key) && $payment_key) { ?>
		<? $step = 'confirmation'; ?>
	<? } elseif ((!$has_shipping || $shipping_address_id) && $payment_address_id) { ?>
		<? $step = 'methods'; ?>
	<? } else { ?>
		<? $step = 'address'; ?>
	<? } ?>

	<form id="checkout-form" action="<?= site_url('checkout/checkout/confirm'); ?>" class="form <?= $step; ?>" method="post">

		<div class="row checkout-row">
			<div class="wrap">
				<? if (!$is_logged && !$is_guest) { ?>
					<?= block('account/login', null, array('template' => 'block/account/login')); ?>
				<? } else { ?>
					<? if ($is_guest) { ?>
						<?= call('checkout/checkout/guest'); ?>
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
				<?= call('checkout/checkout/methods'); ?>
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

	<?= area('bottom'); ?>

</section>

<script type="text/javascript">
	var $co_form = $('#checkout-form');
	$co_form.submit(function () {
		var $this = $(this);
		var $button = $this.find('button');
		$button.loading();

		$.post($this.attr('action'), $this.serialize(), function (response) {
			$button.loading('stop');
			$this.after(response);
		});

		return false;
	});

	$('[name=shipping_address_id], [name=payment_address_id]').change(function () {
		if ($('[name=shipping_address_id]').val() && $('[name=payment_address_id]').val()) {
			$co_form.loading();

			$.post("<?= site_url("checkout/checkout/methods"); ?>", $co_form.serialize(), function(response) {
				$co_form.loading('stop');

				$co_form.addClass('methods');
				$('.checkout-methods .wrap').html(response);
			});
		}
	});

	$('.change-address').click(function () {
		$('#checkout-form').removeClass('methods confirmation');
	});

	$('.change-method').click(function () {
		$('#checkout-form').removeClass('confirmation');
	});
</script>

<?= call('common/footer'); ?>
