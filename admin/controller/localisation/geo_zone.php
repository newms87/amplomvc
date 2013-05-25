<?php
class ControllerLocalisationGeoZone extends Controller { 
	
 
	public function index() {
		$this->load->language('localisation/geo_zone');

		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
	}

	public function insert() {
		$this->load->language('localisation/geo_zone');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_geo_zone->addGeoZone($_POST);
			
			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('localisation/geo_zone'));
		}

		$this->getForm();
	}

	public function update() {
		$this->load->language('localisation/geo_zone');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_geo_zone->editGeoZone($_GET['geo_zone_id'], $_POST);

			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('localisation/geo_zone'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('localisation/geo_zone');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $geo_zone_id) {
				$this->model_localisation_geo_zone->deleteGeoZone($geo_zone_id);
			}
						
			$this->message->add('success', $this->_('text_success'));
		}

		$this->getList();
	}

	private function getList() {
		$this->template->load('localisation/geo_zone_list');

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

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('localisation/geo_zone', $url));

		$this->data['insert'] = $this->url->link('localisation/geo_zone/insert', $url);
		$this->data['delete'] = $this->url->link('localisation/geo_zone/delete', $url);
		
		$this->data['geo_zones'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$geo_zone_total = $this->model_localisation_geo_zone->getTotalGeoZones();
		
		$results = $this->model_localisation_geo_zone->getGeoZones($data);

		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('localisation/geo_zone/update', 'geo_zone_id=' . $result['geo_zone_id'] . $url)
			);
					
			$this->data['geo_zones'][] = array(
				'geo_zone_id' => $result['geo_zone_id'],
				'name'		=> $result['name'],
				'description' => $result['description'],
				'selected'	=> isset($_POST['selected']) && in_array($result['geo_zone_id'], $_POST['selected']),
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
		
		$this->data['sort_name'] = $this->url->link('localisation/geo_zone', 'sort=name' . $url);
		$this->data['sort_description'] = $this->url->link('localisation/geo_zone', 'sort=description' . $url);
		
		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}
												
		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total = $geo_zone_total;
		$this->pagination->page = $page;
		$this->pagination->limit = $this->config->get('config_admin_limit');
		$this->pagination->url = $this->url->link('localisation/geo_zone', $url);

		$this->data['pagination'] = $this->pagination->render();

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function getForm() {
		$this->template->load('localisation/geo_zone_form');
		
		$geo_zone_id = isset($_GET['geo_zone_id']) ? $_GET['geo_zone_id'] : 0;
		
		$url = $this->url->get_query('sort', 'order', 'page');

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('localisation/geo_zone', $url));

		if (!$geo_zone_id) {
			$this->data['action'] = $this->url->link('localisation/geo_zone/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('localisation/geo_zone/update', 'geo_zone_id=' . $geo_zone_id . '&' . $url);
		}

		$this->data['cancel'] = $this->url->link('localisation/geo_zone', $url);

		if ($geo_zone_id && ($_SERVER['REQUEST_METHOD'] != 'POST')) {
			$geo_zone_info = $this->model_localisation_geo_zone->getGeoZone($geo_zone_id);
		}
		
		$defaults = array(
			'name' => '',
			'description' => '',
			'exclude' => 0,
			'zones' => array(),
		);
		
		foreach($defaults as $key => $default){
			if(isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif(isset($geo_zone_info[$key])) {
				$this->data[$key] = $geo_zone_info[$key];
			} elseif(!$geo_zone_id) {
				$this->data[$key] = $default;
			}
		}
		
		$this->data['data_countries'] = $this->model_localisation_country->getCountries();
		
		if(!isset($this->data['zones'])){
			$this->data['zones'] = $this->model_localisation_geo_zone->getZones($geo_zone_id);
		}
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}
	
	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'localisation/geo_zone')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if (!$this->validation->text($_POST['name'], 3, 32)) {
			$this->error['name'] = $this->_('error_name');
		}

		if (!$this->validation->text($_POST['description'], 3, 255)) {
			$this->error['description'] = $this->_('error_description');
		}
		
		if(empty($_POST['exclude'])){
			$_POST['exclude'] = 0;
		}
		
		return $this->error ? false : true;
	}

	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'localisation/geo_zone')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		foreach ($_POST['selected'] as $geo_zone_id) {
			$tax_rate_total = $this->model_localisation_tax_rate->getTotalTaxRatesByGeoZoneId($geo_zone_id);

			if ($tax_rate_total) {
				$this->error['warning'] = sprintf($this->_('error_tax_rate'), $tax_rate_total);
			}
		}
		
		return $this->error ? false : true;
	}
}