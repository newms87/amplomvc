<?php  
class ControllerCheckoutBlockLogin extends Controller { 
	public function index() {
      $this->template->load('checkout/block/login');

		$this->language->load('checkout/checkout');
		
		$this->data['guest_checkout'] = ($this->config->get('config_guest_checkout') && !$this->config->get('config_customer_price') && !$this->cart->hasDownload());
		
		if (isset($this->session->data['account'])) {
			$this->data['account'] = $this->session->data['account'];
		} else {
			$this->data['account'] = 'register';
		}
		
		$this->data['rpx_login'] = $this->getChild('module/janrain',array('login_redir'=>$this->url->link('checkout/checkout'),'display_type'=>'popup','icon_size'=>'large'));
      
      //Build the Login Form
      $form = $this->template->get_form('login');
      
      $form->set_template('form/table');
      
      $form->set_field_value('password', 'content_after', '<br /><a href="' . $this->url->link('account/forgotten') . '">' . $this->_('text_forgotten') . '</a><br /><br />');
      
      $form->set_field_value('submit', 'display_name', $this->_('button_login'));
      
      $this->data['form_login'] = $form->build();
		
      
		$this->response->setOutput($this->render());
	}
	
	public function validate() {
		$this->language->load('checkout/checkout');
		
      if ($this->customer->login($_POST['email'], $_POST['password'])) {
         unset($this->session->data['guest']);
         $this->message->add('success', $this->_('text_login_success'));
      } else {
         $this->message->add('warning', $this->_('error_login'));
      }
      
		if (!$this->cart->validate()) {
			$this->redirect($this->url->link('cart/cart'));
		}	
      
      $this->redirect($this->url->link('checkout/checkout'));
	}
}
