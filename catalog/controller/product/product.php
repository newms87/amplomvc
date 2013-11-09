<?php
class Catalog_Controller_Product_Product extends Controller
{
	public function index()
	{
		//Language
		$this->language->load('product/product');

		//Get Product Information
		$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;

		if ($product_id) {
			$product_info = $this->Model_Catalog_Product->getProduct($product_id);
		}

		//Redirect if requested product was not found
		if (empty($product_info)) {
			$this->url->redirect('error/not_found');
		}

		$this->data = $product_info;

		//Layout Override (only if set)
		$layout_id = $this->Model_Catalog_Product->getProductLayoutId($product_id);

		if ($layout_id) {
			$this->config->set('config_layout_id', $layout_id);
		}

		$this->data['product_id'] = $product_id;

		//Build Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));

		$manufacturer_info = $this->Model_Catalog_Manufacturer->getManufacturer($product_info['manufacturer_id']);

		if ($manufacturer_info && $this->config->get('config_breadcrumbs_show_manufacturer')) {
			$this->breadcrumb->add($manufacturer_info['name'], $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $product_info['manufacturer_id']));
		}

		$product_info['category'] = $this->Model_Catalog_Category->getCategory($product_info['category_id']);

		$this->breadcrumb->add($product_info['name'], $this->url->link('product/product', 'product_id=' . $product_info['product_id']));

		//Setup Document
		$this->document->setTitle($product_info['name']);
		$this->document->setDescription($product_info['meta_description']);
		$this->document->setKeywords($product_info['meta_keywords']);

		$this->language->set('head_title', $product_info['name']);

		if ($product_info['template']) {
			$this->template->load('product/' . $product_info['template']);
		} elseif ($product_info['product_class_id']) {
			$this->template->load($this->Model_Catalog_Product->getClassTemplate($product_info['product_class_id']));
		} else {
			$this->template->load('product/product');
		}

		//Product Information
		$this->data['url_manufacturer'] = $this->url->link('manufacturer/manufacturer', 'manufacturer_id=' . $product_info['manufacturer_id']);

		$this->data['is_purchasable'] = $this->cart->productPurchasable($product_info);
		$this->data['display_model']  = $this->config->get('config_show_product_model');

		if ($this->data['is_purchasable']) {
			//The Product Options Block
			$this->data['block_product_options'] = $this->getBlock('product/options', array('product_id' => $product_info['product_id']));
		}

		//Stock
		$stock_type = $this->config->get('config_stock_display');

		if ($stock_type == 'hide') {
			$this->data['stock_type'] = "";
		} elseif (!$this->data['is_purchasable']) {
			$this->data['stock'] = $this->_('text_stock_inactive');
		} elseif ($product_info['quantity'] <= 0) {
			$this->data['stock'] = $product_info['stock_status'];
		} else {
			if ($stock_type == 'status') {
				$this->data['stock'] = $this->_('text_instock');
			} elseif ((int)$product_info['quantity'] > (int)$stock_type) {
				$this->data['stock'] = $this->_('text_more_stock', (int)$stock_type);
			} elseif ((int)$product_info['quantity'] <= (int)$stock_type) {
				$this->data['stock'] = $this->_('text_less_stock', (int)$product_info['quantity']);
			}
		}

