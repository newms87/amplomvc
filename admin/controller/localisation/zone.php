<?php 
class ControllerLocalisationZone extends Controller {
	 

	public function index() {
		$this->load->language('localisation/zone');

		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
	}

	public function insert() {
		$this->load->language('localisation/zone');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_zone->addZone($_POST);
	
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
			
			$this->redirect($this->url->link('localisation/zone', $url));
		}

		$this->getForm();
	}

	public function update() {
		$this->load->language('localisation/zone');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_zone->editZone($_GET['zone_id'], $_POST);			
			
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
			
			$this->redirect($this->url->link('localisation/zone', $url));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('localisation/zone');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $zone_id) {
				$this->model_localisation_zone->deleteZone($zone_id);
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

			$this->redirect($this->url->link('localisation/zone', $url));
		}

		$this->getList();
	}

	private function getList() {
$this->template->load('localisation/zone_list');

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

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('localisation/zone', $url));

		$this->data['insert'] = $this->url->link('localisation/zone/insert', $url);
		$this->data['delete'] = $this->url->link('localisation/zone/delete', $url);
	
		$this->data['zones'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$zone_total = $this->model_localisation_zone->getTotalZones();
			
		$results = $this->model_localisation_zone->getZones($data);

		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('localisation/zone/update', 'zone_id=' . $result['zone_id'] . $url)
			);
					
			$this->data['zones'][] = array(
				'zone_id'  => $result['zone_id'],
				'country'  => $result['country'],
				'name'     => $result['name'] . (($result['zone_id'] == $this->config->get('config_zone_id')) ? $this->_('text_default') : null),
				'code'     => $result['code'],
				'selected' => isset($_POST['selected']) && in_array($result['zone_id'], $_POST['selected']),
				'action'   => $action			
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
		 
		$this->data['sort_country'] = $this->url->link('localisation/zone', 'sort=c.name' . $url);
		$this->data['sort_name'] = $this->url->link('localisation/zone', 'sort=z.name' . $url);
		$this->data['sort_code'] = $this->url->link('localisation/zone', 'sort=z.code' . $url);
		
		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}
												
		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total = $zone_total;
		$this->pagination->page = $page;
		$this->pagination->limit = $this->config->get('config_admin_limit');
		$this->pagination->text = $this->_('text_pagination');
		$this->pagination->url = $this->url->link('localisation/zone', $url . '&page={page}');

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
$this->template->load('localisation/zone_form');

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

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('localisation/zone', $url));

		if (!isset($_GET['zone_id'])) {
			$this->data['action'] = $this->url->link('localisation/zone/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('localisation/zone/update', 'zone_id=' . $_GET['zone_id'] . $url);
		}
		 
		$this->data['cancel'] = $this->url->link('localisation/zone', $url);

		if (isset($_GET['zone_id']) && ($_SERVER['REQUEST_METHOD'] != 'POST')) {
			$zone_info = $this->model_localisation_zone->getZone($_GET['zone_id']);
		}

		if (isset($_POST['status'])) {
			$this->data['status'] = $_POST['status'];
		} elseif (isset($zone_info)) {
			$this->data['status'] = $zone_info['status'];
		} else {
			$this->data['status'] = '1';
		}
		
		if (isset($_POST['name'])) {
			$this->data['name'] = $_POST['name'];
		} elseif (isset($zone_info)) {
			$this->data['name'] = $zone_info['name'];
		} else {
			$this->data['name'] = '';
		}

		if (isset($_POST['code'])) {
			$this->data['code'] = $_POST['code'];
		} elseif (isset($zone_info)) {
			$this->data['code'] = $zone_info['code'];
		} else {
			$this->data['code'] = '';
		}

		if (isset($_POST['country_id'])) {
			$this->data['country_id'] = $_POST['country_id'];
		} elseif (isset($zone_info)) {
			$this->data['country_id'] = $zone_info['country_id'];
		} else {
			$this->data['country_id'] = '';
		}
		
		$this->data['countries'] = $this->model_localisation_country->getCountries();

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'localisation/zone')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if ((strlen($_POST['name']) < 3) || (strlen($_POST['name']) > 64)) {
			$this->error['name'] = $this->_('error_name');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'localisation/zone')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		foreach ($_POST['selected'] as $zone_id) {
			if ($this->config->get('config_zone_id') == $zone_id) {
				$this->error['warning'] = $this->_('error_default');
			}
			
			$store_total = $this->model_setting_store->getTotalStoresByZoneId($zone_id);

			if ($store_total) {
				$this->error['warning'] = sprintf($this->_('error_store'), $store_total);
			}
		
			$address_total = $this->model_sale_customer->getTotalAddressesByZoneId($zone_id);

			if ($address_total) {
				$this->error['warning'] = sprintf($this->_('error_address'), $address_total);
			}

			$affiliate_total = $this->model_sale_affiliate->getTotalAffiliatesByZoneId($zone_id);

			if ($affiliate_total) {
				$this->error['warning'] = sprintf($this->_('error_affiliate'), $affiliate_total);
			}
					
			$zone_to_geo_zone_total = $this->model_localisation_geo_zone->getTotalZoneToGeoZoneByZoneId($zone_id);
		
			if ($zone_to_geo_zone_total) {
				$this->error['warning'] = sprintf($this->_('error_zone_to_geo_zone'), $zone_to_geo_zone_total);
			}
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}