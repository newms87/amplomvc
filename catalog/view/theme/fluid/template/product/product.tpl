<?= _call('common/header'); ?>
<?= _area('left'); ?>
<?= _area('right'); ?>

<section id="product-<?= $product_id; ?>" class="product-content content">
	<header class="row top-row">
		<div class="wrap">
			<?= _breadcrumbs(); ?>

			<h1><?= $name; ?></h1>
		</div>
	</header>

	<?= _area('top'); ?>

	<div class="row product-row">
		<div class="wrap">
			<div class="product-image col xs-12 md-6">
				<? if (!empty($thumb)) { ?>
					<div id="the-zoombox" class="image clearfix">
						<a id="zoombox-image-link" href="<?= $popup; ?>" title="<?= $name; ?>" class="zoombox clearfix" rel="gal1">
							<img src="<?= $thumb; ?>" title="<?= $name; ?>" alt="<?= $name; ?>" id="image"/>
						</a>
						<a class="view-full-size" onclick="$.colorbox({href: $('#zoombox-image-link').attr('href'), width: '70%', height: '90%'});"><?= _l("View Full Size Image"); ?></a>
					</div>
				<? } ?>

				<? if (!empty($images)) { ?>
					<div class="image-additional">
						<? foreach ($images as $img) { ?>
							<a href="javscript:void(0);" title="<?= $name; ?>" rel="<?= $img['rel']; ?>">
								<img src="<?= $img['thumb']; ?>" title="<?= $name; ?>" alt="<?= $name; ?>"/>
							</a>
						<? } ?>
					</div>
				<? } ?>

				<? if (option('config_show_product_related')) { ?>
					<?= _block('product/related'); ?>
				<? } ?>
			</div>

			<div class="product-info col xs-12 md-6">
				<div class="product-top">
					<? if ($show_model) { ?>
						<div class="description-model">
							<span><?= _l("Model:"); ?></span>
							<span><?= $model; ?></span>
						</div>
					<? } ?>

					<? if ($show_price && $is_purchasable) { ?>
						<div class="price">
							<? if (empty($special)) { ?>
								<span class="regular"><?= $price; ?></span>
							<? } else { ?>
								<span class="special"><?= $special; ?></span>
								<span class="retail"><?= _l("%s retail", $price); ?></span>
							<? } ?>

							<? if (!empty($tax)) { ?>
								<div class="price-tax"><?= _l("Ex Tax: %s", $tax); ?></div>
							<? } ?>

							<? if (!empty($points)) { ?>
								<div class="price-reward"><?= _l("Price in reward points: %s", $points); ?></div>
							<? } ?>

							<? if (!empty($discounts)) { ?>
								<div class="discounts">
									<? foreach ($discounts as $discount) { ?>
										<div class="discount"><?= _l("Discount for %s: %s", $discount['quantity'], $discount['price']); ?></div>
									<? } ?>
								</div>
							<? } ?>
						</div>
					<? } ?>
				</div>

				<div class="product-tabs tab-header htabs">
					<a class="tab" href="#tab-description"><?= _l("Description"); ?></a>

					<? if ($information) { ?>
						<a class="tab" href="#tab-information"><?= _l("More Info"); ?></a>
					<? } ?>

					<a class="tab" href="#tab-shipping-return"><?= _l("Shipping / Returns"); ?></a>

					<? if (!empty($attribute_groups)) { ?>
						<a class="tab" href="#tab-attribute"><?= _l("Specifications"); ?></a>
					<? } ?>
				</div>

				<div class="tab-contents">
					<div id="tab-description" class="description">
						<? if (!empty($reward)) { ?>
							<div class="reward"><?= _l("You will earn %s points!", $reward); ?></div>
						<? } ?>

						<? if (!empty($stock)) { ?>
							<div class="description_stock <?= $stock_class; ?>">
								<span class="text"><?= _l("Availability:"); ?></span>
								<span class="stock"><?= $stock; ?></span>
							</div>
						<? } ?>

						<? if (!empty($description)) { ?>
							<div class="product-description">
								<div class="scroll-wrapper">
									<?= $description; ?>
								</div>
							</div>
						<? } ?>
					</div>

					<? if ($information) { ?>
						<div id="tab-information" class="tab-content"><?= $information; ?></div>
					<? } ?>

					<div id="tab-shipping-return" class="tab-content">
						<? if ($shipping_policy) { ?>
							<div class="shipping-policy">
								<div class="title"><?= $shipping_policy['title']; ?></div>
								<div class="description"><?= $shipping_policy['description']; ?></div>
							</div>
						<? } ?>

						<? if ($return_policy) { ?>
							<div class="return-policy">
								<div class="title"><?= $return_policy['title']; ?></div>
								<div class="description"><?= $return_policy['description']; ?></div>
							</div>
						<? } ?>

						<? if ($is_final) { ?>
							<div class="final-sale">
								<?= _l("A Product Marked as <span class=\"final_sale\"></span> cannot be returned. Read our <a href=\"%s\" onclick=\"$(this).colorbox()\">Return Policy</a> for details.", site_url('page/shipping_return_policy', 'product_id=' . $product_id)); ?>
							</div>
						<? } ?>

						<? if (option('config_shipping_return_info_id')) { ?>
							<p>
								<?= _l("Please see our"); ?>
								<a class="colorbox" href="<?= site_url('information/information/info', 'information_id=' . option('config_shipping_return_info_id')); ?>"><?= _l("Shipping & Return Policy"); ?></a>
								<?= _l("for more information."); ?>
							</p>
						<? } ?>
					</div>

					<? if (!empty($data_attribute_groups)) { ?>
						<div id="tab-attribute" class="tab-content">
							<table class="attribute">
								<? foreach ($data_attribute_groups as $attribute_group) { ?>
									<thead>
										<tr>
											<td colspan="2"><?= $attribute_group['name']; ?></td>
										</tr>
									</thead>
									<tbody>
										<? foreach ($attribute_group['attributes'] as $attribute) { ?>
											<tr>
												<td><?= $attribute['name']; ?></td>
												<td><?= $attribute['text']; ?></td>
											</tr>
										<? } ?>
									</tbody>
								<? } ?>
							</table>
						</div>
					<? } ?>
				</div>

				<? if ($is_purchasable) { ?>
					<form id="product-form" class="form full-width" action="<?= site_url('cart/cart/buy_now'); ?>" method="post">

						<div class="option-list">
							<?= _block('product/options', null, array('product_id' => $product_id)); ?>
						</div>

						<div class="cart">

							<div id="product-submit-box" class="clear">
								<div class="quantity form-item">
									<label><?= _l("Quantity"); ?></label>
									<input type="text" name="quantity" id="quantity" size="2" value="<?= $minimum; ?>"/>
									<input type="hidden" id="product-id" name="product_id" size="2" value="<?= $product_id; ?>"/>
								</div>
								<div id="product-buttons-box">
									<div id="buy-product-buttons">
										<input type="submit" name="buy_now" value="<?= _l("Buy Now"); ?>" id="button-buy-now" class="button medium"/>
										<input type="button" name="add_to_cart" value="<?= _l("Add to Cart"); ?>" id="button-add-to-cart" class="button medium"/>
									</div>
								</div>
							</div>

							<div class="product-nav">
								<a href="<?= site_url('cart/cart'); ?>"><?= _l("View Cart"); ?></a>
								<a href="<?= site_url('checkout/checkout'); ?>"><?= _l("Checkout"); ?></a>
								<a href="<?= $this->breadcrumb->prevUrl(); ?>"><?= _l("Continue Shopping"); ?></a>
							</div>
							<? if ($minimum > 1) { ?>
								<div class="minimum"><?= _l("This product has a minimum quantity of %s", $minimum); ?></div>
							<? } ?>
						</div>
					</form>

				<? } else { ?>
					<div id="product-inactive"><?= _l("This product is currently unavailable."); ?></div>
				<? } ?>

				<? if ($show_sharing) { ?>
					<div class="product-sharing">
						<?= _block('extras/sharing'); ?>
					</div>
				<? } ?>
			</div>

			<? if (!empty($tags)) { ?>
				<div class="tags"><b><?= _l("Tags:"); ?></b>
					<? foreach ($tags as $i => $tag) { ?>
						<a href="<?= $tags[$i]['href']; ?>"><?= $tags[$i]['text']; ?></a> <?= $i == (count($tags) - 1) ? '' : ','; ?>
					<? } ?>
				</div>
			<? } ?>
		</div>

	</div>
	</div>


	<? if ($show_reviews) { ?>
		<div class="row review-row">
			<div class="wrap">
				<?= _block('product/review'); ?>
			</div>
		</div>
	<? } ?>

	<?= _area('bottom'); ?>

