<?php
class Admin_Controller_Catalog_AttributeGroup extends Controller 
{
  	public function index()
  	{
		$this->load->language('catalog/attribute_group');
		
		$this->getList();
  	}
				
  	public function insert()
	{
		$this->load->language('catalog/attribute_group');
		
		if (($this->request->isPost()) && $this->validateForm()) {
			$this->Model_Catalog_AttributeGroup->addAttributeGroup($_POST);
			
			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
				
				$this->url->redirect($this->url->link('catalog/attribute_group'));
			}
		}

		$this->getForm();
	}

	public function update()
	{
		$this->load->language('catalog/attribute_group');

		if (($this->request->isPost()) && $this->validateForm()) {
			$this->Model_Catalog_AttributeGroup->editAttributeGroup($_GET['attribute_group_id'], $_POST);
			
			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
			
				$this->url->redirect($this->url->link('catalog/attribute_group'));
			}
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->load->language('catalog/attribute_group');
		
		if (!empty($_GET['attribute_group_id']) && $this->validateDelete()) {
			$this->Model_Catalog_AttributeGroup->deleteAttributeGroup($_GET['attribute_group_id']);
			
			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));

				$this->url->redirect($this->url->link('catalog/attribute_group'));
			}
		}

		$this->getList();
	}
	
	public function batch_update()
	{
		$this->language->load('catalog/attribute_group');
		
		if (!empty($_POST['selected']) && isset($_GET['action'])) {
			foreach ($_POST['selected'] as $attribute_group_id) {
				switch($_GET['action']){
					case 'delete':
						if ($this->validateDelete()) {
							$this->Model_Catalog_AttributeGroup->deleteAttributeGroup($attribute_group_id);
						}
						break;
				}
				
				if ($this->error) {
					break;
				}
			}
			
			if (!$this->error && !$this->message->error_set()) {
				$this->message->add('success',$this->_('text_success'));
				
				$this->url->redirect($this->url->link('catalog/attribute_group'));
			}
		}

		$this->getList();
	}
	
	private function getList()
	{
		$this->document->setTitle($this->_('heading_title'));
		
		$this->template->load('catalog/attribute_group_list');

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/attribute_group'));
		
		//The Table Columns
		$columns = array();
		
		$columns['name'] = array(
			'type' => 'text',
			'display_name' => $this->_('column_name'),
			'filter' => true,
			'sortable' => true,
		);
		
		$columns['attribute_count'] = array(
			'type' => 'int',
			'display_name' => $this->_('column_attribute_count'),
			'filter' => false,
			'sortable' => true,
		);
		
		$columns['sort_order'] = array(
			'type' => 'int',
			'display_name' => $this->_('column_sort_order'),
			'filter' => false,
			'sortable' => true,
		);
		
		//The Sort data
		$sort_filter = array();
		
		$this->sort->load_query_defaults($sort_filter, 'name', 'ASC');
		
		//Filter
		$filter_values = !empty($_GET['filter']) ? $_GET['filter'] : array();
		
		if ($filter_values) {
			$sort_filter += $filter_values;
		}
		
		//This triggers the attribute_count to be added to the query
		if (!isset($sort_filter['attribute_count'])) {
			$sort_filter['attribute_count'] = true;
		}
		
		//Retrieve the Filtered Table row data
		$attribute_group_total = $this->Model_Catalog_AttributeGroup->getTotalAttributeGroups($sort_filter);
		$attribute_groups = $this->Model_Catalog_AttributeGroup->getAttributeGroups($sort_filter);
		
		foreach ($attribute_groups as &$attribute_group) {
			$attribute_group['actions'] = array(
				'edit' => array(
					'text' => $this->_('text_edit'),
					'href' => $this->url->link('catalog/attribute_group/update', 'attribute_group_id=' . $attribute_group['attribute_group_id'])
				),
				'delete' => array(
					'text' => $this->_('text_delete'),
					'href' => $this->url->link('catalog/attribute_group/delete', 'attribute_group_id=' . $attribute_group['attribute_group_id'])
				)
			);
		}unset($attribute_group);
		
		//The table template data
		$tt_data = array(
			'row_id'		=> 'attribute_group_id',
			'route'		=> 'catalog/attribute_group',
			'sort'		=> $sort_filter['sort'],
			'order'		=> $sort_filter['order'],
			'page'		=> $sort_filter['page'],
			'sort_url'	=> $this->url->link('catalog/attribute_group', $this->url->get_query('filter')),
			'columns'	=> $columns,
			'data'		=> $attribute_groups,
		);
		
		$tt_data += $this->language->data;
		
		//Build the table template
		$this->table->init();
		$this->table->set_template('table/list_view');
		$this->table->set_template_data($tt_data);
		$this->table->map_attribute('filter_value', $filter_values);
		
		$this->data['list_view'] = $this->table->render();
		
		//Batch Actions
		$this->data['batch_actions'] = array(
			'delete' => array(
				'label' => "Delete",
			),
		);
		
		$url_query = $this->url->get_query('filter', 'sort', 'order', 'page');
		
		$this->data['batch_update'] = html_entity_decode($this->url->link('catalog/attribute_group/batch_update', $url_query));
		
		//Action Buttons
		$this->data['insert'] = $this->url->link('catalog/attribute_group/insert');
		
		//Item Limit Menu
		$this->data['limits'] = $this->sort->render_limit();
		
		//Pagination
		$this->pagination->init();
		$this->pagination->total = $attribute_group_total;
		
		$this->data['pagination'] = $this->pagination->render();
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
  	
  	private function getForm()
  	{
  		$this->document->setTitle($this->_('heading_title'));
		
		$this->template->load('catalog/attribute_group_form');
		
		$attribute_group_id = !empty($_GET['attribute_group_id']) ? $_GET['attribute_group_id'] : 0;

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/attribute_group'));

		if (!$attribute_group_id) {
			$this->data['action'] = $this->url->link('catalog/attribute_group/insert');
		} else {
			$this->data['action'] = $this->url->link('catalog/attribute_group/update', 'attribute_group_id=' . $attribute_group_id);
		}
		
		$this->data['cancel'] = $this->url->link('catalog/attribute_group');

		if ($attribute_group_id && (!$this->request->isPost())) {
			$attribute_group_info = $this->Model_Catalog_AttributeGroup->getAttributeGroup($attribute_group_id);
			
			$attributes = $this->Model_Catalog_AttributeGroup->getAttributes($attribute_group_id);
			
			foreach ($attributes as &$attribute) {
				$count = $this->Model_Catalog_AttributeGroup->getAttributeProductCount($attribute['attribute_id']);
				
				if ($count) {
					$attribute['product_count'] = $this->language->format('text_product_count', $count);
				}
			}
			
			$attribute_group_info['attributes'] = $attributes;
		}
		
		$defaults = array(
			'name' => '',
			'sort_order' => '',
			'attributes' => array(),
		);
		
		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($attribute_group_info[$key])) {
				$this->data[$key] = $attribute_group_info[$key];
			} else {
				$this->data[$key] = $default;
			}
		}
		
		//Translation for Attribute Group
		$translate_fields = array(
			'name',
		);
		
		$this->data['translations'] = $this->translation->get_translations('attribute_group', $attribute_group_id, $translate_fields);
		
		//Translations for Attributes
		$translate_fields = array(
			'name',
		);
		
		foreach ($this->data['attributes'] as &$attribute) {
			$attribute['translations'] = $this->translation->get_translations('attribute', $attribute['attribute_id'], $translate_fields);
		};
		
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
		
		if (!$this->validation->text($_POST['name'], 3, 64)) {
			$this->error['name'] = $this->_('error_name');
		}
		
		return $this->error ? false : true;
  	}

  	private function validateDelete()
  	{
		if (!$this->user->hasPermission('modify', 'catalog/attribute_group')) {
				$this->error['warning'] = $this->_('error_permission');
		}
		
		$attribute_group_ids = array();
		
		if (!empty($_GET['attribute_group_id'])) {
			$attribute_group_ids[] = $_GET['attribute_group_id'];
		}
		
		if (!empty($_POST['selected'])) {
			$attribute_group_ids = array_merge($_POST['selected'], $attribute_group_ids);
		}
		
		foreach ($attribute_group_ids as $attribute_group_id) {
			if ($attribute_total = $this->Model_Catalog_AttributeGroup->hasProductAssociation($attribute_group_id)) {
				$attribute_group = $this->Model_Catalog_AttributeGroup->getAttributeGroup($attribute_group_id);
				
				$this->error['warning_' . $attribute_group_id] = $this->language->format('error_attribute', $attribute_group['name'], $attribute_total);
			}
		}
		
		return $this->error ? false : true;
  	}
	
	public function autocomplete()
	{
		$json = array();
		
		if (isset($_GET['name'])) {
			$data = array(
				'name' => $_GET['name'],
				'start'		=> 0,
				'limit'		=> 20
			);
			
			$json = array();
			
			$results = $this->Model_Catalog_AttributeGroup->getAttributesFilter($data);
			
			foreach ($results as $result) {
				$json[] = array(
					'attribute_id'	=> $result['attribute_id'],
					'name'				=> $result['name'],
					'attribute_group' => $result['attribute_group_id']
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