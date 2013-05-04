<?php
class ControllerLocalisationTaxClass extends Controller {
	
 
	public function index() {
		$this->load->language('localisation/tax_class');

		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList(); 
	}

	public function insert() {
		$this->load->language('localisation/tax_class');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_tax_class->addTaxClass($_POST);

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
			
			$this->redirect($this->url->link('localisation/tax_class', $url));
		}

		$this->getForm();
	}

	public function update() {
		$this->load->language('localisation/tax_class');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_tax_class->editTaxClass($_GET['tax_class_id'], $_POST);
			
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
			
			$this->redirect($this->url->link('localisation/tax_class', $url));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('localisation/tax_class');

		$this->document->setTitle($this->_('heading_title'));
 		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $tax_class_id) {
				$this->model_localisation_tax_class->deleteTaxClass($tax_class_id);
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
			
			$this->redirect($this->url->link('localisation/tax_class', $url));
		}

		$this->getList();
	}

	private function getList() {
		$this->template->load('localisation/tax_class_list');

		if (isset($_GET['sort'])) {
			$sort = $_GET['sort'];
		} else {
			$sort = 'title';
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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('localisation/tax_class', $url));

		$this->data['insert'] = $this->url->link('localisation/tax_class/insert', $url);
		$this->data['delete'] = $this->url->link('localisation/tax_class/delete', $url);		
		
		$this->data['tax_classes'] = array();
		
		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$tax_class_total = $this->model_localisation_tax_class->getTotalTaxClasses();

		$results = $this->model_localisation_tax_class->getTaxClasses($data);

		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('localisation/tax_class/update', 'tax_class_id=' . $result['tax_class_id'] . $url)
			);
					
			$this->data['tax_classes'][] = array(
				'tax_class_id' => $result['tax_class_id'],
				'title'        => $result['title'],
				'selected'     => isset($_POST['selected']) && in_array($result['tax_class_id'], $_POST['selected']),
				'action'       => $action				
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
		 
		$this->data['sort_title'] = $this->url->link('localisation/tax_class', 'sort=title' . $url);
		
		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}
												
		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total = $tax_class_total;
		$this->pagination->page = $page;
		$this->pagination->limit = $this->config->get('config_admin_limit');
		$this->pagination->text = $this->_('text_pagination');
		$this->pagination->url = $this->url->link('localisation/tax_class', $url . '&page={page}');

		$this->data['pagination'] = $this->pagination->render();

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->children = array(
			'common/header',
			'common/footer',
		);
				
		$this->response->setOutput($this->render());
	}

	private function getForm() {
		$this->template->load('localisation/tax_class_form');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['title'])) {
			$this->data['error_title'] = $this->error['title'];
		} else {
			$this->data['error_title'] = '';
		}
		
 		if (isset($this->error['description'])) {
			$this->data['error_description'] = $this->error['description'];
		} else {
			$this->data['error_description'] = '';
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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('localisation/tax_class', $url));

		if (!isset($_GET['tax_class_id'])) {
			$this->data['action'] = $this->url->link('localisation/tax_class/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('localisation/tax_class/update', 'tax_class_id=' . $_GET['tax_class_id'] . $url);
		}
		
		$this->data['cancel'] = $this->url->link('localisation/tax_class', $url);

		if (isset($_GET['tax_class_id']) && ($_SERVER['REQUEST_METHOD'] != 'POST')) {
			$tax_class_info = $this->model_localisation_tax_class->getTaxClass($_GET['tax_class_id']);
		}

		if (isset($_POST['title'])) {
			$this->data['title'] = $_POST['title'];
		} elseif (isset($tax_class_info)) {
			$this->data['title'] = $tax_class_info['title'];
		} else {
			$this->data['title'] = '';
		}

		if (isset($_POST['description'])) {
			$this->data['description'] = $_POST['description'];
		} elseif (isset($tax_class_info)) {
			$this->data['description'] = $tax_class_info['description'];
		} else {
			$this->data['description'] = '';
		}

		$this->data['tax_rates'] = $this->model_localisation_tax_rate->getTaxRates();
		
		if (isset($_POST['tax_rule'])) {
			$this->data['tax_rules'] = $_POST['tax_rule'];
		} elseif (isset($_GET['tax_class_id'])) {
			$this->data['tax_rules'] = $this->model_localisation_tax_class->getTaxRules($_GET['tax_class_id']);
		} else {
			$this->data['tax_rules'] = array();
		}

		$this->children = array(
			'common/header',
			'common/footer',
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'localisation/tax_class')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if ((strlen($_POST['title']) < 3) || (strlen($_POST['title']) > 32)) {
			$this->error['title'] = $this->_('error_title');
		}

		if ((strlen($_POST['description']) < 3) || (strlen($_POST['description']) > 255)) {
			$this->error['description'] = $this->_('error_description');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'localisation/tax_class')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		foreach ($_POST['selected'] as $tax_class_id) {
			$product_total = $this->model_catalog_product->getTotalProductsByTaxClassId($tax_class_id);

			if ($product_total) {
				$this->error['warning'] = sprintf($this->_('error_product'), $product_total);
			}
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}	
}