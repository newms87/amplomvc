<?php
class App_Controller_Admin_Localisation_Zone extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("Zones"));

		$this->getList();
	}

	public function insert()
	{
		$this->document->setTitle(_l("Zones"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Localisation_Zone->addZone($_POST);

			$this->message->add('success', _l("Success: You have modified zones!"));

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

			redirect('admin/localisation/zone', $url);
		}

		$this->getForm();
	}

	public function update()
	{
		$this->document->setTitle(_l("Zones"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Localisation_Zone->editZone($_GET['zone_id'], $_POST);

			$this->message->add('success', _l("Success: You have modified zones!"));

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

			redirect('admin/localisation/zone', $url);
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->document->setTitle(_l("Zones"));

		if (isset($_GET['selected']) && $this->validateDelete()) {
			foreach ($_GET['selected'] as $zone_id) {
				$this->Model_Localisation_Zone->deleteZone($zone_id);
			}

			$this->message->add('success', _l("Success: You have modified zones!"));

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

			redirect('admin/localisation/zone', $url);
		}

		$this->getList();
	}

	private function getList()
	{
		if (isset($_GET['sort'])) {
			$sort = $_GET['sort'];
		} else {
			$sort = 'c.name';
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

		$this->breadcrumb->add(_l("Home"), site_url('admin'));
		$this->breadcrumb->add(_l("Zones"), site_url('admin/localisation/zone', $url));

		$data['insert'] = site_url('admin/localisation/zone/insert', $url);
		$data['delete'] = site_url('admin/localisation/zone/delete', $url);

		$data['zones'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * option('config_admin_limit'),
			'limit' => option('config_admin_limit')
		);

		$zone_total = $this->Model_Localisation_Zone->getTotalZones();

		$results = $this->Model_Localisation_Zone->getZones($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => _l("Edit"),
				'href' => site_url('admin/localisation/zone/update', 'zone_id=' . $result['zone_id'] . $url)
			);

			$data['zones'][] = array(
				'zone_id'  => $result['zone_id'],
				'country'  => $result['country'],
				'name'     => $result['name'] . (($result['zone_id'] == option('config_zone_id')) ? _l(" <b>(Default)</b>") : null),
				'code'     => $result['code'],
				'selected' => isset($_GET['selected']) && in_array($result['zone_id'], $_GET['selected']),
				'action'   => $action
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

		$data['sort_country'] = site_url('admin/localisation/zone', 'sort=c.name' . $url);
		$data['sort_name']    = site_url('admin/localisation/zone', 'sort=z.name' . $url);
		$data['sort_code']    = site_url('admin/localisation/zone', 'sort=z.code' . $url);

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total  = $zone_total;
		$data['pagination'] = $this->pagination->render();

		$data['sort']  = $sort;
		$data['order'] = $order;

		$this->response->setOutput($this->render('localisation/zone_list', $data));
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

		$this->breadcrumb->add(_l("Home"), site_url('admin'));
		$this->breadcrumb->add(_l("Zones"), site_url('admin/localisation/zone', $url));

		if (!isset($_GET['zone_id'])) {
			$data['action'] = site_url('admin/localisation/zone/insert', $url);
		} else {
			$data['action'] = site_url('admin/localisation/zone/update', 'zone_id=' . $_GET['zone_id'] . $url);
		}

		$data['cancel'] = site_url('admin/localisation/zone', $url);

		if (isset($_GET['zone_id']) && !$this->request->isPost()) {
			$zone_info = $this->Model_Localisation_Zone->getZone($_GET['zone_id']);
		}

		if (isset($_POST['status'])) {
			$data['status'] = $_POST['status'];
		} elseif (isset($zone_info)) {
			$data['status'] = $zone_info['status'];
		} else {
			$data['status'] = '1';
		}

		if (isset($_POST['name'])) {
			$data['name'] = $_POST['name'];
		} elseif (isset($zone_info)) {
			$data['name'] = $zone_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($_POST['code'])) {
			$data['code'] = $_POST['code'];
		} elseif (isset($zone_info)) {
			$data['code'] = $zone_info['code'];
		} else {
			$data['code'] = '';
		}

		if (isset($_POST['country_id'])) {
			$data['country_id'] = $_POST['country_id'];
		} elseif (isset($zone_info)) {
			$data['country_id'] = $zone_info['country_id'];
		} else {
			$data['country_id'] = '';
		}

		$data['countries'] = $this->Model_Localisation_Country->getCountries();

		$this->response->setOutput($this->render('localisation/zone_form', $data));
	}

	private function validateForm()
	{
		if (!user_can('modify', 'localisation/zone')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify zones!");
		}

		if ((strlen($_POST['name']) < 3) || (strlen($_POST['name']) > 64)) {
			$this->error['name'] = _l("Zone Name must be between 3 and 128 characters!");
		}

		return empty($this->error);
	}

	private function validateDelete()
	{
		if (!user_can('modify', 'localisation/zone')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify zones!");
		}

		foreach ($_GET['selected'] as $zone_id) {
			if (option('config_zone_id') == $zone_id) {
				$this->error['warning'] = _l("Warning: This zone cannot be deleted as it is currently assigned as the default store zone!");
			}

			$store_total = $this->Model_Setting_Store->getTotalStoresByZoneId($zone_id);

			if ($store_total) {
				$this->error['warning'] = sprintf(_l("Warning: This zone cannot be deleted as it is currently assigned to %s stores!"), $store_total);
			}

			$zone_to_geo_zone_total = $this->Model_Localisation_GeoZone->getTotalZoneToGeoZoneByZoneId($zone_id);

			if ($zone_to_geo_zone_total) {
				$this->error['warning'] = sprintf(_l("Warning: This zone cannot be deleted as it is currently assigned to %s zones to geo zones!"), $zone_to_geo_zone_total);
			}
		}

		return empty($this->error);
	}
}
