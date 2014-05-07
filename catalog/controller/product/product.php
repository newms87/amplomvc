<?php

class Catalog_Controller_Product_Product extends Controller
{
	public function index()
	{
		//Get Product Information
		$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;

		if ($product_id) {
			$product = $this->Model_Catalog_Product->getProduct($product_id);
		}

		//Redirect if requested product was not found
		if (empty($product)) {
			redirect('error/not_found');
		}

		//Layout Override (only if set)
		$layout_id = $this->Model_Catalog_Product->getProductLayoutId($product_id);

		if ($layout_id) {
			$this->config->set('config_layout_id', $layout_id);
		}

		//Page Head
		$this->document->setTitle($product['name']);
		$this->document->setDescription($product['meta_description']);
		$this->document->setKeywords($product['meta_keywords']);

		//Build Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add($product['name'], site_url('product/product', 'product_id=' . $product_id));

		//Product Configs
		$product['is_purchasable'] = $this->cart->productPurchasable($product);
		$product['show_model']     = option('config_show_product_model');
		$product['show_reviews']   = option('config_review_status');
		$product['show_sharing']   = option('config_share_status');
		$product['show_price']     = option('config_customer_hide_price') && !$this->customer->isLogged();
		$product['show_tax']       = option('config_show_price_with_tax');

		//Manufacturer
		$manufacturer = $this->Model_Catalog_Manufacturer->getManufacturer($product['manufacturer_id']);

		if ($manufacturer && option('configbreadcrumbs_show_manufacturer')) {
			$this->breadcrumb->add($manufacturer['name'], site_url('product/manufacturer/product', 'manufacturer_id=' . $product['manufacturer_id']));
		}

		$product['manufacturer'] = $manufacturer;

		//Category
		$category = $this->Model_Catalog_Category->getCategory($product['category_id']);

		if (!$category) {
			$category = array(
				'category_id' => 0,
				'name'        => '',
			);
		}

		$product['category'] = $category;

		//Stock
		$stock_type = option('config_stock_display');

		$stock_classes = array(
			'hidden'      => '',
			'unavailable' => _l("currently not available"),
			'empty'       => $product['stock_status'],
			'available'   => _l("In Stock"),
			'surplus'     => _l("More than %d available", (int)$stock_type),
			'limited'     => _l("Only %d left!", (int)$product['quantity']),
		);

		if ($stock_type === 'hide') {
			$stock_class = 'hidden';
		} elseif (!$product['is_purchasable']) {
			$stock_class = 'unavailable';
		} elseif ($product['quantity'] <= 0) {
			$stock_class = 'empty';
		} else {
			if ($stock_type === 'status') {
				$stock_class = 'available';
			} else {
				$stock_class = (int)$product['quantity'] > (int)$stock_type ? 'surplus' : 'limited';
			}
		}

		$product['stock_class'] = array($stock_class => $stock_classes[$stock_class]);

		//Product Price
		$product['formatted_price'] = $this->currency->format($product['price']);

		if ((float)$product['special']) {
			$product['formatted_special'] = $this->currency->format($product['special']);
		}

		$product['tax'] = $this->currency->format($this->tax->calculate((float)$product['special'] ? $product['special'] : $product['price'], $product['tax_class_id']));

		//Discounts
		$discounts = $this->Model_Catalog_Product->getProductDiscounts($product['product_id']);

		foreach ($discounts as &$discount) {
			$product['discounts'][] = array(
				'quantity' => $discount['quantity'],
				'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product['tax_class_id']))
			);
		}
		unset($discount);

		$product['discounts'] = $discounts;

		//customers must order at least 1 of this product
		$product['minimum'] = max((int)$product['minimum'], 1);

		//Shipping & Return Policies
		$product['shipping_policy'] = $this->cart->getShippingPolicy($product['shipping_policy_id']);
		$product['return_policy']   = $this->cart->getReturnPolicy($product['return_policy_id']);
		$product['is_final']        = $product['return_policy']['days'] < 0;

		//Product Images
		$image_width             = option('config_image_thumb_width');
		$image_height            = option('config_image_thumb_height');
		$image_popup_width       = option('config_image_popup_width');
		$image_popup_height      = option('config_image_popup_height');
		$image_additional_width  = option('config_image_additional_width');
		$image_additional_height = option('config_image_additional_height');

		if ($product['image']) {
			$product['popup'] = $this->image->resize($product['image'], $image_popup_width, $image_popup_height);
			$product['thumb'] = $this->image->resize($product['image'], $image_width, $image_height);
		}

		$image_list = $this->Model_Catalog_Product->getProductImages($product['product_id']);

		//Add the main product image as the first image
		if (!empty($image_list)) {
			array_unshift($image_list, $product['image']);
		}

		$images = array();

		foreach ($image_list as $image) {
			$small_image = $this->image->resize($image, $image_width, $image_height);

			if ($small_image) {
				$popup_image = $this->image->resize($image, $image_popup_width, $image_popup_height);

				$images[] = array(
					'rel'   => "{gallery:'gal1', smallimage:'$small_image', largeimage:'$popup_image'}",
					'popup' => $popup_image,
					'thumb' => $this->image->resize($image, $image_additional_width, $image_additional_height),
				);
			}
		}

		$product['images'] = $images;

		//Template Data
		if (option('config_show_product_attributes')) {
			$product['attribute_groups'] = $this->Model_Catalog_Product->getProductAttributes($product['product_id']);
		}

		//The Tags associated with this product
		$tags = $this->Model_Catalog_Product->getProductTags($product['product_id']);

		foreach ($tags as &$tag) {
			$url_query = array(
				'filter' => array(
					'tag' => $tag['text'],
				),
			);

			$tag['href'] = site_url('product/search', $url_query);
		}
		unset($tag);

		$product['tags'] = $tags;

		//The Template
		if ($product['template']) {
			$template = $product['template'];
		} elseif ($product['product_class_id']) {
			$template = $this->Model_Catalog_Product->getClassTemplate($product['product_class_id']);
		} else {
			$template = 'product/product';
		}

		//Render
		$this->response->setOutput($this->render($template, $product));
	}
}
