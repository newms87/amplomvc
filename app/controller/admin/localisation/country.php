<?php
class App_Controller_Admin_Localisation_Country extends Controller
{


	public function index()
	{
		set_page_info('title', _l("Country"));

		$this->getList();
	}

	public function insert()
	{
		set_page_info('title', _l("Country"));

		if (IS_POST && $this->validateForm()) {
			$this->Model_Localisation_Country->addCountry($_POST);

			message('success', _l("Success: You have modified countries!"));

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

			redirect('admin/localisation/country', $url);
		}

		$this->getForm();
	}

	public function update()
	{
		set_page_info('title', _l("Country"));

		if (IS_POST && $this->validateForm()) {
			$this->Model_Localisation_Country->editCountry($_GET['country_id'], $_POST);

			message('success', _l("Success: You have modified countries!"));

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

			redirect('admin/localisation/country', $url);
		}

		$this->getForm();
	}

	public function delete()
	{
		set_page_info('title', _l("Country"));

		if (isset($_GET['selected']) && $this->validateDelete()) {
			foreach ($_GET['selected'] as $country_id) {
				$this->Model_Localisation_Country->deleteCountry($country_id);
			}

			message('success', _l("Success: You have modified countries!"));

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

			redirect('admin/localisation/country', $url);
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

		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Country"), site_url('admin/localisation/country', $url));

		$data['insert'] = site_url('admin/localisation/country/insert', $url);
		$data['delete'] = site_url('admin/localisation/country/delete', $url);

		$data['countries'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * option('admin_list_limit'),
			'limit' => option('admin_list_limit')
		);

		$country_total = $this->Model_Localisation_Country->getTotalCountries();

		$results = $this->Model_Localisation_Country->getCountries($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => _l("Edit"),
				'href' => site_url('admin/localisation/country/update', 'country_id=' . $result['country_id'] . $url)
			);

			$data['countries'][] = array(
				'country_id' => $result['country_id'],
				'name'       => $result['name'] . (($result['country_id'] == option('config_country_id')) ? _l(" <b>(Default)</b>") : null),
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

			$this->session->remove('success');
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

		$data['sort_name']       = site_url('admin/localisation/country', 'sort=name' . $url);
		$data['sort_iso_code_2'] = site_url('admin/localisation/country', 'sort=iso_code_2' . $url);
		$data['sort_iso_code_3'] = site_url('admin/localisation/country', 'sort=iso_code_3' . $url);

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$data['sort']  = $sort;
		$data['order'] = $order;

		output($this->render('localisation/country_list', $data));
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

		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Country"), site_url('admin/localisation/country', $url));

		if (!isset($_GET['country_id'])) {
			$data['action'] = site_url('admin/localisation/country/insert', $url);
		} else {
			$data['action'] = site_url('admin/localisation/country/update', 'country_id=' . $_GET['country_id'] . $url);
		}

		$data['cancel'] = site_url('admin/localisation/country', $url);

		if (isset($_GET['country_id']) && !IS_POST) {
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

		output($this->render('localisation/country_form', $data));
	}

	private function validateForm()
	{
		if (!user_can('w', 'admin/localisation/country')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify countries!");
		}

		if ((strlen($_POST['name']) < 3) || (strlen($_POST['name']) > 128)) {
			$this->error['name'] = _l("Country Name must be between 3 and 128 characters!");
		}

		return empty($this->error);
	}

	private function validateDelete()
	{
		if (!user_can('w', 'admin/localisation/country')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify countries!");
		}

		foreach ($_GET['selected'] as $country_id) {
			if (option('config_country_id') == $country_id) {
				$this->error['warning'] = _l("Warning: This country cannot be deleted as it is currently assigned as the default store country!");
			}

			$store_total = $this->Model_Setting_Store->getTotalStoresByCountryId($country_id);

			if ($store_total) {
				$this->error['warning'] = sprintf(_l("Warning: This country cannot be deleted as it is currently assigned to %s stores!"), $store_total);
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
