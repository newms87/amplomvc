<?php
class Admin_Controller_Design_Layout extends Controller 
{
	
	public function index()
	{
		$this->load->language('design/layout');

		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
	}

	public function insert()
	{
		$this->load->language('design/layout');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Design_Layout->addLayout($_POST);
			
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
			
			$this->url->redirect($this->url->link('design/layout', $url));
		}

		$this->getForm();
	}

	public function update()
	{
		$this->load->language('design/layout');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Design_Layout->editLayout($_GET['layout_id'], $_POST);

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
					
			$this->url->redirect($this->url->link('design/layout', $url));
		}

		$this->getForm();
	}
 
	public function delete()
	{
		$this->load->language('design/layout');
 
		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $layout_id) {
				$this->Model_Design_Layout->deleteLayout($layout_id);
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

			$this->url->redirect($this->url->link('design/layout', $url));
		}

		$this->getList();
	}

	private function getList()
	{
		$this->template->load('design/layout_list');

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
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('design/layout', $url));

		$this->data['insert'] = $this->url->link('design/layout/insert', $url);
		$this->data['delete'] = $this->url->link('design/layout/delete', $url);
		
		$this->data['layouts'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$layout_total = $this->Model_Design_Layout->getTotalLayouts();
		
		$results = $this->Model_Design_Layout->getLayouts($data);
		
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('design/layout/update', 'layout_id=' . $result['layout_id'] . $url)
			);

			$this->data['layouts'][] = array(
				'layout_id' => $result['layout_id'],
				'name'		=> $result['name'],
				'selected'  => isset($_POST['selected']) && in_array($result['layout_id'], $_POST['selected']),
				'action'	=> $action
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
		
		$this->data['sort_name'] = $this->url->link('design/layout', 'sort=name' . $url);
		
		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}
												
		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total = $layout_total;
		$this->data['pagination'] = $this->pagination->render();
		
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function getForm()
	{
		$this->template->load('design/layout_form');

		$this->data['languages'] = $this->Model_Localisation_Language->getLanguages();
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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('design/layout', $url));

		if (!isset($_GET['layout_id'])) {
			$this->data['action'] = $this->url->link('design/layout/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('design/layout/update', 'layout_id=' . $_GET['layout_id'] . $url);
		}
		
		$this->data['cancel'] = $this->url->link('design/layout', $url);
		
		if (isset($_GET['layout_id']) && !$this->request->isPost()) {
			$layout_info = $this->Model_Design_Layout->getLayout($_GET['layout_id']);
		}

		if (isset($_POST['name'])) {
			$this->data['name'] = $_POST['name'];
		} elseif (!empty($layout_info)) {
			$this->data['name'] = $layout_info['name'];
		} else {
			$this->data['name'] = '';
		}
		
		
		if (isset($_POST['layout_header'])) {
			$this->data['layout_header'] = $_POST['layout_header'];
		} elseif (isset($_GET['layout_id'])) {
			$this->data['layout_header'] = $this->Model_Design_Layout->getLayoutPageHeaders($_GET['layout_id']);
		} else {
			$this->data['layout_header'] = array();
		}
		
		
		$this->data['data_stores'] = $this->Model_Setting_Store->getStores();
		
		if (isset($_POST['layout_route'])) {
			$this->data['layout_routes'] = $_POST['layout_route'];
		} elseif (isset($_GET['layout_id'])) {
			$this->data['layout_routes'] = $this->Model_Design_Layout->getLayoutRoutes($_GET['layout_id']);
		} else {
			$this->data['layout_routes'] = array();
		}
				
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'design/layout')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if ((strlen($_POST['name']) < 3) || (strlen($_POST['name']) > 64)) {
			$this->error['name'] = $this->_('error_name');
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->hasPermission('modify', 'design/layout')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		foreach ($_POST['selected'] as $layout_id) {
			if ($this->config->get('config_default_layout_id') == $layout_id) {
				$this->error['warning'] = $this->_('error_default');
			}
			
			$data = array(
				'layouts' => array($layout_id),
			);
			
			$product_total = $this->Model_Catalog_Product->getTotalProducts($data);
	
			if ($product_total) {
				$this->error['warning_product'] = $this->_('error_product', $product_total);
			}

			$category_total = $this->Model_Catalog_Category->getTotalCategories($data);
	
			if ($category_total) {
				$this->error['warning_category'] = $this->_('error_category', $category_total);
			}
			
			$information_total = $this->Model_Catalog_Information->getTotalInformations($data);
		
			if ($information_total) {
				$this->error['warning_information'] = $this->_('error_information', $information_total);
			}
		}
	
		return $this->error ? false : true;
	}
}