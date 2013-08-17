<? if (!$products_only) { ?>
	<div class="box featured_box" style='overflow:auto;'>
	<div class="box_heading">
		<div class='featured_title'><span
				class='featured_title_first'><?= $featured_title[0]; ?></span><?= substr($featured_title, 1); ?></div>
		<div class='capistrano'><?= $featured_cat; ?></div>
	</div>
	<div class="box_content">
	<div class="box-product">
<? } ?>

<? foreach ($products as $product) { ?>
	<a href='<?= $product['href']; ?>' class='featured_product_clickable'>
		<? if ($product['thumb']) { ?>
			<div class="image"><img src="<?= $product['thumb']; ?>" alt="<?= $product['name']; ?>"/></div>
		<? } ?>
		<div class='featured_product_info'>
			<div class="name"><?= $product['name']; ?></div>
			<? if ($product['price']) { ?>
				<div class="price">
					<? if (!$product['special']) { ?>
						<?= $product['price']; ?>
					<? } else { ?>
						<span class="special"><?= $product['special']; ?></span> <span
							class="retail"><?= $product['price']; ?> retail</span>
					<? } ?>
				</div>
			<? } ?>
			<? if ($product['flashsale_id']) { ?>
				<div class='fs_countdown'>
					<div id='fpop-<?= $product['product_id']; ?>' class='flash_countdown' callback='end_featured_fs'
					     type='short' flashid='<?= $product['flashsale_id']; ?>'></div>
				</div>
			<? } ?>
		</div>
		<? if ($product['is_final']) { ?>
			<!-- <span class='final_sale_logo'></span> -->
		<? } ?>
	</a>
<? } ?>


<? if (!$products_only) { ?>
	</div>
	</div>
	</div>
	<? if (!$in_context) { ?>
		<? if (count($products) < $total_products) { ?>
			<div class='load_more_products' pages='<?= $num_pages; ?>'>
				<span><?= $text_scroll_more; ?></span>
				<img src='<?= $ajax_loader; ?>'/>
			</div>
		<? } ?>

		<script type='text/javascript'>
			//<!--
			function set_page(page) {
				$('#featured_page').val(page);
				show_search();
			}
			function current_page() {
				return parseInt($('#featured_pager .active').attr('value')) || 1;
			}

			function end_featured_fs(context, op) {
				if (op == 'ended') {
					p = context.closest('.featured_product_info');
					orig = p.find('.retail');
					if (orig.length > 0)
						p.find('.price').html(orig.html().replace(/retail/, ''));
					orig.remove();
					p.find('.fs_countdown').remove();
				}
			}
			//-->
		</script>
	<? } ?>
<? } ?>