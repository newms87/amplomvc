<?php
class Admin_Controller_Localisation_Country extends Controller
{


	public function index()
	{
		$this->language->load('localisation/country');

		$this->document->setTitle(_l("Country"));

		$this->getList();
	}

	public function insert()
	{
		$this->language->load('localisation/country');

		$this->document->setTitle(_l("Country"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Localisation_Country->addCountry($_POST);

			$this->message->add('success', _l("Success: You have modified countries!"));

			$url = '';

			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}

			$this->url->redirect('localisation/country', $url);
		}

		$this->getForm();
	}

	public function update()
	{
		$this->language->load('localisation/country');

		$this->document->setTitle(_l("Country"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Localisation_Country->editCountry($_GET['country_id'], $_POST);

			$this->message->add('success', _l("Success: You have modified countries!"));

			$url = '';

			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}

			$this->url->redirect('localisation/country', $url);
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->language->load('localisation/country');

		$this->document->setTitle(_l("Country"));

		if (isset($_GET['selected']) && $this->validateDelete()) {
			foreach ($_GET['selected'] as $country_id) {
				$this->Model_Localisation_Country->deleteCountry($country_id);
			}

			$this->message->add('success', _l("Success: You have modified countries!"));

			$url = '';

			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}

			$this->url->redirect('localisation/country', $url);
		}

		$this->getList();
	}

	private function getList()
	{
		$this->template->load('localisation/country_list');

		if (isset($_GET['sort'])) {
			$sort = $_GET['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($_GET['order'])) {
			$order = $_GET['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Country"), $this->url->link('localisation/country', $url));

		$this->data['insert'] = $this->url->link('localisation/country/insert', $url);
		$this->data['delete'] = $this->url->link('localisation/country/delete', $url);

		$this->data['countries'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);

		$country_total = $this->Model_Localisation_Country->getTotalCountries();

		$results = $this->Model_Localisation_Country->getCountries($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('localisation/country/update', 'country_id=' . $result['country_id'] . $url)
			);

			$this->data['countries'][] = array(
				'country_id' => $result['country_id'],
				'name'       => $result['name'] . (($result['country_id'] == $this->config->get('config_country_id')) ? $this->_('text_default') : null),
				'iso_code_2' => $result['iso_code_2'],
				'iso_code_3' => $result['iso_code_3'],
				'selected'   => isset($_GET['selected']) && in_array($result['country_id'], $_GET['selected']),
				'action'     => $action
			);
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}

		$this->data['sort_name']       = $this->url->link('localisation/country', 'sort=name' . $url);
		$this->data['sort_iso_code_2'] = $this->url->link('localisation/country', 'sort=iso_code_2' . $url);
		$this->data['sort_iso_code_3'] = $this->url->link('localisation/country', 'sort=iso_code_3' . $url);

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total  = $country_total;
		$this->data['pagination'] = $this->pagination->render();

		$this->data['sort']  = $sort;
		$this->data['order'] = $order;

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function getForm()
	{
		$this->template->load('localisation/country_form');

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$this->data['error_name'] = $this->error['name'];
		} else {
			$this->data['error_name'] = '';
		}

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Country"), $this->url->link('localisation/country', $url));

		if (!isset($_GET['country_id'])) {
			$this->data['action'] = $this->url->link('localisation/country/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('localisation/country/update', 'country_id=' . $_GET['country_id'] . $url);
		}

		$this->data['cancel'] = $this->url->link('localisation/country', $url);

		if (isset($_GET['country_id']) && !$this->request->isPost()) {
			$country_info = $this->Model_Localisation_Country->getCountry($_GET['country_id']);
		}

		if (isset($_POST['name'])) {
			$this->data['name'] = $_POST['name'];
		} elseif (isset($country_info)) {
			$this->data['name'] = $country_info['name'];
		} else {
			$this->data['name'] = '';
		}

		if (isset($_POST['iso_code_2'])) {
			$this->data['iso_code_2'] = $_POST['iso_code_2'];
		} elseif (isset($country_info)) {
			$this->data['iso_code_2'] = $country_info['iso_code_2'];
		} else {
			$this->data['iso_code_2'] = '';
		}

		if (isset($_POST['iso_code_3'])) {
			$this->data['iso_code_3'] = $_POST['iso_code_3'];
		} elseif (isset($country_info)) {
			$this->data['iso_code_3'] = $country_info['iso_code_3'];
		} else {
			$this->data['iso_code_3'] = '';
		}

		if (isset($_POST['address_format'])) {
			$this->data['address_format'] = $_POST['address_format'];
		} elseif (isset($country_info)) {
			$this->data['address_format'] = $country_info['address_format'];
		} else {
			$this->data['address_format'] = '';
		}

		if (isset($_POST['postcode_required'])) {
			$this->data['postcode_required'] = $_POST['postcode_required'];
		} elseif (isset($country_info)) {
			$this->data['postcode_required'] = $country_info['postcode_required'];
		} else {
			$this->data['postcode_required'] = 0;
		}

		if (isset($_POST['status'])) {
			$this->data['status'] = $_POST['status'];
		} elseif (isset($country_info)) {
			$this->data['status'] = $country_info['status'];
		} else {
			$this->data['status'] = '1';
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'localisation/country')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify countries!");
		}

		if ((strlen($_POST['name']) < 3) || (strlen($_POST['name']) > 128)) {
			$this->error['name'] = _l("Country Name must be between 3 and 128 characters!");
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'localisation/country')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify countries!");
		}

		foreach ($_GET['selected'] as $country_id) {
			if ($this->config->get('config_country_id') == $country_id) {
				$this->error['warning'] = _l("Warning: This country cannot be deleted as it is currently assigned as the default store country!");
			}

			$store_total = $this->Model_Setting_Store->getTotalStoresByCountryId($country_id);

			if ($store_total) {
				$this->error['warning'] = sprintf(_l("Warning: This country cannot be deleted as it is currently assigned to %s stores!"), $store_total);
			}

			$address_total = $this->Model_Sale_Customer->getTotalAddressesByCountryId($country_id);

			if ($address_total) {
				$this->error['warning'] = sprintf(_l("Warning: This country cannot be deleted as it is currently assigned to %s address book entries!"), $address_total);
			}

			$affiliate_total = $this->Model_Sale_Affiliate->getTotalAffiliatesByCountryId($country_id);

			if ($affiliate_total) {
				$this->error['warning'] = sprintf(_l("Warning: This country cannot be deleted as it is currently assigned to %s affiliates!"), $affiliate_total);
			}

			$zone_total = $this->Model_Localisation_Zone->getTotalZonesByCountryId($country_id);

			if ($zone_total) {
				$this->error['warning'] = sprintf(_l("Warning: This country cannot be deleted as it is currently assigned to %s zones!"), $zone_total);
			}

			$zone_to_geo_zone_total = $this->Model_Localisation_GeoZone->getTotalZoneToGeoZoneByCountryId($country_id);

			if ($zone_to_geo_zone_total) {
				$this->error['warning'] = sprintf(_l("Warning: This country cannot be deleted as it is currently assigned to %s zones to geo zones!"), $zone_to_geo_zone_total);
			}
		}

		return $this->error ? false : true;
	}
}
