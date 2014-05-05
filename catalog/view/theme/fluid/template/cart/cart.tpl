<?= _call('common/header'); ?>
<?= _area('left'); ?>
<?= _area('right'); ?>

<section id="cart-page" class="content">
	<header class="row top-row">
		<div class="wrap">
		<?= _breadcrumbs(); ?>

		<h1><?= _l("Shopping Cart"); ?>
			<? if ($show_weight) { ?>
				<div class="cart-weight">(<?= format('weight', $weight); ?>)</div>
			<? } ?>
		</h1>
		</div>
	</header>

	<?= _area('top'); ?>

	<div class="cart-row row">
		<div class="wrap">
			<? if (!$is_empty) { ?>

				<?= _block('cart/cart'); ?>

				<div id="cart_actions">
					<h2><?= _l("What would you like to do next?"); ?></h2>
					<? if ($show_coupons) { ?>
						<div>
							<a id="text_block_coupon" onclick="$('#toggle_block_coupon').slideToggle();"><?= _l("Use Coupon Code"); ?></a>

							<div id="toggle_block_coupon" class="content">
								<?= _block('cart/coupon'); ?>
							</div>
						</div>
					<? } ?>

					<? if ($show_vouchers) { ?>
						<div id="cart-vouchers">
							<a class="toggle" onclick="$('#block-voucher').slideToggle();"><?= _l("Use Voucher"); ?></a>

							<div id="block-voucher" class="content">
								<?= _block('cart/vouchers'); ?>
							</div>
						</div>
					<? } ?>

					<? if ($show_rewards && $customer_points && $cart_points > 0) { ?>
						<div id="cart-rewards">
							<a class="toggle" onclick="$('#block-reward').slideToggle();"><?= _l("Use Reward"); ?></a>

							<div id="block-reward" class="content">
								<?= _block('cart/reward'); ?>
							</div>
						</div>
					<? } ?>

					<? if ($has_shipping) { ?>
						<div id="cart-shipping-estimate">
							<a class="toggle" onclick="$('#block-shipping').slideToggle();"><?= _l("Estimate Shipping"); ?></a>

							<div id="block-shipping" class="content">
								<?= _block('cart/shipping'); ?>
							</div>
						</div>
					<? } ?>
				</div>

				<? if ($show_total && $can_checkout) { ?>
					<div id="cart-block-total">
						<?= _block('cart/total'); ?>
					</div>
				<? } ?>

				<div class="buttons">
					<? if ($can_checkout) { ?>
						<div class="right"><a href="<?= $checkout; ?>" class="button"><?= _l("Checkout"); ?></a></div>
					<? } ?>

					<div class="center"><a href="<?= $continue; ?>" class="button"><?= _l("Continue Shopping"); ?></a></div>
				</div>

			<? } else { ?>
				<?= _l("Your shopping cart is empty!"); ?>
				<div class="center"><a href="<?= $continue; ?>" class="button"><?= _l("Continue Shopping"); ?></a></div>
			<? } ?>
		</div>
	</div>

	<?= _area('bottom'); ?>

</section>

<? //We use javascript to hide for no script compatibility ?>
<script type="text/javascript">
	$('#cart-page').on('cart_loaded', function () {
		$('#cart-block-total')._block('cart/total');
	});
</script>

<?= _call('common/footer'); ?>