</section>


<script type="text/javascript">
	//Check if Product description is overflowed
	pd = $('.product_info .product_description')[0];
	if (pd && pd.scrollHeight > pd.clientHeight) {
		$(pd).addClass('overflowed');
		$(pd).click(function () {
			$(this).toggleClass('hover');
		})
	}

	function option_select_post_before() {
		$('#product_form input[type=submit]').attr('disabled', true);
		$('#buy_product_buttons').addClass('hidden');
		$('#processing_product').removeClass('hidden');
	}

	function option_select_post_after() {
		$('#product_form input[type=submit]').attr('disabled', false);
		$('#buy_product_buttons').removeClass('hidden');
		$('#processing_product').addClass('hidden');
	}

	data = {
		form: $('#product_form'),
		before: option_select_post_before,
		after: option_select_post_after
	}

	$('#button_add_to_cart').add_to_cart(data);

	$('#zoombox-image-link').click(function() {
		if (!screen_sm) {
			$.colorbox({href: $(this).attr('href'), width: '70%', height: '90%'});
		}

		return false;
	});

	$(document).ready(function () {
		$('.image-additional a img, .option-image a img').click(function () {
			if ($(this).attr('src').replace(/-\d+x\d+/, '') == $('#the-zoombox .zoomPad > img').attr('src').replace(/-\d+x\d+/, '')) {
				event.preventDefault();
				return false;
			}
		});

		if (screen_md || screen_lg) {
			$('.zoombox').jqzoom({
				zoomWidth: $.ac_vars.image_thumb_width,
				zoomHeight: $.ac_vars.image_thumb_height,
				position: 'right',
				xOffset: 25,
				yOffset: 0,
				preloadText: '<?= _l("Loading High Resolution Image"); ?>'
			});
		}
	});

	$('.product-tabs a').tabs();
</script>

<?= _call('common/footer'); ?>
