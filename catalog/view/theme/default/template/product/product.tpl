<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $content_top; ?>

	<div class="product_info">
		<div class="left">
			<? if (!empty($thumb)) { ?>
				<div id="the_zoombox" class="image">
					<a id="zoombox_image_link" onclick="return colorbox($(this), {width: '70%', height: '90%'});" href="<?= $popup; ?>"
						title="<?= $head_title; ?>" class="zoombox" rel="gal1">
						<img src="<?= $thumb; ?>" title="<?= $head_title; ?>" alt="<?= $head_title; ?>" id="image"/>
					</a>
					<a class="view_full_size" onclick="return colorbox($('#zoombox_image_link'), {width: '70%', height: '90%'});"><?= $text_view_full_size_image; ?></a>
				</div>
			<? } ?>

			<? if (!empty($images)) { ?>
				<div class="image-additional">
					<? foreach ($images as $img) { ?>
						<a href="javscript:void(0);" title="<?= $head_title; ?>" rel="<?= $img['rel']; ?>">
							<img src="<?= $img['thumb']; ?>" title="<?= $head_title; ?>" alt="<?= $head_title; ?>"/>
						</a>
					<? } ?>
				</div>
			<? } ?>

			<? if (!empty($block_product_related)) { ?>
				<?= $block_product_related; ?>
			<? } ?>
		</div>

		<div class="right">
			<div class="title"><?= $head_title; ?></div>

			<div class="description">
				<? if ($manufacturer) { ?>
					<div class="description_manufacturer">
						<span class="view_more"><?= $text_view_more; ?></span>
						<span class="keep_shopping"><?= $text_keep_shopping; ?></span>
					</div>
				<? } ?>

				<? if ($display_model) { ?>
					<div class="description_model">
						<span><?= $text_model; ?></span>
						<span><?= $model; ?></span>
					</div>
				<? } ?>

				<? if ($price && $is_purchasable) { ?>
					<div class="price">
						<?= $text_price; ?>

						<? if (empty($special)) { ?>
							<span class="regular"><?= $price; ?></span>
						<? } else { ?>
							<span class="special"><?= $special; ?></span>
							<span class="retail"><?= _l("%s retail", $price); ?></span>
						<? } ?>

						<? if (!empty($is_final_explanation)) { ?>
							<div class="extra_info_block">
								<span class="final_sale"></span>
								<span class="help_icon">
									<span class="help_icon_popup"><?= $is_final_explanation; ?></span>
								</span>
							</div>
						<? } ?>

						<? if (!empty($is_default_shipping)) { ?>
							<div class="extra_info_block">
								<span class="not_default_shipping"></span>
								<span class="help_icon">
									<span class="help_icon_popup"><?= $shipping_policy['description']; ?></span>
								</span>
							</div>
						<? } ?>

						<br class="clear" />
						<? if (!empty($tax)) { ?>
							<span class="price-tax"><?= $text_tax; ?> <?= $tax; ?></span><br/>
						<? } ?>
						<? if ($points) { ?>
							<span class="reward"><small><?= $text_points; ?> <?= $points; ?></small></span><br/>
						<? } ?>
						<? if ($discounts) { ?>
							<br/>
							<div class="discount">
								<? foreach ($discounts as $discount) { ?>
									<?= sprintf($text_discount, $discount['quantity'], $discount['price']); ?><br/>
								<? } ?>
							</div>
						<? } ?>
					</div>
				<? } ?>

				<? if ($reward) { ?>
					<div class="description_reward"><span><?= $text_reward; ?></span><span><?= $reward; ?></span></div>
				<? } ?>
				<? if (!empty($stock)) { ?>
					<div class="description_stock"><span><?= $text_stock; ?></span><span><?= $stock; ?></span></div>
				<? } ?>
				<? if ($description) { ?>
					<div class="product_description">
						<div class="scroll_wrapper">
							<?= $description; ?>
						</div>
					</div>
				<? } ?>
			</div>

			<? if ($is_purchasable) { ?>
				<form id="product_form" action="<?= $buy_now; ?>" method="post">
					<div class="cart clear">
						<? if (isset($block_product_options)) { ?>
							<?= $block_product_options; ?>
						<? } ?>
						<div id="product_submit_box" class="clear">
							<div class="quantity">
								<label><?= _l("Quantity"); ?></label>
								<input type="text" name="quantity" id="quantity" size="2" value="<?= $minimum; ?>"/>
								<input type="hidden" id="product_id" name="product_id" size="2" value="<?= $product_id; ?>"/>
							</div>
							<div id="product_buttons_box">
								<div id="buy_product_buttons">
									<input type="submit" name="buy_now" value="<?= _l("Buy Now"); ?>" id="button_buy_now" class="button"/>
									<input type="button" name="add_to_cart" value="<?= _l("Add to Cart"); ?>" id="button_add_to_cart" class="button"/>
								</div>
								<div id="processing_product" class="hidden"><?= $text_processing; ?></div>
							</div>
						</div>

						<div id="cart_additional_buttons">
							<a href="<?= $view_cart_link; ?>"><?= _l("View Cart"); ?></a>
							<a href="<?= $checkout_link; ?>"><?= _l("Checkout"); ?></a>
							<a href="<?= $continue_shopping_link; ?>"><?= $button_shopping; ?></a>
						</div>
						<? if ($minimum > 1) { ?>
							<div class="minimum"><?= $text_minimum; ?></div>
						<? } ?>
					</div>
				</form>

			<? } else { ?>
				<div id="product_inactive"><?= $text_inactive; ?></div>
			<? } ?>

			<? if (!empty($block_review)) { ?>
				<div class="clear"><?= $block_review; ?></div>
			<? } ?>

			<? if (!empty($block_sharing)) { ?>
				<div id="share_product" class="clear"><?= $block_sharing; ?></div>
			<? } ?>
		</div>
	</div>

	<div id="additional_information">
		<div id="product_additional_tabs" class="htabs">
			<? if ($information) { ?>
				<a href="#tab-information"><?= $tab_information; ?></a>
			<? } ?>

			<a href="#tab-shipping-return"><?= $tab_shipping_return; ?></a>

			<? if (!empty($attribute_groups)) { ?>
				<a href="#tab-attribute"><?= $tab_attribute; ?></a>
			<? } ?>
		</div>

		<? if ($information) { ?>
			<div id="tab-information" class="tab-content"><?= $information; ?></div>
		<? } ?>

		<div id="tab-shipping-return" class="tab-content">
			<? if ($shipping_policy) { ?>
				<div class="shipping_policy">
					<div class="title"><?= $shipping_policy['title']; ?></div>
					<div class="description"><?= $shipping_policy['description']; ?></div>
				</div>
			<? } ?>

			<? if ($return_policy) { ?>
				<div class="return_policy">
					<div class="title"><?= $return_policy['title']; ?></div>
					<div class="description"><?= $return_policy['description']; ?></div>
				</div>
			<? } ?>

			<? if (!empty($is_final_explanation)) { ?>
				<p class="final_sale_explain"><?= $is_final_explanation; ?></p>
			<? } ?>

			<?= $text_view_policies; ?>
		</div>

		<? if (!empty($attribute_groups)) { ?>
			<div id="tab-attribute" class="tab-content">
				<table class="attribute">
					<? foreach ($attribute_groups as $attribute_group) { ?>
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

	<? if (!empty($tags)) { ?>
		<div class="tags"><b><?= $text_tags; ?></b>
			<? foreach ($tags as $i => $tag) { ?>
				<a href="<?= $tags[$i]['href']; ?>"><?= $tags[$i]['text']; ?></a> <?= $i == (count($tags) - 1) ? '' : ','; ?>
			<? } ?>
		</div>
	<? } ?>

	<?= $content_bottom; ?>
</div>


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

	$(document).ready(function () {
		$('.image-additional a img, .option_image a img').click(function () {
			if ($(this).attr('src').replace(/-\d+x\d+/, '') == $('#the_zoombox .zoomPad > img').attr('src').replace(/-\d+x\d+/, '')) {
				event.preventDefault();
				return false;
			}
		});
		$('.zoombox').jqzoom({
			zoomWidth: $.ac_vars.image_thumb_width,
			zoomHeight: $.ac_vars.image_thumb_height,
			position: 'right',
			xOffset: 25,
			yOffset: 0,
			preloadText: '<?= $text_zoombox_load; ?>'
		});
	});

	$('#product_additional_tabs a').tabs();
</script>

<?= $footer; ?>
