<?php

class Catalog_Controller_Product_Product extends Controller
{
	public function index()
	{
		//Get Product Information
		$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;

		if ($product_id) {
			$product_info = $this->Model_Catalog_Product->getProduct($product_id);
		}

		//Redirect if requested product was not found
		if (empty($product_info)) {
			redirect('error/not_found');
		}

		$data = $product_info;

		//Layout Override (only if set)
		$layout_id = $this->Model_Catalog_Product->getProductLayoutId($product_id);

		if ($layout_id) {
			$this->config->set('config_layout_id', $layout_id);
		}

		$data['product_id'] = $product_id;

		//Page Head
		$this->document->setTitle($product_info['name']);
		$this->document->setDescription($product_info['meta_description']);
		$this->document->setKeywords($product_info['meta_keywords']);

		//Page Title
		$data['page_title'] = $product_info['name'];

		//Build Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));

		$manufacturer = $this->Model_Catalog_Manufacturer->getManufacturer($product_info['manufacturer_id']);

		if ($manufacturer && $this->config->get('config_breadcrumbs_show_manufacturer')) {
			$this->breadcrumb->add($manufacturer['name'], site_url('product/manufacturer/product', 'manufacturer_id=' . $product_info['manufacturer_id']));
		}

		$product_info['category'] = $this->Model_Catalog_Category->getCategory($product_info['category_id']);

		if (!$product_info['category']) {
			$product_info['category'] = array(
				'category_id' => 0,
				'name'        => '',
			);
		}

		$this->breadcrumb->add($product_info['name'], site_url('product/product', 'product_id=' . $product_info['product_id']));

		//Product Information
		$data['manufacturer']     = $manufacturer;
		$data['url_manufacturer'] = site_url('manufacturer/manufacturer', 'manufacturer_id=' . $product_info['manufacturer_id']);

		$data['is_purchasable'] = $this->cart->productPurchasable($product_info);
		$data['display_model']  = $this->config->get('config_show_product_model');

		if ($data['is_purchasable']) {
			//The Product Options Block
			$data['block_product_options'] = $this->block->render('product/options', null, array('product_id' => $product_info['product_id']));
		}

		//Stock
		$stock_type = $this->config->get('config_stock_display');

		if ($stock_type === 'hide') {
			$data['stock_type']  = "";
			$data['stock_class'] = 'hidden';
		} elseif (!$data['is_purchasable']) {
			$data['stock']       = _l("currently not available");
			$data['stock_class'] = 'unavailable';
		} elseif ($product_info['quantity'] <= 0) {
			$data['stock']       = $product_info['stock_status'];
			$data['stock_class'] = 'stock_empty';
		} else {
			if ($stock_type === 'status') {
				$data['stock']       = _l("In Stock");
				$data['stock_class'] = 'available';
			} elseif ((int)$product_info['quantity'] > (int)$stock_type) {
				$data['stock']       = _l("More than %d available", (int)$stock_type);
				$data['stock_class'] = 'surplus';
			} elseif ((int)$product_info['quantity'] <= (int)$stock_type) {
				$data['stock']       = _l("Only %d left!", (int)$product_info['quantity']);
				$data['stock_class'] = 'limited_qty';
			}
		}

		if (($this->config->get('config_customer_hide_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_hide_price')) {
			$data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id']));
		} else {
			$data['price'] = false;
		}

		if ((float)$product_info['special']) {
			$data['special'] = $this->currency->format($product_info['special'], $product_info['tax_class_id']);
		}

		if ($this->config->get('config_show_price_with_tax')) {
			$data['tax'] = $this->currency->format($this->tax->calculate((float)$product_info['special'] ? $product_info['special'] : $product_info['price']));
		}

		$discounts = $this->Model_Catalog_Product->getProductDiscounts($product_info['product_id']);

		foreach ($discounts as &$discount) {
			$data['discounts'][] = array(
				'quantity' => $discount['quantity'],
				'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id']))
			);
		}
		unset($discount);

		$data['discounts'] = $discounts;

		//customers must order at least 1 of this product
		$data['minimum'] = max((int)$product_info['minimum'], 1);

		//Product Review
		if ($this->config->get('config_review_status')) {
			$data['block_review'] = $this->block->render('product/review');
		}

		//Social Sharing
		if ($this->config->get('config_share_status')) {
			$data['block_sharing'] = $this->block->render('extras/sharing');
		}

		//Shipping & Return Policies
		$data['shipping_policy'] = $this->cart->getShippingPolicy($product_info['shipping_policy_id']);
		$data['return_policy']   = $this->cart->getReturnPolicy($product_info['return_policy_id']);

		$data['is_default_shipping_policy'] = $product_info['shipping_policy_id'] == $this->config->get('config_default_shipping_policy');
		$data['is_default_return_policy']   = $product_info['return_policy_id'] == $this->config->get('config_default_return_policy');

		if ($data['return_policy']['days'] < 0) {
			$data['is_final_explanation'] = _l("A Product Marked as <span class='final_sale'></span> cannot be returned. Read our <a href=\"%s\" onclick=\"return colorbox($(this));\">Return Policy</a> for details.", site_url('information/information/shipping_return_policy', 'product_id=' . $product_info['product_id']));
		}

		//Links
		$product_info['category']['url'] = site_url('product/category', 'category_id=' . $product_info['category']['category_id']);
		$data['category']                = $product_info['category'];

		$data['keep_shopping']          = site_url('product/category');
		$data['view_cart_link']         = site_url('cart/cart');
		$data['checkout_link']          = site_url('checkout/checkout');
		$data['continue_shopping_link'] = $this->breadcrumb->get_prev_url();

		//Product Images
		$image_width             = $this->config->get('config_image_thumb_width');
		$image_height            = $this->config->get('config_image_thumb_height');
		$image_popup_width       = $this->config->get('config_image_popup_width');
		$image_popup_height      = $this->config->get('config_image_popup_height');
		$image_additional_width  = $this->config->get('config_image_additional_width');
		$image_additional_height = $this->config->get('config_image_additional_height');

		if ($product_info['image']) {
			$data['popup'] = $this->image->resize($product_info['image'], $image_popup_width, $image_popup_height);
			$data['thumb'] = $this->image->resize($product_info['image'], $image_width, $image_height);
		}

		$image_list = $this->Model_Catalog_Product->getProductImages($product_info['product_id']);

		//Add the main product image as the first image
		if (!empty($image_list)) {
			array_unshift($image_list, $product_info['image']);
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

		$data['images'] = $images;

		//Template Data
		if ($this->config->get('config_shipping_return_info_id')) {
			$data['data_policies'] = site_url('information/information/info', 'information_id=' . $this->config->get('config_shipping_return_info_id'));
		}

		if ($this->config->get('config_show_product_attributes')) {
			$data['data_attribute_groups'] = $this->Model_Catalog_Product->getProductAttributes($product_info['product_id']);
		}

		//Related Products
		$show_related = $this->config->get('config_show_product_related');

		if ($show_related > 1 || ($show_related == 1 && !$this->cart->productPurchasable($product_info))) {
			$ps_params = array(
				'product_info' => $product_info,
				'limit'        => 4
			);

			//TODO: Move product/suggestions to product/related...
			$data['block_product_related'] = $this->block->render('product/suggestions', null, $ps_params);
		}

		//The Tags associated with this product
		$tags = $this->Model_Catalog_Product->getProductTags($product_info['product_id']);

		foreach ($tags as &$tag) {
			$url_query = array(
				'filter' => array(
					'tag' => $tag['text'],
				),
			);

			$tag['href'] = site_url('product/search', $url_query);
		}

		$data['tags'] = $tags;

		if ($product_info['template'] == 'product_video') {
			$data['description'] = html_entity_decode($product_info['description']);
		}

		//Action Buttons
		$data['buy_now'] = site_url('cart/cart/buy_now');

		//The Template
		if ($product_info['template']) {
			$template = $product_info['template'];
		} elseif ($product_info['product_class_id']) {
			$template = $this->Model_Catalog_Product->getClassTemplate($product_info['product_class_id']);
		} else {
			$template = 'product/product';
		}

		//Render
		$this->response->setOutput($this->render($template, $data));
	}
}
