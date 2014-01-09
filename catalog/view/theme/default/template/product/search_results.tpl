<div id="search_content">

	<div class="product_list" style="overflow:hidden">
		<?
		if (count($results)) {
			$cols  = 2;
			$count = 0;
			foreach ($results as $product) {
				extract($product);?>
				<div class="search_result" onclick="open_product_view('<?= $product['href']; ?>')">
					<div class="product_left">
						<div class="image"><img src="<?= $image; ?>" title="<?= $name; ?>" alt="<?= $name; ?>"/></div>
						<div class="name">
							<a class="product_name" onclick="open_product_view('<?= $href; ?>')"><?= $name; ?></a>

							<div class="designer_name">by <?= $designer_name; ?></div>
						</div>
						<? if ($rating) { ?>
							<div class="rating">
								<?= get_star_rating($rating); ?>
								<div class="reviews"><?= $reviews . " reviews"; ?></div>
							</div>
						<? } ?>
					</div>
					<div class="product_right">
						<div class="description"><?= $description; ?></div>
						<div class="price">
							<? if (isset($special)) { ?>
								<div class="product_info_orig_price"><?= $price; ?></div>
								<div class="product_info_price_text"><?= _l("On Sale!"); ?></div>
								<div class="product_info_price"><?= $special; ?></div>
							<? } else { ?>
								<div class="product_info_price"><?= $price; ?></div>
							<? } ?>
						</div>
					</div>
				</div>
				<?
				echo($count++ % $cols == 1 ? "<div class=\"clear\"></div>" : "");
			}
		} else {
			?>
			<div id="no_search_results">Sorry, there were no products found matching your criteria.</div>
		<? } ?>
	</div>

</div>

<script type="text/javascript">
	function open_product_view(href) {
		var sc = $('#search_content .product_list');
		sc.data('orig_height', sc.data('orig_height') || sc.height());
		sc.data('href', href);
		sc.height(440);
		function cbox_closed_call() {
			sc.height(sc.data('orig_height'));
		}

		left = $('#container').offset().left;
		top = $('#content').offset().top;
		$.colorbox({href: href, width: 983, height: 700, top: 100, opacity: .3, left: left, onComplete: cbox_opened, close: "Back to Search Results", onClosed: cbox_closed_call});
	}
	function open_current_product() {
		open_product_view($('#search_content .product_list').data('href'));
	}
	function cbox_opened() {
		$("#cboxBottomRight").html('Back to Search Results')
			.css({color: '#38B0E3', 'text-indent': '-58%', 'font-size': '15px', cursor: 'pointer', 'padding-top': 5 })
			.click(function () {
				$.colorbox.close();
			});
	}
</script>
