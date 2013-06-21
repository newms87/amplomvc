<?php
class Catalog_Controller_Account_Register extends Controller 
{
	
  	public function index()
  	{
  		$this->template->load('account/register');
		
		if ($this->customer->isLogged()) {
			$this->url->redirect($this->url->link('account/account'));
		}

		$this->language->load('account/register');
		
		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validate()) {
			$this->Model_Account_Customer->addCustomer($_POST);

			$this->customer->login($_POST['email'], $_POST['password']);
			
			$this->url->redirect($this->url->link('account/success'));
		}
		
		$this->_('text_account_already', $this->url->link('account/login'));
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_account'), $this->url->link('account/account'));
		$this->breadcrumb->add($this->_('text_register'), $this->url->link('account/register'));
		
		$this->data['action'] = $this->url->link('account/register');
		
		$defaults = array(
			'firstname'=>'',
			'lastname'=>'',
			'email'=>'',
			'company'=>'',
			'address_1'=>'',
			'address_2'=>'',
			'postcode'=>'',
			'city'=>'',
			'country_id'=>'',
			'zone_id'=>'',
			'password'=>'',
			'confirm'=>'',
			'newsletter'=>1,
			'agree'=>false
		);
		
		foreach ($defaults as $key=>$default) {
			$this->data[$key] = isset($_POST[$key])?$_POST[$key]:$default;
		}
		
		$this->data['countries'] = $this->Model_Localisation_Country->getCountries();
		
		$this->data['text_agree'] = '';
		
		if ($this->config->get('config_account_id')) {
			$information_info = $this->Model_Catalog_Information->getInformation($this->config->get('config_account_id'));
			
			if ($information_info) {
				$this->_('text_agree', $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_account_id')), $information_info['title'], $information_info['title']);
			}
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

  	public function validate()
  	{
		if ((strlen($_POST['firstname']) < 1) || (strlen($_POST['firstname']) > 32)) {
				$this->error['firstname'] = $this->_('error_firstname');
		}

		if ((strlen($_POST['lastname']) < 1) || (strlen($_POST['lastname']) > 32)) {
				$this->error['lastname'] = $this->_('error_lastname');
		}

		if ((strlen($_POST['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $_POST['email'])) {
				$this->error['email'] = $this->_('error_email');
		}

		if ($this->Model_Account_Customer->getTotalCustomersByEmail($_POST['email'])) {
				$this->error['email'] = $this->_('error_exists');
		}
		
		if ((strlen($_POST['address_1']) < 3) || (strlen($_POST['address_1']) > 128)) {
				$this->error['address_1'] = $this->_('error_address_1');
		}

		if ((strlen($_POST['city']) < 2) || (strlen($_POST['city']) > 128)) {
				$this->error['city'] = $this->_('error_city');
		}

		$country_info = $this->Model_Localisation_Country->getCountry($_POST['country_id']);
		
		if ($country_info && $country_info['postcode_required'] && (strlen($_POST['postcode']) < 2) || (strlen($_POST['postcode']) > 10)) {
			$this->error['postcode'] = $this->_('error_postcode');
		}

		if ($_POST['country_id'] == '') {
				$this->error['country_id'] = $this->_('error_country');
		}
		
		if ($_POST['zone_id'] == '') {
				$this->error['zone_id'] = $this->_('error_zone');
		}
		
		if ((strlen($_POST['password']) < 4) || (strlen($_POST['password']) > 20)) {
				$this->error['password'] = $this->_('error_password');
		}

		if ($_POST['confirm'] != $_POST['password']) {
				$this->error['confirm'] = $this->_('error_confirm');
		}
		
		if ($this->config->get('config_account_id')) {
			$information_info = $this->Model_Catalog_Information->getInformation($this->config->get('config_account_id'));
			
			if ($information_info && !isset($_POST['agree'])) {
					$this->error['warning'] = sprintf($this->_('error_agree'), $information_info['title']);
			}
		}
		
		return $this->error ? false : true;
  	}
}
