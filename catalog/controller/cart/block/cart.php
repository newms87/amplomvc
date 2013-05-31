<?php
class Catalog_Controller_Cart_Block_Cart extends Controller
{
		
	public function index($settings = null, $ajax_cart = true)
	{
		$this->language->load('cart/block/cart');
		
		if (!isset($this->session->data['vouchers'])) {
			$this->session->data['vouchers'] = array();
		}
		
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
			elseif (strpos($_POST['action'], 'remove') === 0) {
				$key = substr($_POST['action'], 6);
		
				if (isset($this->session->data['vouchers'][$key])) {
					unset($this->session->data['vouchers'][$key]);
					
					$this->message->add('success', $this->language->format('text_remove', $this->_('text_voucher')));
				}
				else {
					$id = $this->cart->getProductId($key);
					$name = $this->cart->getProductName($key);
					
					$this->cart->remove($key);
					
					$this->message->add('success', $this->language->format('text_remove', $this->url->link('product/product', 'product_id=' . $id), $name));
				}
			}
			
			unset($this->session->data['reward']);
		}
		
		if (!$this->cart->isEmpty()) {
			
			$this->language->format('final_sale_explanation',$this->url->link('information/information/info','information_id=7'));
			
			if ($ajax_cart) {
				$this->data['action'] = $this->url->link('cart/block/cart/ajax_cart');
				
				$this->data['messages'] = $this->message->fetch();
			}
			else {
				$this->data['action'] = '';
			}
						
			if ($this->config->get('config_customer_price') && !$this->customer->isLogged()) {
				$this->data['no_price_display'] = $this->language->format('text_login', $this->url->link('account/login'), $this->url->link('account/register'));
			}
			
			if (!$this->cart->validate()) {
				$this->error = $this->cart->get_errors(null, true);
			}
			
			$products = $this->cart->getProducts();
			
			foreach ($products as &$product) {
				if ($product['image']) {
					$product['thumb'] = $this->image->resize($product['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
				}

				$option_data = array();

				foreach ($product['option'] as &$option) {
					$option['value'] = $this->tool->limit_characters($option['option_value'], 20);
				}
				
				
				$product['price'] = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class _id']));
			
				$product['total'] = $this->currency->format($this->tax->calculate($product['total'], $product['tax_class_id']));
				
				if ($product['reward']) {
					$product['reward'] = sprintf($this->_('text_points'), $product['reward']);
				}
				
				$product['href'] = $this->url->link('product/product', 'product_id=' . $product['product_id']);
				$product['remove'] = $this->url->link('cart/block/cart', 'remove=' . $product['key']);
			}

			$this->data['products'] = $products;
			
			// Gift Voucher
			$this->data['vouchers'] = array();
			
			if (!empty($this->session->data['vouchers'])) {
				foreach ($this->session->data['vouchers'] as $key => $voucher) {
					$this->data['vouchers'][$key] = $voucher;
					$this->data['vouchers']['amount'] = $this->currency->format($voucher['amount']);
					$this->data['vouchers']['remove'] = $this->url->link('cart/cart', 'remove=' . $key);
				}
			}
			
			$this->data['ajax_cart'] = $ajax_cart;
			
			$this->response->setOutput($this->render());
			
		} else {
			$this->response->setOutput(false);
		}
	}

	public function ajax_cart()
	{
		$this->index(array(), true);
	}
}