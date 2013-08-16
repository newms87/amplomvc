<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<style>
		html {
			background: white;
		}

		body {
			background: white;
			font-family: Arial, Sans-serif !important;
		}

		#bcshop_ad {
			width: 620px;
			margin: auto;
			position: relative;
		}

		#bcshop_ad a {
			color: #ed2d82;
			border: none !important;
			text-decoration: none !important;
		}

		#bcshop_ad a:hover {
			color: #999;
		}

		#bcshop_ad img {
			border: none;
		}

		.bcshop_featured_product_clickable {
			cursor: pointer;
			display: block;
			float: left;
			width: 176px;
			border: 1px solid #e7e6e6;
			color: #999;
			padding: 5px;
			margin-left: 15px;
			margin-bottom: 20px;
			text-decoration: none;
			position: relative;
			background: #DDD;
		}

		.bcshop_featured_product_clickable:hover {
			background: #EEE;
		}

		.bcshop_featured_product_clickable.first_col {
			margin-left: 0;
		}

		#bcshop_become_designer {
			position: absolute;
			top: 0;
			right: 20px;
			width: 300px;
			color: gray;
		}

		#bcshop_product_list {
			overflow: auto;
		}

		.bcshop_image {
			background: white;
			text-align: center;
		}

		.bcshop_image img {
			border: none;
			padding: 0;
		}

		.bcshop_featured_product_info {
			padding-top: 6px;
			padding-left: 5px;
			color: #959595;
		}

		.bcshop_featured_product_info .bcshop_name {
			font-size: 14px;
			height: 30px;
			color: black;
			overflow: hidden;
		}

		.bcshop_featured_product_info .bcshop_price {
			padding-top: 5px;
			margin: 0;
			font-weight: bold;
			color: #ed2d82;
			font-size: 18px;
		}

		.bcshop_featured_product_info .bcshop_price_retail {
			font-size: 16px;
			color: #959595;
			text-decoration: line-through;
		}
	</style>
</head>

<div id='bcshop_ad'>
	<div id='bcshop_top'>
		<a href='<?= $shop_url; ?>' target="_blank">
			<img src="<?= $shop_logo; ?>"/>
		</a>

		<div id='bcshop_become_designer'><?= $text_become_designer; ?></div>
	</div>

	<div id='bcshop_product_list'>
		<? foreach ($products as $product) { ?>
			<a href='<?= $product['href']; ?>' class='bcshop_featured_product_clickable' target="_blank">
				<div class="bcshop_image"><img src="<?= $product['thumb']; ?>" alt="<?= $product['name']; ?>"/></div>
				<div class='bcshop_featured_product_info'>
					<div class="bcshop_name"><?= $product['title']; ?></div>
					<div class="bcshop_price">
						<span class="bcshop_price"><?= $product['price']; ?></span>
						<? if (isset($product['retail'])) { ?>
							<span class="bcshop_price_retail"><?= $product['retail']; ?> retail</span>
						<? } ?>
					</div>
				</div>
			</a>
		<? } ?>
	</div>
</div>
</body>
</html>