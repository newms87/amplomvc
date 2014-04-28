<?php
class Admin_Controller_Localisation_Country extends Controller
{


	public function index()
	{
		$this->document->setTitle(_l("Country"));

		$this->getList();
	}

	public function insert()
	{
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

			redirect('localisation/country', $url);
		}

		$this->getForm();
	}

	public function update()
	{
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

			redirect('localisation/country', $url);
		}

		$this->getForm();
	}

	public function delete()
	{
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

			redirect('localisation/country', $url);
		}

		$this->getList();
	}

	private function getList()
	{
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

		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Country"), site_url('localisation/country', $url));

		$data['insert'] = site_url('localisation/country/insert', $url);
		$data['delete'] = site_url('localisation/country/delete', $url);

		$data['countries'] = array();

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
				'text' => _l("Edit"),
				'href' => site_url('localisation/country/update', 'country_id=' . $result['country_id'] . $url)
			);

			$data['countries'][] = array(
				'country_id' => $result['country_id'],
				'name'       => $result['name'] . (($result['country_id'] == $this->config->get('config_country_id')) ? _l(" <b>(Default)</b>") : null),
				'iso_code_2' => $result['iso_code_2'],
				'iso_code_3' => $result['iso_code_3'],
				'selected'   => isset($_GET['selected']) && in_array($result['country_id'], $_GET['selected']),
				'action'     => $action
			);
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if ($this->session->has('success')) {
			$data['success'] = $this->session->get('success');

			$this->session->delete('success');
		} else {
			$data['success'] = '';
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

		$data['sort_name']       = site_url('localisation/country', 'sort=name' . $url);
		$data['sort_iso_code_2'] = site_url('localisation/country', 'sort=iso_code_2' . $url);
		$data['sort_iso_code_3'] = site_url('localisation/country', 'sort=iso_code_3' . $url);

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total  = $country_total;
		$data['pagination'] = $this->pagination->render();

		$data['sort']  = $sort;
		$data['order'] = $order;

		$this->response->setOutput($this->render('localisation/country_list', $data));
	}

	private function getForm()
	{
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
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

		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Country"), site_url('localisation/country', $url));

		if (!isset($_GET['country_id'])) {
			$data['action'] = site_url('localisation/country/insert', $url);
		} else {
			$data['action'] = site_url('localisation/country/update', 'country_id=' . $_GET['country_id'] . $url);
		}

		$data['cancel'] = site_url('localisation/country', $url);

		if (isset($_GET['country_id']) && !$this->request->isPost()) {
			$country_info = $this->Model_Localisation_Country->getCountry($_GET['country_id']);
		}

		if (isset($_POST['name'])) {
			$data['name'] = $_POST['name'];
		} elseif (isset($country_info)) {
			$data['name'] = $country_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($_POST['iso_code_2'])) {
			$data['iso_code_2'] = $_POST['iso_code_2'];
		} elseif (isset($country_info)) {
			$data['iso_code_2'] = $country_info['iso_code_2'];
		} else {
			$data['iso_code_2'] = '';
		}

		if (isset($_POST['iso_code_3'])) {
			$data['iso_code_3'] = $_POST['iso_code_3'];
		} elseif (isset($country_info)) {
			$data['iso_code_3'] = $country_info['iso_code_3'];
		} else {
			$data['iso_code_3'] = '';
		}

		if (isset($_POST['address_format'])) {
			$data['address_format'] = $_POST['address_format'];
		} elseif (isset($country_info)) {
			$data['address_format'] = $country_info['address_format'];
		} else {
			$data['address_format'] = '';
		}

		if (isset($_POST['postcode_required'])) {
			$data['postcode_required'] = $_POST['postcode_required'];
		} elseif (isset($country_info)) {
			$data['postcode_required'] = $country_info['postcode_required'];
		} else {
			$data['postcode_required'] = 0;
		}

		if (isset($_POST['status'])) {
			$data['status'] = $_POST['status'];
		} elseif (isset($country_info)) {
			$data['status'] = $country_info['status'];
		} else {
			$data['status'] = '1';
		}

		$this->response->setOutput($this->render('localisation/country_form', $data));
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'localisation/country')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify countries!");
		}

		if ((strlen($_POST['name']) < 3) || (strlen($_POST['name']) > 128)) {
			$this->error['name'] = _l("Country Name must be between 3 and 128 characters!");
		}

		return empty($this->error);
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

			$zone_total = $this->Model_Localisation_Zone->getTotalZonesByCountryId($country_id);

			if ($zone_total) {
				$this->error['warning'] = sprintf(_l("Warning: This country cannot be deleted as it is currently assigned to %s zones!"), $zone_total);
			}

			$zone_to_geo_zone_total = $this->Model_Localisation_GeoZone->getTotalZoneToGeoZoneByCountryId($country_id);

			if ($zone_to_geo_zone_total) {
				$this->error['warning'] = sprintf(_l("Warning: This country cannot be deleted as it is currently assigned to %s zones to geo zones!"), $zone_to_geo_zone_total);
			}
		}

		return empty($this->error);
	}
}
