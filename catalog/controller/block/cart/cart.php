<?php
class Catalog_Controller_Block_Cart_Cart extends Controller
{
	public function index($settings = array())
	{
		//Template and Language
		$this->template->load('block/cart/cart');
		$this->language->load('block/cart/cart');
		
		$ajax_cart = isset($settings['ajax_cart']) ? $settings['ajax_cart'] : true;
		
		//Update Product
		if (isset($_POST['cart_form'])) {
			if ($_POST['action'] == 'update') {
				if (!empty($_POST['quantity'])) {
					foreach ($_POST['quantity'] as $key => $value) {
						$this->cart->update($key, $value);
					}
					
					$this->message->add('success', $this->_('text_update'));
				}
			}
		}
		elseif (!empty($_GET['remove'])) {
			$product_index = $_GET['remove'];
			
			$id = $this->cart->getProductId($product_index);
			$name = $this->cart->getProductName($product_index);
			
			$this->cart->remove($product_index);
			
			$this->message->add('success', $this->_('text_remove', $this->url->link('product/product', 'product_id=' . $id), $name));
		}
		elseif (!empty($_GET['removevoucher'])) {
			$this->cart->removeVoucher($_GET['removevoucher']);
			
			$this->message->add('success', $this->_('text_remove', $this->_('text_voucher')));
		}
		
		if (!$this->cart->isEmpty()) {
			//Check if the shipping estimate was invalidated and that we are not in the checkout process
			// -> update the shipping estimate to the first Shipping option
			if (!$this->order->hasOrder() && $this->cart->hasShippingMethod()) {
				echo 'order inactive';
				$shipping_methods = $this->cart->getShippingMethods();
				
				if (!empty($shipping_methods) && !isset($shipping_methods[$this->cart->getShippingMethodId()])) {
					$this->cart->setShippingMethod(key($shipping_methods));
				}
			}
			
			if ($ajax_cart) {
				$this->data['action'] = $this->url->ajax('block/cart/cart');
				
				$this->data['messages'] = $this->message->fetch();
			}
			else {
				$this->data['action'] = $this->url->here();
			}
			
			if ($this->config->get('config_customer_price') && !$this->customer->isLogged()) {
				$this->data['no_price_display'] = $this->_('text_login', $this->url->link('account/login'), $this->url->link('account/register'));
			}
			
			if (!$this->cart->validate()) {
				$this->error = $this->cart->get_errors(null, true);
			}
			
			$products = $this->cart->getProducts();
			
			$show_return_policy = $this->config->get('config_cart_show_return_policy');
			
			foreach ($products as &$product) {
				if ($product['image']) {
					$product['thumb'] = $this->image->resize($product['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
				}

				$option_data = array();

				foreach ($product['option'] as &$option) {
					$option['value'] = $this->tool->limit_characters($option['option_value'], 20);
				}
				
				
				$product['price'] = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id']));
				$product['total'] = $this->currency->format($this->tax->calculate($product['total'], $product['tax_class_id']));
				
				if ($product['reward']) {
					$product['reward'] = sprintf($this->_('text_points'), $product['reward']);
				}
				
				if ($show_return_policy) {
					$policy = $this->cart->getReturnPolicy($product['return_policy_id']);
					
					if ($policy['days'] > 0) {
						$product['return_policy'] = $this->_('text_return_days', $policy['days']);
					} elseif ((int)$policy['days'] === 0) {
						$product['return_policy'] = $this->_('text_return_anytime');
					} else {
						$product['return_policy'] = $this->builder->finalSale();
					}
				}
				
				$product['href'] = $this->url->link('product/product', 'product_id=' . $product['product_id']);
				
				if ($ajax_cart) {
					$product['remove'] = $this->url->ajax('block/cart/cart', 'remove=' . $product['key']);
				} else {
					$product['remove'] = $this->url->link('block/cart/cart', 'remove=' . $product['key']);
				}
			} unset($product);

			$this->data['products'] = $products;
			
			// Gift Voucher
			if ($this->cart->hasVouchers()) {
				$vouchers = $this->cart->getVouchers();
				
				foreach ($vouchers as $voucher_id => &$voucher) {
					$voucher['amount'] = $this->currency->format($voucher['amount']);
					
					if ($ajax_cart) {
						$voucher['remove'] = $this->url->ajax('block/cart/cart', 'removevoucher=' . $voucher_id);
					} else {
						$voucher['remove'] = $this->url->link('block/cart/cart', 'removevoucher=' . $voucher_id);
					}
				}
				
				$this->data['vouchers'] = $vouchers;
			}
			
			$this->data['ajax_cart'] = $ajax_cart;
			$this->data['show_return_policy'] = $show_return_policy;
			
			$this->response->setOutput($this->render());
		} else {
			$this->response->setOutput('');
		}
	}
}