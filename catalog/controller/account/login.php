<?php 
class ControllerAccountLogin extends Controller {
	
	public function index() {
		$this->template->load('account/login');

		// Login override for admin users
		if (!empty($_COOKIE['customer_token'])) {
			$this->customer->logout();
			
			$customer_info = $this->model_account_customer->getCustomerByToken($_COOKIE['customer_token']);
			
		 	if ($customer_info && $this->customer->login($customer_info['email'], '', true)) {
				$this->redirect($this->url->link('account/account')); 
			}
		}
		
		if ($this->customer->isLogged()) {
      	$this->redirect($this->url->link('account/account'));
    	}
	
    	$this->language->load('account/login');

    	$this->document->setTitle($this->_('heading_title'));
								
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			unset($this->session->data['guest']);
			
			// Added strpos check to pass McAfee PCI compliance test (http://forum.opencart.com/viewtopic.php?f=10&t=12043&p=151494#p151295)
			if (isset($_POST['redirect']) && (strpos($_POST['redirect'], HTTP_SERVER) !== false || strpos($_POST['redirect'], HTTPS_SERVER) !== false)) {
				$this->redirect(str_replace('&amp;', '&', $_POST['redirect']));
			} else {
				$this->redirect($this->url->link('account/account')); 
			}
    	}  
		
      $this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
      $this->breadcrumb->add($this->_('text_account'), $this->url->link('account/account'));
      $this->breadcrumb->add($this->_('text_login'), $this->url->link('account/login'));
		
		$this->data['action'] = $this->url->link('account/login');
		$this->data['register'] = $this->url->link('account/register');
		$this->data['forgotten'] = $this->url->link('account/forgotten');

    	// Added strpos check to pass McAfee PCI compliance test (http://forum.opencart.com/viewtopic.php?f=10&t=12043&p=151494#p151295)
		if (isset($_POST['redirect']) && (strpos($_POST['redirect'], HTTP_SERVER) !== false || strpos($_POST['redirect'], HTTPS_SERVER) !== false)) {
			$this->data['redirect'] = $_POST['redirect'];
		} elseif (isset($this->session->data['redirect'])) {
      		$this->data['redirect'] = $this->session->data['redirect'];
	  		
			unset($this->session->data['redirect']);
    	} else {
			$this->data['redirect'] = '';
		}

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
  
  	private function validate() {
    	if (!$this->customer->login($_POST['email'], $_POST['password'])) {
      		$this->error['warning'] = $this->_('error_login');
    	}
	
    	return $this->error ? false : true;
  	}
}
