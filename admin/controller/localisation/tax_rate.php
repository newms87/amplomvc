<?php
class Admin_Controller_Localisation_TaxRate extends Controller 
{
	
 
	public function index()
	{
		$this->load->language('localisation/tax_rate');

		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
	}

	public function insert()
	{
		$this->load->language('localisation/tax_rate');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Localisation_TaxRate->addTaxRate($_POST);

			$this->message->add('success', $this->_('text_success'));

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
			
			$this->url->redirect($this->url->link('localisation/tax_rate', $url));
		}

		$this->getForm();
	}

	public function update()
	{
		$this->load->language('localisation/tax_rate');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Localisation_TaxRate->editTaxRate($_GET['tax_rate_id'], $_POST);
			
			$this->message->add('success', $this->_('text_success'));
			
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
			
			$this->url->redirect($this->url->link('localisation/tax_rate', $url));
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->load->language('localisation/tax_rate');

		$this->document->setTitle($this->_('heading_title'));
 		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $tax_rate_id) {
				$this->Model_Localisation_TaxRate->deleteTaxRate($tax_rate_id);
			}
			
			$this->message->add('success', $this->_('text_success'));
			
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
			
			$this->url->redirect($this->url->link('localisation/tax_rate', $url));
		}

		$this->getList();
	}

	private function getList()
	{
		$this->template->load('localisation/tax_rate_list');

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
		
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('localisation/tax_rate', $url));

		$this->data['insert'] = $this->url->link('localisation/tax_rate/insert', $url);
		$this->data['delete'] = $this->url->link('localisation/tax_rate/delete', $url);
		
		$this->data['tax_rates'] = array();
		
		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$tax_rate_total = $this->Model_Localisation_TaxRate->getTotalTaxRates();

		$results = $this->Model_Localisation_TaxRate->getTaxRates($data);

		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('localisation/tax_rate/update', 'tax_rate_id=' . $result['tax_rate_id'] . $url)
			);
											
			$this->data['tax_rates'][] = array(
				'tax_rate_id'	=> $result['tax_rate_id'],
				'name'			=> $result['name'],
				'rate'			=> $result['rate'],
				'type'			=> ($result['type'] == 'F' ? $this->_('text_amount') : $this->_('text_percent')),
				'geo_zone'		=> $result['geo_zone'],
				'date_added'	=> $this->date->format($result['date_added'], $this->language->getInfo('date_format_short')),
				'date_modified' => $this->date->format($result['date_modified'], $this->language->getInfo('date_format_short')),
				'selected'		=> isset($_POST['selected']) && in_array($result['tax_rate_id'], $_POST['selected']),
				'action'		=> $action
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
		
		$this->data['sort_name'] = $this->url->link('localisation/tax_rate', 'sort=tr.name' . $url);
		$this->data['sort_rate'] = $this->url->link('localisation/tax_rate', 'sort=tr.rate' . $url);
		$this->data['sort_type'] = $this->url->link('localisation/tax_rate', 'sort=tr.type' . $url);
		$this->data['sort_geo_zone'] = $this->url->link('localisation/tax_rate', 'sort=gz.name' . $url);
		$this->data['sort_date_added'] = $this->url->link('localisation/tax_rate', 'sort=tr.date_added' . $url);
		$this->data['sort_date_modified'] = $this->url->link('localisation/tax_rate', 'sort=tr.date_modified' . $url);
		
		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}
												
		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total = $tax_rate_total;
		$this->data['pagination'] = $this->pagination->render();

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->data['breadcrumbs'] = $this->breadcrumb->render();
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function getForm()
	{
		$this->template->load('localisation/tax_rate_form');

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

 		if (isset($this->error['rate'])) {
			$this->data['error_rate'] = $this->error['rate'];
		} else {
			$this->data['error_rate'] = '';
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

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('localisation/tax_rate', $url));

		if (!isset($_GET['tax_rate_id'])) {
			$this->data['action'] = $this->url->link('localisation/tax_rate/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('localisation/tax_rate/update', 'tax_rate_id=' . $_GET['tax_rate_id'] . $url);
		}
		
		$this->data['cancel'] = $this->url->link('localisation/tax_rate', $url);

		if (isset($_GET['tax_rate_id']) && !$this->request->isPost()) {
			$tax_rate_info = $this->Model_Localisation_TaxRate->getTaxRate($_GET['tax_rate_id']);
		}

		if (isset($_POST['name'])) {
			$this->data['name'] = $_POST['name'];
		} elseif (!empty($tax_rate_info)) {
			$this->data['name'] = $tax_rate_info['name'];
		} else {
			$this->data['name'] = '';
		}
		
		if (isset($_POST['rate'])) {
			$this->data['rate'] = $_POST['rate'];
		} elseif (!empty($tax_rate_info)) {
			$this->data['rate'] = $tax_rate_info['rate'];
		} else {
			$this->data['rate'] = '';
		}
		
		if (isset($_POST['type'])) {
			$this->data['type'] = $_POST['type'];
		} elseif (!empty($tax_rate_info)) {
			$this->data['type'] = $tax_rate_info['type'];
		} else {
			$this->data['type'] = '';
		}
		
		if (isset($_POST['tax_rate_customer_group'])) {
			$this->data['tax_rate_customer_group'] = $_POST['tax_rate_customer_group'];
		} elseif (!empty($tax_rate_info)) {
			$this->data['tax_rate_customer_group'] = $this->Model_Localisation_TaxRate->getTaxRateCustomerGroups($_GET['tax_rate_id']);
		} else {
			$this->data['tax_rate_customer_group'] = array();
		}
		
		$this->data['customer_groups'] = $this->Model_Sale_CustomerGroup->getCustomerGroups();
						
		if (isset($_POST['geo_zone_id'])) {
			$this->data['geo_zone_id'] = $_POST['geo_zone_id'];
		} elseif (!empty($tax_rate_info)) {
			$this->data['geo_zone_id'] = $tax_rate_info['geo_zone_id'];
		} else {
			$this->data['geo_zone_id'] = '';
		}
				
		$this->data['geo_zones'] = $this->Model_Localisation_GeoZone->getGeoZones();
				
		$this->data['breadcrumbs'] = $this->breadcrumb->render();
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'localisation/tax_rate')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if ((strlen($_POST['name']) < 3) || (strlen($_POST['name']) > 32)) {
			$this->error['name'] = $this->_('error_name');
		}

		if (!$_POST['rate']) {
			$this->error['rate'] = $this->_('error_rate');
		}
								
		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->hasPermission('modify', 'localisation/tax_rate')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		foreach ($_POST['selected'] as $tax_rate_id) {
			$tax_rule_total = $this->Model_Localisation_Taxclass->getTotalTaxRulesByTaxRateId($tax_rate_id);

			if ($tax_rule_total) {
				$this->error['warning'] = sprintf($this->_('error_tax_rule'), $tax_rule_total);
			}
		}
				
		return $this->error ? false : true;
	}
}