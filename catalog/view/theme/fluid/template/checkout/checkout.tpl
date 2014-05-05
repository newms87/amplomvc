<?= _call('common/header'); ?>
<?= _area('left'); ?>
<?= _area('right'); ?>

<section id="checkout-page" class="content">

	<header class="row top-row">
		<div class="wrap">
			<?= _breadcrumbs(); ?>

			<h1><?= _l("Checkout"); ?></h1>
		</div>
	</header>

	<?= _area('top'); ?>

	<div class="row checkout-row">
		<div class="wrap">
			<div id="checkout-content">
				<form action="<?= site_url('checkout/checkout/confirm'); ?>" class="form" method="post">
					<? if (!$is_logged && !$is_guest) { ?>
						<?= _block('account/login', null, array('template' => 'block/account/login')); ?>
					<? } else { ?>
						<? if ($is_guest) { ?>
							<?= _call('checkout/checkout/guest'); ?>
						<? } else { ?>
							<div class="checkout-shipping col xs-12 lg-6">
								<div id="shipping-address" class="address">
									<?= _block('account/address/select', null, array("address_id" => $shipping_address_id)); ?>
								</div
							</div>
						<? } ?>
					<? } ?>
				</form>
			</div>
		</div>
	</div>

	<?= _area('bottom'); ?>

</section>

<?= _call('common/footer'); ?>
