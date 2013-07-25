<div class="right">
	<div class="title"><?= $heading_title; ?></div>
	<div class="description">
		<? if ($manufacturer) { ?>
		<div class="description_manufacturer">
			<span class="view_more"><?= $text_view_more; ?></span>
			<span class="keep_shopping"><?= $text_keep_shopping; ?></span>
		</div>
		<? } ?>
		<? if($display_model) {?>
		<div class="description_model"><span><?= $text_model; ?></span><span><?= $model; ?></span></div>
		<? } ?>
		
		<? if ($price && $is_purchasable) { ?>
		<div class="price">
			<?= $text_price; ?>
			
			<? if (empty($special)) { ?>
				<span class="regular"><?= $price; ?></span>
			<? } else { ?>
				<span class="special"><?= $special; ?></span><span class="retail"><?= $price; ?> retail</span>
			<? } ?>
			
			<? if(!empty($is_final_explanation)){?>
					<div class='extra_info_block'><span class='final_sale'></span><span class='help_icon'><span class='help_icon_popup'><?= $is_final_explanation; ?></span></span></div>
			<? }?>
			
			<? if(!empty($is_default_shipping)){?>
				<div class='extra_info_block'><span class='not_default_shipping'></span><span class='help_icon'><span class='help_icon_popup'><?= $shipping_policy['description']; ?></span></span></div>
			<? }?>
			
			<br style='clear:both' />
			<? if (!empty($tax)) { ?>
				<span class="price-tax"><?= $text_tax; ?> <?= $tax; ?></span><br />
			<? } ?>
			<? if ($points) { ?>
				<span class="reward"><small><?= $text_points; ?> <?= $points; ?></small></span><br />
			<? } ?>
			<? if ($discounts) { ?>
			<br />
			<div class="discount">
				<? foreach ($discounts as $discount) { ?>
				<?= sprintf($text_discount, $discount['quantity'], $discount['price']); ?><br />
				<? } ?>
			</div>
			<? } ?>
		</div>
		<? } ?>
	
		<? if ($reward) { ?>
		<div class="description_reward"><span><?= $text_reward; ?></span><span><?= $reward; ?></span></div>
		<? } ?>
		<? if(!empty($stock)) {?>
		<div class="description_stock"><span><?= $text_stock; ?></span><span><?= $stock; ?></span></div>
		<? } ?>
		<? if($description) { ?>
		<div class="product_description"><?= $description; ?></div>
		<? } ?>
	</div>
	
	<? if($is_purchasable){?>
	<div class="cart">
		<? if(isset($block_product_options)) {?>
			<?= $block_product_options; ?>
		<? }?>
	<div style="clear:both"></div>
		<div id='product_submit_box'>
			<div id='product_quantity_box'>
				<?= $text_qty; ?>
				<input type="text" name="quantity" id='quantity' size="2" value="<?= $minimum; ?>" />
				<input type="hidden" id='product_id' name="product_id" size="2" value="<?= $product_id; ?>" />
			</div>
			<div id='product_buttons_box'>
				<span id='buy_product_buttons'>
					<input type="button" value="<?= $button_buy_now; ?>" id="button-buy-now" class="button" />
					<input type="button" value="<?= $button_cart; ?>" id="button-cart" class="button" />
				</span>
				<span id='processing_product' style='display:none'><?= $text_processing; ?></span>
			</div>
		</div>
		<div id='or_text'><?= $text_or; ?></div>
			<div id='cart_additional_buttons' >
				<!-- <a onclick="addToWishList('<?= $product_id; ?>');"><?= $button_wishlist; ?></a> -->
				<a href='<?= $view_cart_link; ?>'><?= $button_view_cart; ?></a>
				<a href='<?= $checkout_link; ?>'><?= $button_checkout; ?></a>
				<a href='<?= $continue_shopping_link; ?>'><?= $button_shopping; ?></a>
			</div>
		<? if ($minimum > 1) { ?>
		<div class="minimum"><?= $text_minimum; ?></div>
		<? } ?>
	</div>
	<? }?>
	
	<? if (!empty($block_review)) { ?>
		<?= $block_review; ?>
	<? } ?>
	
	<? if (!empty($block_sharing)) { ?>
		<?= $block_sharing; ?>
	<? } ?>
	
	<? if(!$is_purchasable){?>
		<div id='product_inactive'><?= $text_inactive; ?></div>
		<?= $block_product_suggestions; ?>
	<? }?>
</div>

<script type="text/javascript">//<!--
//Check if Product description is overflowed
pd = $('.product-info .product_description')[0];
if (pd.scrollHeight > pd.clientHeight) {
	$(pd).addClass('overflowed');
}

function option_select_post_before(){
	$('#button-cart, #button-buy-now').attr('disabled',true);
	$('#buy_product_buttons').hide();
	$('#processing_product').fadeIn(100);
}

function option_select_post_after(){
	$('#button-cart, #button-buy-now').attr('disabled',false);
	$('#buy_product_buttons').fadeIn(800);
	$('#processing_product').fadeOut(200);
}

$('#button-cart, #button-buy-now').bind('click', function() {
	buynow = this.id=='button-buy-now';
	
	option_select_post_before();
	
	selected_options = {};
	
	$('.option').each(function(opt_i,opt_e){
		options = {}
		$(opt_e).find('.selected_option').each(function(i,e){
			if($(e).attr('value') || $(e).val()){
				ov = 0;
				if($(e).is('select')){
					ov = $(e).find('option[value="' + $(e).val() + '"]').attr('ov');
					pov = $(e).val();
				}
				else{
					ov = $(e).attr('ov');
					pov = $(e).attr('value');
				}
				
				options[i] = {option_value_id: ov, product_option_value_id: pov};
			}
		});
		selected_options[$(opt_e).attr('option_id')] = options;
	});
	
	data = {selected: selected_options, product_id: <?= $product_id; ?>, quantity: $('#quantity').val()};
	
	$.ajax({
		url: "<?= $url_add_to_cart; ?>",
		type: 'post',
		data: data,
		dataType: 'json',
		success: function(json) {
			$('.success, .warning, .attention, information, .error').remove();
			$('#product_options .option').removeClass('option_error');
			
			if (json['error']) {
				if (json['error']['option']) {
					msgs = '';
					for (i in json['error']['option']) {
						$('#option-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>').addClass('option_error');
						msgs += json['error']['option'][i] + '<br>';
					}
					
					$('#page-content > .product-info').before("<div class='message_box warning'>" + msgs + "</div>");
				}
			}
			
			if (json['success']) {
				if(buynow){
					buynow = 'redirect';
					window.location = '<?= $checkout_link; ?>';
				}
				else{
					show_msg('success', json['success']);
					$('#cart-total').html(json['total']);
				}
			}
		},
		complete: function(json, status){
			if( buynow != 'redirect' ){
				option_select_post_after();
			}
			
			if(status != 'success'){
				show_msg('warning', '<?= $error_add_to_cart; ?>');
			}
		}
	});
});
//--></script>
