<?php  
class ControllerCheckoutBlockLogin extends Controller { 
	public function index() {
		$this->template->load('checkout/block/login');
		$this->language->load('checkout/block/login');
		
		if(isset($_POST['username'])){
			$this->validate();
		}
		
		$this->data['guest_checkout'] = ($this->config->get('config_guest_checkout') && !$this->config->get('config_customer_price') && !$this->cart->hasDownload());
		
		if (isset($this->session->data['account'])) {
			$this->data['account'] = $this->session->data['account'];
		} else {
			$this->data['account'] = 'register';
		}
		
		$janrain_args = array(
			'login_redir'	=> $this->url->link('checkout/checkout'),
			'display_type'	=> 'popup',
			'icon_size'		=> 'large'
		);
		
		$this->data['rpx_login'] = $this->getChild('module/janrain', $janrain_args);
		
		$defaults = array(
			'username' => '',
		);
		
		foreach($defaults as $key => $default){
			if(isset($_POST[$key])){
				$this->data[$key] = $_POST[$key];
			} else {
				$this->data[$key] = $default;
			}
		}
		
		$this->data['validate_login'] = $this->url->link('checkout/block/login/validate');
		
		$this->data['url_forgotten'] = $this->url->link('account/forgotten');
		
		$this->response->setOutput($this->render());
	}
	
	public function validate() {
		$this->language->load('checkout/block/login');
		
		if ($this->customer->login($_POST['username'], $_POST['password'])) {
			unset($this->session->data['guest']);
			$this->message->add('success', $this->_('text_login_success'));
		} else {
			$this->message->add('warning', $this->_('error_login'));
		}
		
		if (!$this->cart->validate()) {
			$this->url->redirect($this->url->link('cart/cart'));
		}	
		
		$this->url->redirect($this->url->link('checkout/checkout'));
	}
}
