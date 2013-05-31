<?php
class Admin_Controller_Catalog_Attribute extends Controller 
{
	
	
  	public function index()
  	{
		$this->load->language('catalog/attribute');
	
		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
  	}
				
  	public function insert()
  	{
		$this->load->language('catalog/attribute');
	
		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
				$this->Model_Catalog_Attribute->addAttribute($_POST);
			
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
						
				$this->url->redirect($this->url->link('catalog/attribute', $url));
		}
	
		$this->getForm();
  	}

  	public function update()
  	{
		$this->load->language('catalog/attribute');
	
		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->Model_Catalog_Attribute->editAttribute($_GET['attribute_id'], $_POST);
			
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
			
			$this->url->redirect($this->url->link('catalog/attribute', $url));
		}
	
		$this->getForm();
  	}

  	public function delete()
  	{
		$this->load->language('catalog/attribute');
	
		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $attribute_id) {
				$this->Model_Catalog_Attribute->deleteAttribute($attribute_id);
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
			
			$this->url->redirect($this->url->link('catalog/attribute', $url));
			}
	
		$this->getList();
  	}
	
  	private function getList()
  	{
		$this->template->load('catalog/attribute_list');

		if (isset($_GET['sort'])) {
			$sort = $_GET['sort'];
		} else {
			$sort = 'ad.name';
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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/attribute', $url));

		$this->data['insert'] = $this->url->link('catalog/attribute/insert', $url);
		$this->data['delete'] = $this->url->link('catalog/attribute/delete', $url);

		$this->data['attributes'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$attribute_total = $this->Model_Catalog_Attribute->getTotalAttributes();
	
		$results = $this->Model_Catalog_Attribute->getAttributes($data);
 
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('catalog/attribute/update', 'attribute_id=' . $result['attribute_id'] . $url)
			);
						
			$this->data['attributes'][] = array(
				'attribute_id'	=> $result['attribute_id'],
				'name'				=> $result['name'],
				'attribute_group' => $result['attribute_group'],
				'sort_order'		=> $result['sort_order'],
				'selected'		=> isset($_POST['selected']) && in_array($result['attribute_id'], $_POST['selected']),
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
		
		$this->data['sort_name'] = $this->url->link('catalog/attribute', 'sort=ad.name' . $url);
		$this->data['sort_attribute_group'] = $this->url->link('catalog/attribute', 'sort=attribute_group' . $url);
		$this->data['sort_sort_order'] = $this->url->link('catalog/attribute', 'sort=a.sort_order' . $url);
		
		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}
												
		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total = $attribute_total;
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
		$this->template->load('catalog/attribute_form');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['name'])) {
			$this->data['error_name'] = $this->error['name'];
		} else {
			$this->data['error_name'] = array();
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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/attribute', $url));

		if (!isset($_GET['attribute_id'])) {
			$this->data['action'] = $this->url->link('catalog/attribute/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('catalog/attribute/update', 'attribute_id=' . $_GET['attribute_id'] . $url);
		}
			
		$this->data['cancel'] = $this->url->link('catalog/attribute', $url);

		if (isset($_GET['attribute_id']) && ($_SERVER['REQUEST_METHOD'] != 'POST')) {
			$attribute_info = $this->Model_Catalog_Attribute->getAttribute($_GET['attribute_id']);
		}
				
		$this->data['languages'] = $this->Model_Localisation_Language->getLanguages();
		
		if (isset($_POST['attribute_description'])) {
			$this->data['attribute_description'] = $_POST['attribute_description'];
		} elseif (isset($_GET['attribute_id'])) {
			$this->data['attribute_description'] = $this->Model_Catalog_Attribute->getAttributeDescriptions($_GET['attribute_id']);
		} else {
			$this->data['attribute_description'] = array();
		}

		if (isset($_POST['attribute_group_id'])) {
			$this->data['attribute_group_id'] = $_POST['attribute_group_id'];
		} elseif (!empty($attribute_info)) {
			$this->data['attribute_group_id'] = $attribute_info['attribute_group_id'];
		} else {
			$this->data['attribute_group_id'] = '';
		}
		
		$this->data['attribute_groups'] = $this->Model_Catalog_AttributeGroup->getAttributeGroups();

		if (isset($_POST['sort_order'])) {
			$this->data['sort_order'] = $_POST['sort_order'];
		} elseif (!empty($attribute_info)) {
			$this->data['sort_order'] = $attribute_info['sort_order'];
		} else {
			$this->data['sort_order'] = '';
		}
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
  	}
  	
	private function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'catalog/attribute')) {
				$this->error['warning'] = $this->_('error_permission');
		}
	
		foreach ($_POST['attribute_description'] as $language_id => $value) {
				if ((strlen($value['name']) < 3) || (strlen($value['name']) > 64)) {
				$this->error['name'][$language_id] = $this->_('error_name');
				}
		}
		
		return $this->error ? false : true;
  	}

  	private function validateDelete()
  	{
		if (!$this->user->hasPermission('modify', 'catalog/attribute')) {
				$this->error['warning'] = $this->_('error_permission');
		}
		
		foreach ($_POST['selected'] as $attribute_id) {
			$product_total = $this->Model_Catalog_Product->getTotalProductsByAttributeId($attribute_id);

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
	
	public function autocomplete()
	{
		$json = array();
		
		if (isset($_GET['filter_name'])) {
			$data = array(
				'filter_name' => $_GET['filter_name'],
				'start'		=> 0,
				'limit'		=> 20
			);
			
			$json = array();
			
			$results = $this->Model_Catalog_Attribute->getAttributes($data);
			
			foreach ($results as $result) {
				$json[] = array(
					'attribute_id'	=> $result['attribute_id'],
					'name'				=> $result['name'],
					'attribute_group' => $result['attribute_group']
				);
			}
		}

		$sort_order = array();
	
		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->setOutput(json_encode($json));
	}
}
