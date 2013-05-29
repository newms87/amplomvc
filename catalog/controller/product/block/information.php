<?php
class ControllerProductBlockInformation extends Controller 
{
	
	public function index($setting, $product_info)
	{
		
		$this->language->load('product/block/information');
		
		$this->template->load('product/block/information');
		
		$review_status = $this->config->get('config_review_status');
		$share_status = $this->config->get('config_share_status');
		
		$this->data['product_id'] = $product_info['product_id'];
		
		$this->data['review_status'] = $review_status;
		$this->data['share_status'] = $share_status;
		
		$this->data['manufacturer'] = $product_info['manufacturer'];
		$this->data['manufacturer_url'] = $this->url->link('designers/designers', 'designer_id=' . $product_info['manufacturer_id']);
			
		$expiration = $product_info['date_expires'];
		$diff = date_diff(new DateTime(), new DateTime($expiration));
		
		//If the product is not active
		$is_active = (!$diff->invert || $expiration == DATETIME_ZERO) && (int)$product_info['manufacturer_status'] && ($product_info['quantity'] >0);
		
		$this->data['is_active'] = $is_active;
		
		if ($is_active) {
			//The Product Options Block
			$this->data['block_product_options'] = $this->getBlock('product', 'options', array($product_info['product_id']));
		}
		else {
			$ps_params = array(
				'product_info'=>$product_info,
				'limit'=>4
			);
			
			$this->data['block_product_suggestions'] = $this->getBlock('product', 'suggestions', $ps_params);
		}
		
		$this->data['blurb'] = html_entity_decode($product_info['blurb'], ENT_QUOTES, 'UTF-8');
		
		$this->data['model'] = $product_info['model'];
		$this->data['reward'] = $product_info['reward'];
		$this->data['points'] = $product_info['points'];
		
		$this->data['display_model'] = $this->config->get('config_show_product_model');
		
		$stock_type = $this->config->get('config_stock_display');
		
		if ($stock_type == 'hide') {
			$this->data['stock_type'] = "";
		}
		elseif (!$is_active) {
			$this->language->set('stock', $this->_('text_stock_inactive'));
		}
		elseif ($product_info['quantity'] <= 0) {
			$this->data['stock'] = $product_info['stock_status'];
		} else {
			if ($stock_type == 'status') {
				$this->language->set('stock', $this->_('text_instock'));
			}
			elseif ((int)$product_info['quantity'] > (int)$stock_type) {
				$this->data['stock'] = $this->language->format('text_more_stock', (int)$stock_type);
			}
			elseif ((int)$product_info['quantity'] <= (int)$stock_type) {
				$this->data['stock'] = $this->language->format('text_less_stock', (int)$product_info['quantity']);
			}
		}
		
		if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
			$this->data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class _id']));
		} else {
			$this->data['price'] = false;
		}
		
		$this->data['is_final'] = (int)$product_info['is_final'];
		$this->language->format('final_sale_explanation', $this->url->link('information/information/info','information_id=7').'/#return_policy');
		
		if ((float)$product_info['special']) {
			$this->data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class _id']));
		} else {
			$this->data['special'] = false;
		}
		
		if ($this->config->get('config_show_price_with_tax')) {
			$this->data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price']);
		} else {
			$this->data['tax'] = false;
		}
		
		$discounts = $this->model_catalog_product->getProductDiscounts($product_info['product_id']);
		
		$this->data['discounts'] = array();
		
		foreach ($discounts as $discount) {
			$this->data['discounts'][] = array(
				'quantity' => $discount['quantity'],
				'price'	=> $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class _id']))
			);
		}
		
		$this->data['continue_shopping_link'] = $this->breadcrumb->get_prev_url();
		
		$this->data['checkout_link'] = $this->url->link('checkout/checkout');
		
		$this->language->format('error_add_to_cart', $this->config->get('config_email'));
		
		if($product_info['shipping_return'])
{
			$this->data['shipping_return'] = html_entity_decode($product_info['shipping_return'], ENT_QUOTES, 'UTF-8');
			
			$this->data['is_default_shipping'] = trim(strip_tags($this->data['shipping_return'])) == trim($this->_('shipping_return_policy'));
		}
		else {
			$this->data['shipping_return'] = $this->_('shipping_return_policy');
			
			$this->data['is_default_shipping'] = true;
		}
		
		$this->data['shipping_return_popup'] = $this->tool->limit_characters($this->data['shipping_return'], 90) . $this->_('text_read_more');
		
		//customers must order at least 1 of this product
		$this->data['minimum'] = $product_info['minimum'] ? $product_info['minimum'] : 1;
		
		$this->language->format('text_minimum', $product_info['minimum']);
		
		if ($review_status) {
			$this->data['reviews'] = $this->language->format('text_reviews', (int)$product_info['reviews']);
			
			$this->data['rating'] = (int)$product_info['rating'];
		}

		if ($share_status) {
			$this->data['block_sharing'] = $this->getBlock('extras', 'sharing');
		}
		
		$this->render();
	}
}
