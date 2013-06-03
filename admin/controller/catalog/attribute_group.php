<?php
class Admin_Controller_Catalog_AttributeGroup extends Controller 
{
	
	
  	public function index()
  	{
		$this->load->language('catalog/attribute_group');
	
		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
  	}
				
  	public function insert()
  	{
		$this->load->language('catalog/attribute_group');
	
		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
				$this->Model_Catalog_AttributeGroup->addAttributeGroup($_POST);
			
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
						
				$this->url->redirect($this->url->link('catalog/attribute_group', $url));
		}
	
		$this->getForm();
  	}

  	public function update()
  	{
		$this->load->language('catalog/attribute_group');
	
		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->Model_Catalog_AttributeGroup->editAttributeGroup($_GET['attribute_group_id'], $_POST);
			
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
			
			$this->url->redirect($this->url->link('catalog/attribute_group', $url));
		}
	
		$this->getForm();
  	}

  	public function delete()
  	{
		$this->load->language('catalog/attribute_group');
	
		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $attribute_group_id) {
				$this->Model_Catalog_AttributeGroup->deleteAttributeGroup($attribute_group_id);
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
			
			$this->url->redirect($this->url->link('catalog/attribute_group', $url));
			}
	
		$this->getList();
  	}
	
  	private function getList()
  	{
		$this->template->load('catalog/attribute_group_list');

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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/attribute_group', $url));

		$this->data['insert'] = $this->url->link('catalog/attribute_group/insert', $url);
		$this->data['delete'] = $this->url->link('catalog/attribute_group/delete', $url);

		$this->data['attribute_groups'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$attribute_group_total = $this->Model_Catalog_AttributeGroup->getTotalAttributeGroups();
	
		$results = $this->Model_Catalog_AttributeGroup->getAttributeGroups($data);
 
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('catalog/attribute_group/update', 'attribute_group_id=' . $result['attribute_group_id'] . $url)
			);
						
			$this->data['attribute_groups'][] = array(
				'attribute_group_id' => $result['attribute_group_id'],
				'name'					=> $result['name'],
				'sort_order'			=> $result['sort_order'],
				'selected'			=> isset($_POST['selected']) && in_array($result['attribute_group_id'], $_POST['selected']),
				'action'				=> $action
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
		
		$this->data['sort_name'] = $this->url->link('catalog/attribute_group', 'sort=agd.name' . $url);
		$this->data['sort_sort_order'] = $this->url->link('catalog/attribute_group', 'sort=ag.sort_order' . $url);
		
		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}
												
		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total = $attribute_group_total;
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
		$this->template->load('catalog/attribute_group_form');

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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/attribute_group', $url));

		if (!isset($_GET['attribute_group_id'])) {
			$this->data['action'] = $this->url->link('catalog/attribute_group/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('catalog/attribute_group/update', 'attribute_group_id=' . $_GET['attribute_group_id'] . $url);
		}
			
		$this->data['cancel'] = $this->url->link('catalog/attribute_group', $url);

		if (isset($_GET['attribute_group_id']) && ($_SERVER['REQUEST_METHOD'] != 'POST')) {
			$attribute_group_info = $this->Model_Catalog_AttributeGroup->getAttributeGroup($_GET['attribute_group_id']);
		}
				
		$this->data['languages'] = $this->Model_Localisation_Language->getLanguages();
		
		if (isset($_POST['attribute_group_description'])) {
			$this->data['attribute_group_description'] = $_POST['attribute_group_description'];
		} elseif (isset($_GET['attribute_group_id'])) {
			$this->data['attribute_group_description'] = $this->Model_Catalog_AttributeGroup->getAttributeGroupDescriptions($_GET['attribute_group_id']);
		} else {
			$this->data['attribute_group_description'] = array();
		}

		if (isset($_POST['sort_order'])) {
			$this->data['sort_order'] = $_POST['sort_order'];
		} elseif (!empty($attribute_group_info)) {
			$this->data['sort_order'] = $attribute_group_info['sort_order'];
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
		if (!$this->user->hasPermission('modify', 'catalog/attribute_group')) {
				$this->error['warning'] = $this->_('error_permission');
		}
	
		foreach ($_POST['attribute_group_description'] as $language_id => $value) {
				if ((strlen($value['name']) < 3) || (strlen($value['name']) > 64)) {
				$this->error['name'][$language_id] = $this->_('error_name');
				}
		}
		
		return $this->error ? false : true;
  	}

  	private function validateDelete()
  	{
		if (!$this->user->hasPermission('modify', 'catalog/attribute_group')) {
				$this->error['warning'] = $this->_('error_permission');
		}
		
		foreach ($_POST['selected'] as $attribute_group_id) {
			$attribute_total = $this->Model_Catalog_Attribute->getTotalAttributesByAttributeGroupId($attribute_group_id);

			if ($attribute_total) {
				$this->error['warning'] = sprintf($this->_('error_attribute'), $attribute_total);
			}
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
  	}
}