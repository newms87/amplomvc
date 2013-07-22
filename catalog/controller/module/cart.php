<?php
class Catalog_Controller_Module_Cart extends Controller
{
	public function index()
	{
		html_backtrace(10);
		
		$this->template->load('module/cart');
		$this->language->load('module/cart');
		
		if (isset($_GET['remove'])) {
			$this->cart->remove($_GET['remove']);
		
			unset($this->session->data['vouchers'][$_GET['remove']]);
		}
			
		// Totals
		$total_data = array();
		
		
		$this->data['totals'] = $this->cart->getTotals();
		
		$this->_('text_items', $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total));
		$this->data['products'] = array();
			
		foreach ($this->cart->getProducts() as $product) {
			if ($product['image']) {
				$image = $this->image->resize($product['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
			} else {
				$image = '';
			}
							
			$option_data = array();
			
			foreach ($product['option'] as $option) {
				if ($option['type'] != 'file') {
					$value = $option['option_value'];
				} else {
					$filename = $this->encryption->decrypt($option['option_value']);
					
					$value = substr($filename, 0, strrpos($filename, '.'));
				}
				
				$option_data[] = array(
					'name'  => $option['name'],
					'value' => (strlen($value) > 20 ? substr($value, 0, 20) . '..' : $value),
					'type'  => $option['type']
				);
			}
			
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id']));
			} else {
				$price = false;
			}

			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$total = $this->currency->format($this->tax->calculate($product['total'], $product['tax_class_id']));
			} else {
				$total = false;
			}
													
			$this->data['products'][] = array(
				'key'		=> $product['key'],
				'thumb'	=> $image,
				'name'	=> $product['name'],
				'model'	=> $product['model'],
				'option'	=> $option_data,
				'quantity' => $product['quantity'],
				'price'	=> $price,
				'total'	=> $total,
				'href'	=> $this->url->link('product/product', 'product_id=' . $product['product_id'])
			);
		}
		
		// Gift Voucher
		$this->data['vouchers'] = array();
		
		if (!empty($this->session->data['vouchers'])) {
			foreach ($this->session->data['vouchers'] as $key => $voucher) {
				$this->data['vouchers'][] = array(
					'key'			=> $key,
					'description' => $voucher['description'],
					'amount'		=> $this->currency->format($voucher['amount'])
				);
			}
		}
					
		$this->data['cart'] = $this->url->link('cart/cart');
						
		$this->data['checkout'] = $this->url->link('checkout/checkout');

		$this->response->setOutput($this->render());
	}
}