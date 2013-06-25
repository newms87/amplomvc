<?php
class Catalog_Controller_Affiliate_Edit extends Controller 
{
	

	public function index()
	{
		$this->template->load('affiliate/edit');

		if (!$this->affiliate->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('affiliate/edit');

			$this->url->redirect($this->url->link('affiliate/login'));
		}

		$this->language->load('affiliate/edit');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validate()) {
			$this->Model_Affiliate_Affiliate->editAffiliate($_POST);
			
			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect($this->url->link('affiliate/account'));
		}

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_account'), $this->url->link('affiliate/account'));
			$this->breadcrumb->add($this->_('text_edit'), $this->url->link('affiliate/edit'));

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
		
		$this->data['action'] = $this->url->link('affiliate/edit');

		if (!$this->request->isPost()) {
			$affiliate_info = $this->Model_Affiliate_Affiliate->getAffiliate($this->affiliate->getId());
		}

		if (isset($_POST['firstname'])) {
			$this->data['firstname'] = $_POST['firstname'];
		} elseif (!empty($affiliate_info)) {
			$this->data['firstname'] = $affiliate_info['firstname'];
		} else {
			$this->data['firstname'] = '';
		}

		if (isset($_POST['lastname'])) {
			$this->data['lastname'] = $_POST['lastname'];
		} elseif (!empty($affiliate_info)) {
			$this->data['lastname'] = $affiliate_info['lastname'];
		} else {
			$this->data['lastname'] = '';
		}

		if (isset($_POST['email'])) {
			$this->data['email'] = $_POST['email'];
		} elseif (!empty($affiliate_info)) {
			$this->data['email'] = $affiliate_info['email'];
		} else {
			$this->data['email'] = '';
		}

		if (isset($_POST['telephone'])) {
			$this->data['telephone'] = $_POST['telephone'];
		} elseif (!empty($affiliate_info)) {
			$this->data['telephone'] = $affiliate_info['telephone'];
		} else {
			$this->data['telephone'] = '';
		}

		if (isset($_POST['fax'])) {
			$this->data['fax'] = $_POST['fax'];
		} elseif (!empty($affiliate_info)) {
			$this->data['fax'] = $affiliate_info['fax'];
		} else {
			$this->data['fax'] = '';
		}
		
		if (isset($_POST['company'])) {
			$this->data['company'] = $_POST['company'];
		} elseif (!empty($affiliate_info)) {
			$this->data['company'] = $affiliate_info['company'];
		} else {
			$this->data['company'] = '';
		}

		if (isset($_POST['website'])) {
			$this->data['website'] = $_POST['website'];
		} elseif (!empty($affiliate_info)) {
			$this->data['website'] = $affiliate_info['website'];
		} else {
			$this->data['website'] = '';
		}
				
		if (isset($_POST['address_1'])) {
			$this->data['address_1'] = $_POST['address_1'];
		} elseif (!empty($affiliate_info)) {
			$this->data['address_1'] = $affiliate_info['address_1'];
		} else {
			$this->data['address_1'] = '';
		}

		if (isset($_POST['address_2'])) {
			$this->data['address_2'] = $_POST['address_2'];
		} elseif (!empty($affiliate_info)) {
			$this->data['address_2'] = $affiliate_info['address_2'];
		} else {
			$this->data['address_2'] = '';
		}

		if (isset($_POST['postcode'])) {
			$this->data['postcode'] = $_POST['postcode'];
		} elseif (!empty($affiliate_info)) {
			$this->data['postcode'] = $affiliate_info['postcode'];
		} else {
			$this->data['postcode'] = '';
		}
		
		if (isset($_POST['city'])) {
			$this->data['city'] = $_POST['city'];
		} elseif (!empty($affiliate_info)) {
			$this->data['city'] = $affiliate_info['city'];
		} else {
			$this->data['city'] = '';
		}

		if (isset($_POST['country_id'])) {
				$this->data['country_id'] = $_POST['country_id'];
		} elseif (!empty($affiliate_info)) {
			$this->data['country_id'] = $affiliate_info['country_id'];
		} else {
				$this->data['country_id'] = $this->config->get('config_country_id');
		}

		if (isset($_POST['zone_id'])) {
				$this->data['zone_id'] = $_POST['zone_id'];
		} elseif (!empty($affiliate_info)) {
			$this->data['zone_id'] = $affiliate_info['zone_id'];
		} else {
				$this->data['zone_id'] = '';
		}
		
		$this->data['countries'] = $this->Model_Localisation_Country->getCountries();

		$this->data['back'] = $this->url->link('affiliate/account');

		$this->data['breadcrumbs'] = $this->breadcrumb->render();
		
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
		
		if (($this->affiliate->getEmail() != $_POST['email']) && $this->Model_Affiliate_Affiliate->getTotalAffiliatesByEmail($_POST['email'])) {
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
		
		return $this->error ? false : true;
	}
}