<?php  
class ControllerCheckoutCheckout extends Controller { 
	public function index() {
$this->template->load('checkout/checkout');

		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
	  		$this->redirect($this->url->link('cart/cart'));
    	}	
		
		// Validate minimum quantity requirments.			
		$products = $this->cart->getProducts();
				
		foreach ($products as $product) {
			$product_total = 0;
				
			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}		
			
			if ($product['minimum'] > $product_total) {
				$this->redirect($this->url->link('cart/cart'));
			}				
		}
		
      $this->language->load('checkout/checkout');
		
		$this->document->setTitle($this->_('heading_title')); 
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_cart'), $this->url->link('cart/cart'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('checkout/checkout'));

		$this->data['logged'] = $this->customer->isLogged();
		$this->data['shipping_required'] = $this->cart->hasShipping();
		
      $this->language->format('error_page_load', $this->config->get('config_email'));
      
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'	
		);
				
		$this->response->setOutput($this->render());
  	}
}