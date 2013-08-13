<?php
class Catalog_Controller_Affiliate_Register extends Controller
{
	
			
  	public function index()
  	{
		$this->template->load('affiliate/register');

		if ($this->affiliate->isLogged()) {
			$this->url->redirect($this->url->link('affiliate/account'));
		}

		$this->language->load('affiliate/register');
		
		$this->document->setTitle($this->_('head_title'));
		
		if ($this->request->isPost() && $this->validate()) {
			$this->Model_Affiliate_Affiliate->addAffiliate($_POST);

			$this->affiliate->login($_POST['email'], $_POST['password']);

			$this->url->redirect($this->url->link('affiliate/success'));
		}

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_account'), $this->url->link('affiliate/account'));
			$this->breadcrumb->add($this->_('text_register'), $this->url->link('affiliate/register'));

		$this->_('text_account_already', $this->url->link('affiliate/login'));
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->error['firstname'])) {
			$this->data['error_firstname'] = $this->error['firstname'];
		} else {
			$this->data['error_firstname'] = '';
		}
		
		if (isset($this->error['lastname'])) {
			$this->data['error_lastname'] = $this->error['lastname'];
		} else {
			$this->data['error_lastname'] = '';
		}
	
		if (isset($this->error['email'])) {
			$this->data['error_email'] = $this->error['email'];
		} else {
			$this->data['error_email'] = '';
		}
		
		if (isset($this->error['telephone'])) {
			$this->data['error_telephone'] = $this->error['telephone'];
		} else {
			$this->data['error_telephone'] = '';
		}
		
		if (isset($this->error['password'])) {
			$this->data['error_password'] = $this->error['password'];
		} else {
			$this->data['error_password'] = '';
		}
		
 		if (isset($this->error['confirm'])) {
			$this->data['error_confirm'] = $this->error['confirm'];
		} else {
			$this->data['error_confirm'] = '';
		}
		
  		if (isset($this->error['address_1'])) {
			$this->data['error_address_1'] = $this->error['address_1'];
		} else {
			$this->data['error_address_1'] = '';
		}
			
		if (isset($this->error['city'])) {
			$this->data['error_city'] = $this->error['city'];
		} else {
			$this->data['error_city'] = '';
		}
		
		if (isset($this->error['postcode'])) {
			$this->data['error_postcode'] = $this->error['postcode'];
		} else {
			$this->data['error_postcode'] = '';
		}

		if (isset($this->error['country'])) {
			$this->data['error_country'] = $this->error['country'];
		} else {
			$this->data['error_country'] = '';
		}

		if (isset($this->error['zone'])) {
			$this->data['error_zone'] = $this->error['zone'];
		} else {
			$this->data['error_zone'] = '';
		}
								
		$this->data['action'] = $this->url->link('affiliate/register');

		if (isset($_POST['firstname'])) {
			$this->data['firstname'] = $_POST['firstname'];
		} else {
			$this->data['firstname'] = '';
		}

		if (isset($_POST['lastname'])) {
			$this->data['lastname'] = $_POST['lastname'];
		} else {
			$this->data['lastname'] = '';
		}
		
		if (isset($_POST['email'])) {
			$this->data['email'] = $_POST['email'];
		} else {
			$this->data['email'] = '';
		}
		
		if (isset($_POST['telephone'])) {
			$this->data['telephone'] = $_POST['telephone'];
		} else {
			$this->data['telephone'] = '';
		}
		
		if (isset($_POST['fax'])) {
			$this->data['fax'] = $_POST['fax'];
		} else {
			$this->data['fax'] = '';
		}
		
		if (isset($_POST['company'])) {
			$this->data['company'] = $_POST['company'];
		} else {
			$this->data['company'] = '';
		}

		if (isset($_POST['website'])) {
			$this->data['website'] = $_POST['website'];
		} else {
			$this->data['website'] = '';
		}
				
		if (isset($_POST['address_1'])) {
			$this->data['address_1'] = $_POST['address_1'];
		} else {
			$this->data['address_1'] = '';
		}

		if (isset($_POST['address_2'])) {
			$this->data['address_2'] = $_POST['address_2'];
		} else {
			$this->data['address_2'] = '';
		}

		if (isset($_POST['postcode'])) {
			$this->data['postcode'] = $_POST['postcode'];
		} else {
			$this->data['postcode'] = '';
		}
		
		if (isset($_POST['city'])) {
			$this->data['city'] = $_POST['city'];
		} else {
			$this->data['city'] = '';
		}

		if (isset($_POST['country_id'])) {
				$this->data['country_id'] = $_POST['country_id'];
		} else {
				$this->data['country_id'] = $this->config->get('config_country_id');
		}

		if (isset($_POST['zone_id'])) {
				$this->data['zone_id'] = $_POST['zone_id'];
		} else {
				$this->data['zone_id'] = '';
		}
		
		$this->data['countries'] = $this->Model_Localisation_Country->getCountries();

		if (isset($_POST['tax'])) {
			$this->data['tax'] = $_POST['tax'];
		} else {
			$this->data['tax'] = '';
		}
		
		if (isset($_POST['payment'])) {
			$this->data['payment'] = $_POST['payment'];
		} else {
			$this->data['payment'] = 'cheque';
		}

		if (isset($_POST['cheque'])) {
			$this->data['cheque'] = $_POST['cheque'];
		} else {
			$this->data['cheque'] = '';
		}

		if (isset($_POST['paypal'])) {
			$this->data['paypal'] = $_POST['paypal'];
		} else {
			$this->data['paypal'] = '';
		}

		if (isset($_POST['bank_name'])) {
			$this->data['bank_name'] = $_POST['bank_name'];
		} else {
			$this->data['bank_name'] = '';
		}

		if (isset($_POST['bank_branch_number'])) {
			$this->data['bank_branch_number'] = $_POST['bank_branch_number'];
		} else {
			$this->data['bank_branch_number'] = '';
		}

		if (isset($_POST['bank_swift_code'])) {
			$this->data['bank_swift_code'] = $_POST['bank_swift_code'];
		} else {
			$this->data['bank_swift_code'] = '';
		}

		if (isset($_POST['bank_account_name'])) {
			$this->data['bank_account_name'] = $_POST['bank_account_name'];
		} else {
			$this->data['bank_account_name'] = '';
		}
		
		if (isset($_POST['bank_account_number'])) {
			$this->data['bank_account_number'] = $_POST['bank_account_number'];
		} else {
			$this->data['bank_account_number'] = '';
		}
																		
		if (isset($_POST['password'])) {
			$this->data['password'] = $_POST['password'];
		} else {
			$this->data['password'] = '';
		}
		
		if (isset($_POST['confirm'])) {
			$this->data['confirm'] = $_POST['confirm'];
		} else {
			$this->data['confirm'] = '';
		}

		if ($this->config->get('config_affiliate_terms_info_id')) {
			$information_info = $this->Model_Catalog_Information->getInformation($this->config->get('config_affiliate_terms_info_id'));
			
			if ($information_info) {
				$this->_('text_agree', $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_affiliate_terms_info_id')), $information_info['title'], $information_info['title']);
			} else {
				$this->data['text_agree'] = '';
			}
		} else {
			$this->data['text_agree'] = '';
		}
		
		if (isset($_POST['agree'])) {
				$this->data['agree'] = $_POST['agree'];
		} else {
			$this->data['agree'] = false;
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

  	private function validate()
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

		if ($this->Model_Affiliate_Affiliate->getTotalAffiliatesByEmail($_POST['email'])) {
				$this->error['warning'] = $this->_('error_exists');
		}
		
		if ((strlen($_POST['telephone']) < 3) || (strlen($_POST['telephone']) > 32)) {
				$this->error['telephone'] = $this->_('error_telephone');
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
				$this->error['country'] = $this->_('error_country');
		}
		
		if ($_POST['zone_id'] == '') {
				$this->error['zone'] = $this->_('error_zone');
		}

		if ((strlen($_POST['password']) < 4) || (strlen($_POST['password']) > 20)) {
				$this->error['password'] = $this->_('error_password');
		}

		if ($_POST['confirm'] != $_POST['password']) {
				$this->error['confirm'] = $this->_('error_confirm');
		}
		
		if ($this->config->get('config_affiliate_terms_info_id')) {
			$information_info = $this->Model_Catalog_Information->getInformation($this->config->get('config_affiliate_terms_info_id'));
			
			if ($information_info && !isset($_POST['agree'])) {
					$this->error['warning'] = sprintf($this->_('error_agree'), $information_info['title']);
			}
		}
		
		return $this->error ? false : true;
  	}
}