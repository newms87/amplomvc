<?php
class App_Controller_Admin_Localisation_TaxRate extends Controller
{


	public function index()
	{
		$this->document->setTitle(_l("Tax Rates"));

		$this->getList();
	}

	public function insert()
	{
		$this->document->setTitle(_l("Tax Rates"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Localisation_TaxRate->addTaxRate($_POST);

			$this->message->add('success', _l("Success: You have modified tax classes!"));

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

			redirect('admin/localisation/tax_rate', $url);
		}

		$this->getForm();
	}

	public function update()
	{
		$this->document->setTitle(_l("Tax Rates"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Localisation_TaxRate->editTaxRate($_GET['tax_rate_id'], $_POST);

			$this->message->add('success', _l("Success: You have modified tax classes!"));

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

			redirect('admin/localisation/tax_rate', $url);
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->document->setTitle(_l("Tax Rates"));

		if (isset($_GET['selected']) && $this->validateDelete()) {
			foreach ($_GET['selected'] as $tax_rate_id) {
				$this->Model_Localisation_TaxRate->deleteTaxRate($tax_rate_id);
			}

			$this->message->add('success', _l("Success: You have modified tax classes!"));

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

			redirect('admin/localisation/tax_rate', $url);
		}

		$this->getList();
	}

	private function getList()
	{
		if (isset($_GET['sort'])) {
			$sort = $_GET['sort'];
		} else {
			$sort = 'tr.name';
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

		$this->breadcrumb->add(_l("Home"), site_url('admin/common/home'));
		$this->breadcrumb->add(_l("Tax Rates"), site_url('admin/localisation/tax_rate', $url));

		$data['insert'] = site_url('admin/localisation/tax_rate/insert', $url);
		$data['delete'] = site_url('admin/localisation/tax_rate/delete', $url);

		$data['tax_rates'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * option('config_admin_limit'),
			'limit' => option('config_admin_limit')
		);

		$tax_rate_total = $this->Model_Localisation_TaxRate->getTotalTaxRates();

		$results = $this->Model_Localisation_TaxRate->getTaxRates($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => _l("Edit"),
				'href' => site_url('admin/localisation/tax_rate/update', 'tax_rate_id=' . $result['tax_rate_id'] . $url)
			);

			$data['tax_rates'][] = array(
				'tax_rate_id'   => $result['tax_rate_id'],
				'name'          => $result['name'],
				'rate'          => $result['rate'],
				'type'          => ($result['type'] == 'F' ? _l("Fixed Amount") : _l("Percentage")),
				'geo_zone'      => $result['geo_zone'],
				'date_added'    => $this->date->format($result['date_added'], 'short'),
				'date_modified' => $this->date->format($result['date_modified'], 'short'),
				'selected'      => isset($_GET['selected']) && in_array($result['tax_rate_id'], $_GET['selected']),
				'action'        => $action
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

		$data['sort_name']          = site_url('admin/localisation/tax_rate', 'sort=tr.name' . $url);
		$data['sort_rate']          = site_url('admin/localisation/tax_rate', 'sort=tr.rate' . $url);
		$data['sort_type']          = site_url('admin/localisation/tax_rate', 'sort=tr.type' . $url);
		$data['sort_geo_zone']      = site_url('admin/localisation/tax_rate', 'sort=gz.name' . $url);
		$data['sort_date_added']    = site_url('admin/localisation/tax_rate', 'sort=tr.date_added' . $url);
		$data['sort_date_modified'] = site_url('admin/localisation/tax_rate', 'sort=tr.date_modified' . $url);

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total  = $tax_rate_total;
		$data['pagination'] = $this->pagination->render();

		$data['sort']  = $sort;
		$data['order'] = $order;

		$this->response->setOutput($this->render('localisation/tax_rate_list', $data));
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

		if (isset($this->error['rate'])) {
			$data['error_rate'] = $this->error['rate'];
		} else {
			$data['error_rate'] = '';
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

		$this->breadcrumb->add(_l("Home"), site_url('admin/common/home'));
		$this->breadcrumb->add(_l("Tax Rates"), site_url('admin/localisation/tax_rate', $url));

		if (!isset($_GET['tax_rate_id'])) {
			$data['action'] = site_url('admin/localisation/tax_rate/insert', $url);
		} else {
			$data['action'] = site_url('admin/localisation/tax_rate/update', 'tax_rate_id=' . $_GET['tax_rate_id'] . $url);
		}

		$data['cancel'] = site_url('admin/localisation/tax_rate', $url);

		if (isset($_GET['tax_rate_id']) && !$this->request->isPost()) {
			$tax_rate_info = $this->Model_Localisation_TaxRate->getTaxRate($_GET['tax_rate_id']);
		}

		if (isset($_POST['name'])) {
			$data['name'] = $_POST['name'];
		} elseif (!empty($tax_rate_info)) {
			$data['name'] = $tax_rate_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($_POST['rate'])) {
			$data['rate'] = $_POST['rate'];
		} elseif (!empty($tax_rate_info)) {
			$data['rate'] = $tax_rate_info['rate'];
		} else {
			$data['rate'] = '';
		}

		if (isset($_POST['type'])) {
			$data['type'] = $_POST['type'];
		} elseif (!empty($tax_rate_info)) {
			$data['type'] = $tax_rate_info['type'];
		} else {
			$data['type'] = '';
		}

		if (isset($_POST['tax_rate_customer_group'])) {
			$data['tax_rate_customer_group'] = $_POST['tax_rate_customer_group'];
		} elseif (!empty($tax_rate_info)) {
			$data['tax_rate_customer_group'] = $this->Model_Localisation_TaxRate->getTaxRateCustomerGroups($_GET['tax_rate_id']);
		} else {
			$data['tax_rate_customer_group'] = array();
		}

		$data['customer_groups'] = $this->Model_Sale_CustomerGroup->getCustomerGroups();

		if (isset($_POST['geo_zone_id'])) {
			$data['geo_zone_id'] = $_POST['geo_zone_id'];
		} elseif (!empty($tax_rate_info)) {
			$data['geo_zone_id'] = $tax_rate_info['geo_zone_id'];
		} else {
			$data['geo_zone_id'] = '';
		}

		$data['geo_zones'] = $this->Model_Localisation_GeoZone->getGeoZones();

		$this->response->setOutput($this->render('localisation/tax_rate_form', $data));
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'localisation/tax_rate')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify tax classes!");
		}

		if ((strlen($_POST['name']) < 3) || (strlen($_POST['name']) > 32)) {
			$this->error['name'] = _l("Tax Name must be between 3 and 32 characters!");
		}

		if (!$_POST['rate']) {
			$this->error['rate'] = _l("Tax Rate required!");
		}

		return empty($this->error);
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'localisation/tax_rate')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify tax classes!");
		}

		foreach ($_GET['selected'] as $tax_rate_id) {
			$tax_rule_total = $this->Model_Localisation_Taxclass->getTotalTaxRulesByTaxRateId($tax_rate_id);

			if ($tax_rule_total) {
				$this->error['warning'] = sprintf(_l("Warning: This tax rate cannot be deleted as it is currently assigned to %s tax classes!"), $tax_rule_total);
			}
		}

		return empty($this->error);
	}
}
