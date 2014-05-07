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

	<div class="row checkout-row">
		<div class="wrap">
			<div id="checkout-content">
				<form action="<?= site_url('checkout/checkout/confirm'); ?>" class="form" method="post">
					<? if (!$is_logged && !$is_guest) { ?>
						<?= block('account/login', null, array('template' => 'block/account/login')); ?>
					<? } else { ?>
						<? if ($is_guest) { ?>
							<?= call('checkout/checkout/guest'); ?>
						<? } else { ?>
							<div class="checkout-shipping col xs-12 lg-6">
								<div id="shipping-address" class="address">
									<?= block('account/address/select', null, array("address_id" => $shipping_address_id)); ?>
								</div
							</div>
						<? } ?>
					<? } ?>
				</form>
			</div>
		</div>
	</div>

	<?= area('bottom'); ?>

</section>

<?= call('common/footer'); ?>
