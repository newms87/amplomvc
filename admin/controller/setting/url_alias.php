<?php
class Admin_Controller_Setting_UrlAlias extends Controller
{
	public function index()
	{
		$this->language->load('setting/url_alias');

		$this->getList();
	}
	
  	public function update()
  	{
		$this->language->load('setting/url_alias');

		if ($this->request->isPost() && $this->validateForm()) {
			//Insert
			if (empty($_GET['url_alias_id'])) {
				$this->Model_Setting_UrlAlias->addUrlAlias($_POST);
			}
			//Update
			else {
				$this->Model_Setting_UrlAlias->editUrlAlias($_GET['url_alias_id'], $_POST);
			}
			
			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
				
				$this->url->redirect($this->url->link('setting/url_alias'));
			}
		}

		$this->getForm();
  	}

  	public function delete()
  	{
		$this->language->load('setting/url_alias');
		
		if (!empty($_GET['url_alias_id']) && $this->validateDelete()) {
			$this->Model_Setting_UrlAlias->deleteUrlAlias($_GET['url_alias_id']);

			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
				
				$this->url->redirect($this->url->link('setting/url_alias'));
			}
		}
		
		$this->index();
  	}
	
	public function batch_update()
	{
		$this->language->load('setting/url_alias');
		
		if (!empty($_GET['selected']) && isset($_GET['action'])) {
			foreach ($_GET['selected'] as $url_alias_id) {
				$data = array();
				
				switch($_GET['action']){
					case 'enable':
						$data['status'] = 1;
						break;
					case 'disable':
						$data['status'] = 0;
						break;
					case 'delete':
						$this->Model_Setting_UrlAlias->deleteUrlAlias($url_alias_id);
						break;
					default:
						break 2; //Break For loop
				}

				$this->Model_Setting_UrlAlias->editUrlAlias($url_alias_id, $data);
			}
			
			if (!$this->message->error_set()) {
				$this->message->add('success',$this->_('text_success'));
			}
		}

		$this->url->redirect($this->url->link('setting/url_alias', $this->url->getQueryExclude('action', 'action_value')));
	}
	
	private function getList()
	{
		//Page Head
		$this->document->setTitle($this->_('head_title'));
		
		//Template
		$this->template->load('setting/url_alias_list');

		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('setting/url_alias'));
		
		//The Table Columns
		$columns = array();

		$columns['alias'] = array(
			'type' => 'text',
			'display_name' => $this->_('column_alias'),
			'filter' => true,
			'sortable' => true,
		);
		
		$columns['path'] = array(
			'type' => 'text',
			'display_name' => $this->_('column_path'),
			'filter' => true,
			'sortable' => true,
		);
		
		$columns['query'] = array(
			'type' => 'text',
			'display_name' => $this->_('column_query'),
			'filter' => true,
			'sortable' => true,
		);
		
		$columns['store_id'] = array(
			'type' => 'select',
			'display_name' => $this->_('column_store'),
			'filter' => true,
			'build_config' => array('store_id', 'name'),
			'build_data' => array_merge($this->_('data_non_stores'), $this->Model_Setting_Store->getStores()),
			'sortable' => false,
		);
		
		$columns['status'] = array(
			'type' => 'select',
			'display_name' => $this->_('column_status'),
			'filter' => true,
			'build_data' => $this->_('data_statuses'),
			'sortable' => true,
		);
		
		//Get Sorted / Filtered Data
		$sort = $this->sort->getQueryDefaults('alias', 'ASC');
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();
		
		$url_alias_total = $this->Model_Setting_UrlAlias->getTotalUrlAliases($filter);
		$url_aliases = $this->Model_Setting_UrlAlias->getUrlAliases($sort + $filter);
		
		$url_query = $this->url->getQueryExclude('url_alias_id');
		
		foreach ($url_aliases as &$url_alias) {
			$url_alias['actions'] = array(
				'edit' => array(
					'text' => $this->_('text_edit'),
					'href' => $this->url->link('setting/url_alias/update', 'url_alias_id=' . $url_alias['url_alias_id'])
				),
				'delete' => array(
					'text' => $this->_('text_delete'),
					'href' => $this->url->link('setting/url_alias/delete', 'url_alias_id=' . $url_alias['url_alias_id'] . '&' . $url_query)
				)
			);
		} unset($url_alias);
		
		//Build The Table
		$tt_data = array(
			'row_id'		=> 'url_alias_id',
		);
		
		$this->table->init();
		$this->table->setTemplate('table/list_view');
		$this->table->setColumns($columns);
		$this->table->setRows($url_aliases);
		$this->table->setTemplateData($tt_data);
		$this->table->mapAttribute('filter_value', $filter);
		
		$this->data['list_view'] = $this->table->render();
		
		//Batch Actions
		$this->data['batch_actions'] = array(
			'enable'	=> array(
				'label' => $this->_('text_enable')
			),
			'disable'=>	array(
				'label' => $this->_('text_disable'),
			),
			'delete' => array(
				'label' => $this->_('text_delete'),
			),
		);
		
		$this->data['batch_update'] = 'setting/url_alias/batch_update';
		
		//Render Limit Menu
		$this->data['limits'] = $this->sort->render_limit();
		
		//Pagination
		$this->pagination->init();
		$this->pagination->total = $url_alias_total;
		
		$this->data['pagination'] = $this->pagination->render();
		
		//Action Buttons
		$this->data['insert'] = $this->url->link('setting/url_alias/update');
		$this->data['delete'] = $this->url->link('setting/url_alias/delete');
		
		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		//Render
		$this->response->setOutput($this->render());
	}
	
	public function getForm()
	{
		//Page Head
		$this->document->setTitle($this->_('head_title'));
		
		//Template
		$this->template->load('setting/url_alias_form');
		
		//Insert or Update
		$url_alias_id = isset($_GET['url_alias_id']) ? (int)$_GET['url_alias_id'] : 0;
		
		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('setting/url_alias'));
		
		if (!$url_alias_id) {
			$this->breadcrumb->add($this->_('text_insert'), $this->url->link('setting/url_alias/udpate'));
		} else {
			$this->breadcrumb->add($this->_('text_edit'), $this->url->link('setting/url_alias/update', 'url_alias_id=' . $url_alias_id));
		}
		
		//Load Information
		if ($url_alias_id && !$this->request->isPost()) {
			$url_alias_info = $this->Model_Setting_UrlAlias->getUrlAlias($url_alias_id);
		}
		
		//Load Values or Defaults
		$defaults = array(
			'alias'	=>'',
			'path'	=>'',
			'query'	=>'',
			'store_id'	=> 0,
			'redirect'  => '',
			'status'	=> 1,
		);
			
		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($url_alias_info[$key])) {
				$this->data[$key] = $url_alias_info[$key];
			} else {
				$this->data[$key] = $default;
			}
		}
		
		//Additional Data
		$this->data['data_stores'] = array_merge($this->_('data_non_stores'), $this->Model_Setting_Store->getStores());
		
		//Action Buttons
		$this->data['save'] = $this->url->link('setting/url_alias/update', 'url_alias_id=' . $url_alias_id);
		$this->data['cancel'] = $this->url->link('setting/url_alias');
		
		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		//Render
		$this->response->setOutput($this->render());
	}

	private function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'setting/url_alias')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (empty($_POST['alias'])) {
			$this->error['alias'];
		}
		
		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->hasPermission('modify', 'setting/url_alias')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		return $this->error ? false : true;
	}
}