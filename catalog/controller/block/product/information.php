<?php
class Catalog_Controller_Block_Product_Information extends Controller 
{
	public function index($settings)
	{
		if (empty($settings['product_info'])) {
			return;
		}
		
		$product_info = $settings['product_info'];
		
		$this->language->load('block/product/information');
		$this->template->load('block/product/information');
		
		$this->data = $product_info;
		
		$this->data['url_manufacturer'] = $this->url->link('manufacturer/manufacturer', 'manufacturer_id=' . $product_info['manufacturer_id']);
		
		$this->data['is_purchasable'] = $this->cart->productPurchasable($product_info);
		$this->data['display_model'] = $this->config->get('config_show_product_model');
		
		if ($this->data['is_purchasable']) {
			//The Product Options Block
			$this->data['block_product_options'] = $this->getBlock('product/options', array('product_id' => $product_info['product_id']));
		}
		
		$show_related = $this->config->get('config_show_product_related');
		
		if ($show_related > 1 || ($show_related == 1 && !$this->data['is_purchasable'])) {
			$ps_params = array(
				'product_info' => $product_info,
				'limit' => 4
			);
			
			$this->data['block_product_suggestions'] = $this->getBlock('product/suggestions', $ps_params);
		}
		
		//Stock
		$stock_type = $this->config->get('config_stock_display');
		
		if ($stock_type == 'hide') {
			$this->data['stock_type'] = "";
		}
		elseif (!$this->data['is_purchasable']) {
			$this->data['stock'] = $this->_('text_stock_inactive');
		}
		elseif ($product_info['quantity'] <= 0) {
			$this->data['stock'] = $product_info['stock_status'];
		} else {
			if ($stock_type == 'status') {
				$this->data['stock'] = $this->_('text_instock');
			}
			elseif ((int)$product_info['quantity'] > (int)$stock_type) {
				$this->data['stock'] = $this->_('text_more_stock', (int)$stock_type);
			}
			elseif ((int)$product_info['quantity'] <= (int)$stock_type) {
				$this->data['stock'] = $this->_('text_less_stock', (int)$product_info['quantity']);
			}
		}
		
		if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
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
				'price'	=> $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id']))
			);
		} unset($discount);
		
		$this->data['discounts'] = $discounts;
		
		//customers must order at least 1 of this product
		$this->data['minimum'] = $product_info['minimum'] ? $product_info['minimum'] : 1;
		
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
		$this->data['return_policy'] = $this->cart->getReturnPolicy($product_info['return_policy_id']);
		
		$this->data['default_shipping_policy'] = $this->config->get('config_default_shipping_policy') == $product_info['shipping_policy_id'];
		$this->data['default_return_policy'] = $this->config->get('config_default_return_policy') == $product_info['return_policy_id'];
		
		if ($this->data['return_policy']['days'] < 0) {
			$this->data['is_final_explanation'] = $this->_('final_sale_explanation', $this->url->link('information/information/shipping_return_policy','product_id=' . $product_info['product_id']));
		}

		//Links
		$this->_('text_view_more', $this->url->link('product/category', 'category_id=' . $product_info['category']['category_id']), $product_info['category']['name']);
		$this->_('text_keep_shopping', $this->url->link('product/category'));
		$this->data['continue_shopping_link'] = $this->breadcrumb->get_prev_url();
		
		$this->data['view_cart_link'] = $this->url->link('cart/cart');
		$this->data['checkout_link'] = $this->url->link('checkout/checkout');
		
		$this->_('error_add_to_cart', $this->config->get('config_email'));
		
		//Ajax Urls
		$this->data['url_add_to_cart'] = $this->url->ajax('cart/cart/add');
		
		//Render
		$this->render();
	}
}
