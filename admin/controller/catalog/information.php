<?php
class Admin_Controller_Catalog_Information extends Controller 
{
	public function index()
	{
		$this->load->language('catalog/information');

		$this->getList();
	}

	public function insert()
	{
		$this->load->language('catalog/information');
		
		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Catalog_Information->addInformation($_POST);
			
			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
				
				$this->url->redirect($this->url->link('catalog/information'));
			}
		}

		$this->getForm();
	}

	public function update()
	{
		$this->load->language('catalog/information');
		
		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Catalog_Information->editInformation($_GET['information_id'], $_POST);
			
			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
				
				$this->url->redirect($this->url->link('catalog/information'));
			}
		}

		$this->getForm();
	}
 	
	public function copy()
	{
		$this->load->language('catalog/information');

		if (isset($_GET['information_id']) && $this->validateCopy()) {
			$this->Model_Catalog_Information->copyInformation($_GET['information_id']);
			
			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
				
				$this->url->redirect($this->url->link('catalog/information', $this->url->get_query_exclude('information_id')));
			}
		}

		$this->getList();
	}
	
	public function delete()
	{
		$this->load->language('catalog/information');

		if (isset($_GET['information_id']) && $this->validateDelete()) {
			$this->Model_Catalog_Information->deleteInformation($_GET['information_id']);
			
			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
				
				$this->url->redirect($this->url->link('catalog/information', $this->url->get_query_exclude('information_id')));
			}
		}

		$this->getList();
	}
	
	public function batch_update()
	{
		$this->language->load('catalog/information');
		
		if (!empty($_POST['selected']) && isset($_GET['action'])) {
			if ($_GET['action'] !== 'delete' || $this->validateDelete()) { 
				foreach ($_POST['selected'] as $information_id) {
					switch($_GET['action']){
						case 'enable':
							$this->Model_Catalog_Information->updateField($information_id, array('status' => 1));
							break;
						case 'disable':
							$this->Model_Catalog_Information->updateField($information_id, array('status' => 0));
							break;
						case 'delete':
							$this->Model_Catalog_Information->deleteInformation($information_id);
							break;
						case 'copy':
							$this->Model_Catalog_Information->copyInformation($information_id);
							break;
					}
					
					if ($this->error) {
						break;
					}
				}
			}
			
			if (!$this->error && !$this->message->error_set()) {
				$this->message->add('success',$this->_('text_success'));
				
				$this->url->redirect($this->url->link('catalog/information', $this->url->get_query_exclude('action')));
			}
		}

		$this->getList();
	}
	
	private function getList()
	{
		$this->document->setTitle($this->_('heading_title'));
		
		$this->template->load('catalog/information_list');

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/information'));
		
		//The Table Columns
		$columns = array();

		$columns['title'] = array(
			'type' => 'text',
			'display_name' => $this->_('column_title'),
			'filter' => true,
			'sortable' => true,
		);
		
		$columns['stores'] = array(
			'type' => 'multiselect',
			'display_name' => $this->_('column_store'),
			'filter' => true,
			'build_config' => array('store_id' => 'name'),
			'build_data' => $this->Model_Setting_Store->getStores(),
			'sortable' => false,
		);
		
		$columns['status'] = array(
			'type' => 'select',
			'display_name' => $this->_('column_status'),
			'filter' => true,
			'build_data' => $this->_('data_statuses'),
			'sortable' => true,
		);
		
		//The Sort data
		$sort_filter = array();
		
		$this->sort->load_query_defaults($sort_filter, 'title', 'ASC');
		
		//Filter
		$filter_values = !empty($_GET['filter']) ? $_GET['filter'] : array();
		
		if ($filter_values) {
			$sort_filter += $filter_values;
		}

		$information_total = $this->Model_Catalog_Information->getTotalInformations($sort_filter);
		$informations = $this->Model_Catalog_Information->getInformations($sort_filter);
 		
		$url_query = $this->url->get_query_exclude('information_id');
		
		foreach ($informations as &$information) {
			$information['actions'] = array(
				'edit' => array(
					'text' => $this->_('text_edit'),
					'href' => $this->url->link('catalog/information/update', 'information_id=' . $information['information_id'])
				),
				'copy' => array(
					'text' => $this->_('text_copy'),
					'href' => $this->url->link('catalog/information/copy', 'information_id=' . $information['information_id'] . '&' . $url_query)
				),
				'delete' => array(
					'text' => $this->_('text_delete'),
					'href' => $this->url->link('catalog/information/delete', 'information_id=' . $information['information_id'] . '&' . $url_query)
				),
			);
			
			$information['stores'] = $this->Model_Catalog_Information->getInformationStores($information['information_id']);
		} unset($information);
		
		//The table template data
		$tt_data = array(
			'row_id'		=> 'information_id',
			'route'		=> 'catalog/information',
			'sort'		=> $sort_filter['sort'],
			'order'		=> $sort_filter['order'],
			'page'		=> $sort_filter['page'],
			'sort_url'	=> $this->url->link('catalog/information', $this->url->get_query('filter')),
			'columns'	=> $columns,
			'data'		=> $informations,
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
			'enable'	=> array(
				'label' => "Enable"
			),
			'disable'=>	array(
				'label' => "Disable",
			),
			'copy' => array(
				'label' => "Copy",
			),
			'delete' => array(
				'label' => "Delete",
			),
		);
		
		$this->data['batch_update'] = html_entity_decode($this->url->link('catalog/information/batch_update', $url_query));
		
		//Action Buttons
		$this->data['insert'] = $this->url->link('catalog/information/insert');
		
		//Item Limit Menu
		$this->data['limits'] = $this->sort->render_limit();
		
		//Pagination
		$this->pagination->init();
		$this->pagination->total = $information_total;
		
		$this->data['pagination'] = $this->pagination->render();
		
		//Dependencies
		$this->data['breadcrumbs'] = $this->breadcrumb->render();
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}

	private function getForm()
	{
		$this->document->setTitle($this->_('heading_title'));
		
		$this->template->load('catalog/information_form');
		
		$information_id = !empty($_GET['information_id']) ? $_GET['information_id'] : 0;
		
		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_information_list'), $this->url->link('catalog/information'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/information', 'information_id=' . $information_id));
		
		//Saved Data
		if ($information_id && !$this->request->isPost()) {
			$information_info = $this->Model_Catalog_Information->getInformation($_GET['information_id']);
			$information_info['stores'] = $this->Model_Catalog_Information->getInformationStores($information_id);
			$information_info['layouts'] = $this->Model_Catalog_Information->getInformationLayouts($information_id);
		}
		
		//Initialize data and default data
		$defaults = array(
			'title' => '',
			'description' => '',
			'keyword' => '',
			'stores' => array(0),
			'layouts' => array(),
			'sort_order' => 0,
			'status' => 1,
		);
		
		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($information_info[$key])) {
				$this->data[$key] = $information_info[$key];
			} else {
				$this->data[$key] = $default;
			}
		}
		
		//Data Lists
		$this->data['data_stores'] = $this->Model_Setting_Store->getStores();
		$this->data['data_layouts'] = $this->Model_Design_Layout->getLayouts();
		
		//Action Buttons
		if ($information_id) {
			$this->data['action'] = $this->url->link('catalog/information/update', 'information_id=' . $information_id);
		} else {
			$this->data['action'] = $this->url->link('catalog/information/insert');
		}
		
		$this->data['cancel'] = $this->url->link('catalog/information');
		
		//Translations
		$translate_fields = array(
			'title',
			'description',
		);
		
		$this->data['translations'] = $this->translation->get_translations('information', $information_id, $translate_fields);
		
		//Dependencies
		$this->data['breadcrumbs'] = $this->breadcrumb->render();
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'catalog/information')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (!$this->validation->text($_POST['title'], 3, 128)) {
			$this->error['title'] = $this->_('error_title');
		}

		if (!$this->validation->text($_POST['description'], 3)) {
			$this->error['description'] = $this->_('error_description');
		}
		
		if (!empty($_POST['translations'])) {
			foreach ($_POST['translations'] as $field => $translation) {
				foreach ($translation as $language_id => $text) {
					if (empty($text)) continue; //blank translations will revert to Default language
					
					if ($field === 'title' && !$this->validation->text($text, 3, 128)) {
						$this->error["translations[$field][$language_id]"] = $this->_('error_title_language', $this->language->getInfo('name', $language_id));
					}
					
					if ($field === 'description' && !$this->validation->text($text, 3)) {
						$this->error["translations[$field][$language_id]"] = $this->_('error_description_language', $this->language->getInfo('name', $language_id));
					}
				}
			}
		}
		
		return $this->error ? false : true;
	}
	
	private function validateCopy()
	{
		if (!$this->user->hasPermission('modify', 'catalog/information')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		return $this->error ? false : true;
	}
	
	private function validateDelete()
	{
		if (!$this->user->hasPermission('modify', 'catalog/information')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		$informations_ids = array();
		
		if (!empty($_POST['selected'])) {
			$information_ids = $_POST['selected'];
		} elseif (!empty($_GET['information_id'])) {
			$information_ids[] = $_GET['information_id'];
		}
		
		foreach ($information_ids as $information_id) {
			if ($this->config->get('config_account_id') == $information_id) {
				$this->error['warning' . $information_id] = $this->_('error_account');
			}
			
			if ($this->config->get('config_checkout_id') == $information_id) {
				$this->error['warning' . $information_id] = $this->_('error_checkout');
			}
			
			if ($this->config->get('config_affiliate_id') == $information_id) {
				$this->error['warning' . $information_id] = $this->_('error_affiliate');
			}
						
			$store_total = $this->Model_Setting_Store->getTotalStoresByInformationId($information_id);

			if ($store_total) {
				$this->error['warning' . $information_id] = sprintf($this->_('error_store'), $store_total);
			}
		}

		return $this->error ? false : true;
	}
}