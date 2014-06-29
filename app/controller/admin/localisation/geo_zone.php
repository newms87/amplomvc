<?php
class App_Controller_Admin_Localisation_GeoZone extends Controller
{


	public function index()
	{
		$this->document->setTitle(_l("Geo Zones"));

		$this->getList();
	}

	public function insert()
	{
		$this->document->setTitle(_l("Geo Zones"));

		if (is_post() && $this->validateForm()) {
			$this->Model_Localisation_GeoZone->addGeoZone($_POST);

			message('success', _l("Success: You have modified geo zones!"));

			redirect('admin/localisation/geo_zone');
		}

		$this->getForm();
	}

	public function update()
	{
		$this->document->setTitle(_l("Geo Zones"));

		if (is_post() && $this->validateForm()) {
			$this->Model_Localisation_GeoZone->editGeoZone($_GET['geo_zone_id'], $_POST);

			message('success', _l("Success: You have modified geo zones!"));

			redirect('admin/localisation/geo_zone');
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->document->setTitle(_l("Geo Zones"));

		if (isset($_GET['selected']) && $this->validateDelete()) {
			foreach ($_GET['selected'] as $geo_zone_id) {
				$this->Model_Localisation_GeoZone->deleteGeoZone($geo_zone_id);
			}

			message('success', _l("Success: You have modified geo zones!"));
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

		$this->breadcrumb->add(_l("Home"), site_url('admin'));
		$this->breadcrumb->add(_l("Geo Zones"), site_url('admin/localisation/geo_zone', $url));

		$data['insert'] = site_url('admin/localisation/geo_zone/insert', $url);
		$data['delete'] = site_url('admin/localisation/geo_zone/delete', $url);

		$data['geo_zones'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * option('config_admin_limit'),
			'limit' => option('config_admin_limit')
		);

		$geo_zone_total = $this->Model_Localisation_GeoZone->getTotalGeoZones();

		$results = $this->Model_Localisation_GeoZone->getGeoZones($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => _l("Edit"),
				'href' => site_url('admin/localisation/geo_zone/update', 'geo_zone_id=' . $result['geo_zone_id'] . $url)
			);

			$data['geo_zones'][] = array(
				'geo_zone_id' => $result['geo_zone_id'],
				'name'        => $result['name'],
				'description' => $result['description'],
				'selected'    => isset($_GET['selected']) && in_array($result['geo_zone_id'], $_GET['selected']),
				'action'      => $action
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

		$data['sort_name']        = site_url('admin/localisation/geo_zone', 'sort=name' . $url);
		$data['sort_description'] = site_url('admin/localisation/geo_zone', 'sort=description' . $url);

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total  = $geo_zone_total;
		$data['pagination'] = $this->pagination->render();

		$data['sort']  = $sort;
		$data['order'] = $order;

		output($this->render('localisation/geo_zone_list', $data));
	}

	private function getForm()
	{
		$geo_zone_id = _get('geo_zone_id', 0);

		$url = $this->url->getQuery('sort', 'order', 'page');

		$this->breadcrumb->add(_l("Home"), site_url('admin'));
		$this->breadcrumb->add(_l("Geo Zones"), site_url('admin/localisation/geo_zone', $url));

		if (!$geo_zone_id) {
			$data['action'] = site_url('admin/localisation/geo_zone/insert', $url);
		} else {
			$data['action'] = site_url('admin/localisation/geo_zone/update', 'geo_zone_id=' . $geo_zone_id . '&' . $url);
		}

		$data['cancel'] = site_url('admin/localisation/geo_zone', $url);

		if ($geo_zone_id && !is_post()) {
			$geo_zone_info = $this->Model_Localisation_GeoZone->getGeoZone($geo_zone_id);
		}

		$defaults = array(
			'name'        => '',
			'description' => '',
			'exclude'     => 0,
			'zones'       => array(),
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$data[$key] = $_POST[$key];
			} elseif (isset($geo_zone_info[$key])) {
				$data[$key] = $geo_zone_info[$key];
			} elseif (!$geo_zone_id) {
				$data[$key] = $default;
			}
		}

		$data['data_countries'] = $this->Model_Localisation_Country->getCountries();

		if (!isset($data['zones'])) {
			$data['zones'] = $this->Model_Localisation_GeoZone->getZones($geo_zone_id);
		}

		output($this->render('localisation/geo_zone_form', $data));
	}

	private function validateForm()
	{
		if (!user_can('modify', 'localisation/geo_zone')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify geo zones!");
		}

		if (!validate('text', $_POST['name'], 3, 32)) {
			$this->error['name'] = _l("Geo Zone Name must be between 3 and 32 characters!");
		}

		if (!validate('text', $_POST['description'], 3, 255)) {
			$this->error['description'] = _l("Description Name must be between 3 and 255 characters!");
		}

		if (empty($_POST['exclude'])) {
			$_POST['exclude'] = 0;
		}

		return empty($this->error);
	}

	private function validateDelete()
	{
		if (!user_can('modify', 'localisation/geo_zone')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify geo zones!");
		}

		foreach ($_GET['selected'] as $geo_zone_id) {
			$tax_rate_total = $this->Model_Localisation_TaxRate->getTotalTaxRatesByGeoZoneId($geo_zone_id);

			if ($tax_rate_total) {
				$this->error['warning'] = sprintf(_l("Warning: This geo zone cannot be deleted as it is currently assigned to one or more tax rates!"), $tax_rate_total);
			}
		}

		return empty($this->error);
	}
}