		if (($this->config->get('config_customer_hide_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_hide_price')) {
			$this->data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id']));
		} else {
			$this->data['price'] = false;
		}

		if ((float)$product_info['special']) {
			$this->data['special'] = $this->currency->format($product_info['special'], $product_info['tax_class_id']);
		}

		if ($this->config->get('config_show_price_with_tax')) {
			$this->data['tax'] = $this->currency->format($this->tax->calculate((float)$product_info['special'] ? $product_info['special'] : $product_info['price']));
		}

		$discounts = $this->Model_Catalog_Product->getProductDiscounts($product_info['product_id']);

		foreach ($discounts as &$discount) {
			$this->data['discounts'][] = array(
				'quantity' => $discount['quantity'],
				'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id']))
			);
		}
		unset($discount);

		$this->data['discounts'] = $discounts;

		//customers must order at least 1 of this product
		$this->data['minimum'] = max(array(
		                                  (int)$product_info['minimum'],
		                                  1
		                             ));
		$this->_('text_minimum', $product_info['minimum']);

		//Product Review
		if ($this->config->get('config_review_status')) {
			$this->data['block_review'] = $this->getBlock('product/review');
		}

		//Social Sharing
		if ($this->config->get('config_share_status')) {
			$this->data['block_sharing'] = $this->getBlock('extras/sharing');
		}

		//Shipping & Return Policies
		$this->data['shipping_policy'] = $this->cart->getShippingPolicy($product_info['shipping_policy_id']);
		$this->data['return_policy']   = $this->cart->getReturnPolicy($product_info['return_policy_id']);

		$this->data['is_default_shipping_policy'] = $product_info['shipping_policy_id'] == $this->config->get('config_default_shipping_policy');
		$this->data['is_default_return_policy']   = $product_info['return_policy_id'] == $this->config->get('config_default_return_policy');

		if ($this->data['return_policy']['days'] < 0) {
			$this->data['is_final_explanation'] = $this->_('final_sale_explanation', $this->url->link('information/information/shipping_return_policy', 'product_id=' . $product_info['product_id']));
		}

		//Links
		$this->_('text_view_more', $this->url->link('product/category', 'category_id=' . $product_info['category']['category_id']), $product_info['category']['name']);
		$this->_('text_keep_shopping', $this->url->link('product/category'));

		$this->data['view_cart_link']         = $this->url->link('cart/cart');
		$this->data['checkout_link']          = $this->url->link('checkout/checkout');
		$this->data['continue_shopping_link'] = $this->breadcrumb->get_prev_url();

		$this->_('error_add_to_cart', $this->config->get('config_email'));

		//Product Images
		$image_width             = $this->config->get('config_image_thumb_width');
		$image_height            = $this->config->get('config_image_thumb_height');
		$image_popup_width       = $this->config->get('config_image_popup_width');
		$image_popup_height      = $this->config->get('config_image_popup_height');
		$image_additional_width  = $this->config->get('config_image_additional_width');
		$image_additional_height = $this->config->get('config_image_additional_height');

		if ($product_info['image']) {
			$this->data['popup'] = $this->image->resize($product_info['image'], $image_popup_width, $image_popup_height);
			$this->data['thumb'] = $this->image->resize($product_info['image'], $image_width, $image_height);
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

		$this->data['images'] = $images;

		//Additional Information
		if ($this->config->get('config_shipping_return_info_id')) {
			$this->_('text_view_policies', $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_shipping_return_info_id')));
		} else {
			$this->data['text_view_policies'] = '';
		}

		if ($this->config->get('config_show_product_attributes')) {
			$this->data['attribute_groups'] = $this->Model_Catalog_Product->getProductAttributes($product_info['product_id']);
		}

		//Related Products
		$show_related = $this->config->get('config_show_product_related');

		if ($show_related > 1 || ($show_related == 1 && !$this->cart->productPurchasable($product_info))) {
			$ps_params = array(
				'product_info' => $product_info,
				'limit'        => 4
			);

			//TODO: Move product/suggestions to product/related...
			$this->data['block_product_related'] = $this->getBlock('product/suggestions', $ps_params);
		}

		//The Tags associated with this product
		$tags = $this->Model_Catalog_Product->getProductTags($product_info['product_id']);

		foreach ($tags as &$tag) {
			$url_query = array(
				'filter' => array(
					'tag' => $tag['text'],
				),
			);

			$tag['href'] = $this->url->link('product/search', $url_query);
		}

		$this->_('text_on_store', $this->config->get('config_name'));

		$this->data['tags'] = $tags;

		if ($product_info['template'] == 'product_video') {
			$this->data['description'] = html_entity_decode($product_info['description']);
		}

		//Action Buttons
		$this->data['buy_now'] = $this->url->link('cart/cart/buy_now');

		//Dependencies
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);

		//Render
		$this->response->setOutput($this->render());
	}
}
