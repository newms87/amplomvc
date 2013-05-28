<?php
class ControllerCatalogInformation extends Controller {
	

	public function index() {
		$this->load->language('catalog/information');

		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
	}

	public function insert() {
		$this->load->language('catalog/information');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_information->addInformation($_POST);
			
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
			
			$this->url->redirect($this->url->link('catalog/information', $url));
		}

		$this->getForm();
	}

	public function update() {
		$this->load->language('catalog/information');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_information->editInformation($_GET['information_id'], $_POST);
			
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
			
			$this->url->redirect($this->url->link('catalog/information', $url));
		}

		$this->getForm();
	}
 
	public function delete() {
		$this->load->language('catalog/information');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $information_id) {
				$this->model_catalog_information->deleteInformation($information_id);
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
			
			$this->url->redirect($this->url->link('catalog/information', $url));
		}

		$this->getList();
	}

	private function getList() {
		$this->template->load('catalog/information_list');

		if (isset($_GET['sort'])) {
			$sort = $_GET['sort'];
		} else {
			$sort = 'id.title';
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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/information', $url));

		$this->data['insert'] = $this->url->link('catalog/information/insert', $url);
		$this->data['delete'] = $this->url->link('catalog/information/delete', $url);

		$this->data['informations'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$information_total = $this->model_catalog_information->getTotalInformations();
	
		$results = $this->model_catalog_information->getInformations($data);
 
		foreach ($results as $result) {
			$action = array();
						
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('catalog/information/update', 'information_id=' . $result['information_id'] . $url)
			);
						
			$this->data['informations'][] = array(
				'information_id' => $result['information_id'],
				'title'			=> $result['title'],
				'sort_order'	=> $result['sort_order'],
				'selected'		=> isset($_POST['selected']) && in_array($result['information_id'], $_POST['selected']),
				'action'			=> $action
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
		
		$this->data['sort_title'] = $this->url->link('catalog/information', 'sort=id.title' . $url);
		$this->data['sort_sort_order'] = $this->url->link('catalog/information', 'sort=i.sort_order' . $url);
		
		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}
												
		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total = $information_total;
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
		$this->template->load('catalog/information_form');

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['title'])) {
			$this->data['error_title'] = $this->error['title'];
		} else {
			$this->data['error_title'] = array();
		}
		
		if (isset($this->error['description'])) {
			$this->data['error_description'] = $this->error['description'];
		} else {
			$this->data['error_description'] = array();
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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/information', $url));

		if (!isset($_GET['information_id'])) {
			$this->data['action'] = $this->url->link('catalog/information/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('catalog/information/update', 'information_id=' . $_GET['information_id'] . $url);
		}
		
		$this->data['cancel'] = $this->url->link('catalog/information', $url);

		if (isset($_GET['information_id']) && ($_SERVER['REQUEST_METHOD'] != 'POST')) {
			$information_info = $this->model_catalog_information->getInformation($_GET['information_id']);
		}
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
		if (isset($_POST['information_description'])) {
			$this->data['information_description'] = $_POST['information_description'];
		} elseif (isset($_GET['information_id'])) {
			$this->data['information_description'] = $this->model_catalog_information->getInformationDescriptions($_GET['information_id']);
		} else {
			$this->data['information_description'] = array();
		}

		if (isset($_POST['status'])) {
			$this->data['status'] = $_POST['status'];
		} elseif (!empty($information_info)) {
			$this->data['status'] = $information_info['status'];
		} else {
			$this->data['status'] = 1;
		}
		
		$this->data['data_stores'] = $this->model_setting_store->getStores();
		
		if (isset($_POST['information_store'])) {
			$this->data['information_store'] = $_POST['information_store'];
		} elseif (isset($_GET['information_id'])) {
			$this->data['information_store'] = $this->model_catalog_information->getInformationStores($_GET['information_id']);
		} else {
			$this->data['information_store'] = array(0);
		}
		
		if (isset($_POST['keyword'])) {
			$this->data['keyword'] = $_POST['keyword'];
		} elseif (!empty($information_info)) {
			$this->data['keyword'] = $information_info['keyword'];
		} else {
			$this->data['keyword'] = '';
		}
		
		if (isset($_POST['sort_order'])) {
			$this->data['sort_order'] = $_POST['sort_order'];
		} elseif (!empty($information_info)) {
			$this->data['sort_order'] = $information_info['sort_order'];
		} else {
			$this->data['sort_order'] = '';
		}
		
		if (isset($_POST['information_layout'])) {
			$this->data['information_layout'] = $_POST['information_layout'];
		} elseif (isset($_GET['information_id'])) {
			$this->data['information_layout'] = $this->model_catalog_information->getInformationLayouts($_GET['information_id']);
		} else {
			$this->data['information_layout'] = array();
		}

		$this->data['layouts'] = $this->model_design_layout->getLayouts();
				
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/information')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		foreach ($_POST['information_description'] as $language_id => $value) {
			if ((strlen($value['title']) < 3) || (strlen($value['title']) > 64)) {
				$this->error['title'][$language_id] = $this->_('error_title');
			}
		
			if (strlen($value['description']) < 3) {
				$this->error['description'][$language_id] = $this->_('error_description');
			}
		}
		
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->_('error_warning');
		}
			
		return $this->error ? false : true;
	}

	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/information')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		foreach ($_POST['selected'] as $information_id) {
			if ($this->config->get('config_account_id') == $information_id) {
				$this->error['warning'] = $this->_('error_account');
			}
			
			if ($this->config->get('config_checkout_id') == $information_id) {
				$this->error['warning'] = $this->_('error_checkout');
			}
			
			if ($this->config->get('config_affiliate_id') == $information_id) {
				$this->error['warning'] = $this->_('error_affiliate');
			}
						
			$store_total = $this->model_setting_store->getTotalStoresByInformationId($information_id);

			if ($store_total) {
				$this->error['warning'] = sprintf($this->_('error_store'), $store_total);
			}
		}

		return $this->error ? false : true;
	}
}