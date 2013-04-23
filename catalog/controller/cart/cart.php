<?php 
class ControllerCartCart extends Controller {
	
	public function index() {
	   $this->template->load('cart/cart');
      
	   $this->language->load('cart/cart');

      $this->document->setTitle($this->_('heading_title'));

      $this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
      $this->breadcrumb->add($this->_('heading_title'), $this->url->link('cart/cart'));
      
      $this->data['block_cart'] = $this->getBlock('cart', 'cart');
      
      if ($this->template->option('show_cart_weight')) {
         $this->data['weight'] = $this->weight->format($this->cart->getWeight(), $this->config->get('config_weight_class_id'), $this->language->getInfo('decimal_point'), $this->language->getInfo('thousand_point'));
      }
      
      if($this->config->get('coupon_status')){
         $this->data['block_coupon'] = $this->getBlock('cart','coupon');
      }
      $this->data['show_coupon'] = $this->template->option('show_coupon');
      
      if($this->config->get('voucher_status')){
         $this->data['block_voucher'] = $this->getBlock('cart', 'voucher');
      }
      $this->data['show_voucher'] = $this->template->option('show_voucher');
      
      if($this->config->get('reward_status') && $this->customer->getRewardPoints() && $this->cart->getTotalPoints() > 0){   
         $this->data['block_reward'] = $this->getBlock('cart', 'reward');
      }
      $this->data['show_reward'] = $this->template->option('show_reward');
      
      if($this->config->get('shipping_status') && $this->cart->hasShipping()){  
         $this->data['block_shipping'] = $this->getBlock('cart', 'shipping');
      }
      $this->data['show_shipping'] = $this->template->option('show_shipping');
      
      $this->data['block_total'] = $this->getBlock('cart', 'total');
      
      if(isset($_GET['redirect']) && preg_match("/route=cart\/cart/",$_GET['redirect']) == 0){
         $this->data['continue'] = urldecode($_GET['redirect']);
      }
      else{
         $this->data['continue'] = $this->url->link('common/home');
      }
                        
      $this->data['checkout'] = $this->url->link('checkout/checkout');
      
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
								
	public function add() {
		$this->language->load('cart/cart');
		
		$product_id = isset($_POST['product_id']) ? $_POST['product_id'] : 0;
		$quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 1;
		$options = isset($_POST['selected']) ? array_filter($_POST['selected']) : array();
      $load_page = isset($_POST['load_page']);
      
      $this->cart->add($product_id, $quantity, $options, $load_page);
      
      if($load_page){
         $this->index();
      }
      else{
         $json = array();
         
   		if (!$this->cart->has_error('add')) {
   		   $name = $this->model_catalog_product->getProductName($product_id);
            
   			$redirect = urlencode($this->url->link('product/product', 'product_id=' . $product_id));
            
   			$json['success'] = sprintf($this->_('text_success'), $this->url->link('product/product', 'product_id=' . $product_id), $name, $this->url->link('cart/cart',"redirect=$redirect"));
   			
   			$total_data = $this->cart->getTotals();
   			
   			$json['total'] = sprintf($this->_('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total_data['total']));
   		} else {
   			$errors = $this->cart->get_errors();
            $json['error'] = $errors['add'];
   		}
   		
   		$this->response->setOutput(json_encode($json));
      }
	}
}