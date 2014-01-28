<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $content_top; ?>

	<h1><?= _l("Shopping Cart"); ?>
		<? if (isset($weight)) { ?>
			<span id="cart_weight">(<?= $weight; ?>)</span>
		<? } ?>
	</h1>
	<? if (!$cart_empty) { ?>

		<?= $block_cart; ?>

		<div id="cart_actions">
			<h2><?= _l("What would you like to do next?"); ?></h2>
			<? if (!empty($block_coupon)) { ?>
				<div>
					<a id="text_block_coupon" onclick="$('#toggle_block_coupon').slideToggle();"><?= _l("Use Coupon Code"); ?></a>

					<div id="toggle_block_coupon" class="content">
						<?= $block_coupon; ?>
					</div>
				</div>
			<? } ?>

			<? if (!empty($block_voucher)) { ?>
				<div>
					<a id="text_block_voucher" onclick="$('#toggle_block_voucher').slideToggle();"><?= _l("Use Voucher"); ?></a>

					<div id="toggle_block_voucher" class="content">
						<?= $block_voucher; ?>
					</div>
				</div>
			<? } ?>

			<? if (!empty($block_reward)) { ?>
				<div>
					<a id="text_block_reward" onclick="$('#toggle_block_reward').slideToggle();"><?= _l("Use Reward"); ?></a>

					<div id="toggle_block_reward" class="content">
						<?= $block_reward; ?>
					</div>
				</div>
			<? } ?>

			<? if (!empty($block_shipping)) { ?>
				<div>
					<a id="text_block_shipping" onclick="$('#toggle_block_shipping').slideToggle();"><?= _l("Estimate Shipping"); ?></a>

					<div id="toggle_block_shipping" class="content">
						<?= $block_shipping; ?>
					</div>
				</div>
			<? } ?>
		</div>

		<? if (!empty($block_total) && $can_checkout) { ?>
			<div id="cart_block_total">
				<?= $block_total; ?>
			</div>
		<? } ?>

		<div class="buttons">
			<? if ($can_checkout) { ?>
				<div class="right"><a href="<?= $checkout; ?>" class="button"><?= _l("Checkout"); ?></a></div>
			<? } ?>

			<div class="center"><a href="<?= $continue; ?>" class="button"><?= _l("Continue Shopping"); ?></a></div>
		</div>

	<? } else { ?>
		<?= $block_cart; ?>
		<div class="center"><a href="<?= $continue; ?>" class="button"><?= _l("Continue Shopping"); ?></a></div>
	<? } ?>

	<?= $content_bottom; ?>
</div>


<? //We use javascript to hide for no script compatibility ?>
<script type="text/javascript">
	<? if(!empty($block_coupon)) {?>
	$('#toggle_block_coupon').hide();
	<? }?>
	<? if(!empty($block_voucher)) {?>
	$('#toggle_block_voucher').hide();
	<? }?>
	<? if(!empty($block_reward)) {?>
	$('#toggle_block_reward').hide();
	<? }?>
	<? if(!empty($block_shipping)) {?>
	$('#toggle_block_shipping').hide();
	<? }?>

	$('#the_cart').on('cart_loaded', function () {
		load_block($('#cart_block_total'), 'block/cart/total');
	});
</script>


<?= $footer; ?>
