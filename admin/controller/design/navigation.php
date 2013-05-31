<?php
class Admin_Controller_Design_Navigation extends Controller 
{
	
	public function index()
	{
		$this->load->language('design/navigation');

		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
	}

	public function insert()
	{
		$this->load->language('design/navigation');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->Model_Design_Navigation->addNavigationGroup($_POST);
			
			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('design/navigation'));
		}
		else {
			$this->getForm();
		}
	}

	public function update()
	{
		$this->load->language('design/navigation');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->Model_Design_Navigation->editNavigationGroup($_GET['navigation_group_id'], $_POST);

			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('design/navigation'));
		}
		else {
			$this->getForm();
		}
	}
 
	public function delete()
	{
		$this->load->language('design/navigation');
 
		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $navigation_group_id) {
				$this->Model_Design_Navigation->deleteNavigationGroup($navigation_group_id);
			}
			
			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('design/navigation'));
		}

		$this->getList();
	}
	
	public function reset_admin_navigation()
	{
		$this->Model_Design_Navigation->reset_admin_navigation_group();
		
		$this->message->add("notify", "Admin Navigation Group has been reset!");
		
		$this->url->redirect($this->url->link("design/navigation"));
	}
	
	public function batch_update()
	{
		$this->load->language('design/navigation');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && isset($_GET['action'])) {
			foreach ($_POST['selected'] as $navigation_group_id) {
				switch($_GET['action']){
					case 'enable':
						$this->Model_Design_Navigation->editNavigationGroup($navigation_group_id, array('status' => 1));
						break;
					case 'disable':
						$this->Model_Design_Navigation->editNavigationGroup($navigation_group_id, array('status' => 0));
						break;
					case 'delete':
						$this->Model_Design_Navigation->deleteNavigationGroup($navigation_group_id);
						break;
				}
				if($this->error)
					break;
			}
			
			if (!$this->error) {
				if (!$this->message->error_set()) {
					$this->message->add('success',$this->_('text_success'));
					
					$this->url->redirect($this->url->link('design/navigation'));
				}
			}
		}

		$this->getList();
	}

	private function getList()
	{
		$this->template->load('design/navigation_list');

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('design/navigation'));
		
		//The Table Columns
		$columns = array();
		
		$columns['name'] = array(
			'type' => 'text',
			'display_name' => $this->_('column_name'),
			'filter' => true,
			'sortable' => true,
			'sort_value' => 'name',
		);
		
		$stores = array('admin' => array('store_id' => 'admin', 'name' => 'Admin Panel')) + $this->Model_Setting_Store->getStores();
		
		$columns['stores'] = array(
			'type' => 'multiselect',
			'display_name' => $this->_('column_stores'),
			'filter' => true,
			'build_config' => array('store_id' => 'name'),
			'build_data' => $stores,
			'sortable' => false,
		);
		
		$columns['status'] = array(
			'type' => 'select',
			'display_name' => $this->_('column_status'),
			'filter' => true,
			'build_data' => $this->_('data_statuses'),
			'sortable' => true,
			'sort_value' => 'status',
		);

  		//The Sort data
		$data = array();
		
		$sort_defaults = array(
			'sort' => 'name',
			'order' => 'ASC',
			'limit' => $this->config->get('config_admin_limit'),
			'page' => 1,
		);
		
		foreach ($sort_defaults as $key => $default) {
			$data[$key] = $$key = isset($_GET[$key]) ? $_GET[$key] : $default;
		}
		
		$data['start'] = ($page - 1) * $limit;
		
		//Filter
		$filter_values = !empty($_GET['filter']) ? $_GET['filter'] : array();
		
		if ($filter_values) {
			$data += $filter_values;
		}
		
		
		$navigation_groups_total = $this->Model_Design_Navigation->getTotalNavigationGroups($data);
		$navigation_groups = $this->Model_Design_Navigation->getNavigationGroups($data);
		
		foreach ($navigation_groups as &$nav_group) {
			$action = array();
			
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('design/navigation/update', 'navigation_group_id=' . $nav_group['navigation_group_id']),
			);
			
			if ($nav_group['name'] == 'admin') {
				$action[] = array(
					'text' => $this->_('button_admin_nav_reset'),
					'href' => $this->url->link('design/navigation/reset_admin_navigation'),
					'#class ' => 'reset',
				);
			}
			
			$nav_group['actions'] = $action;
		}
		
		//The table template data
		$tt_data = array(
			'row_id'		=> 'navigation_group_id',
			'route'		=> 'design/navigation',
			'sort'		=> $sort,
			'order'		=> $order,
			'page'		=> $page,
			'sort_url'	=> $this->url->link('design/navigation', $this->url->get_query('filter')),
			'columns'	=> $columns,
			'data'		=> $navigation_groups,
		);
		
		$tt_data += $this->language->data;
		
		//Build the table template
		$this->mytable->init();
		$this->mytable->set_template('table/list_view');
		$this->mytable->set_template_data($tt_data);
		$this->mytable->map_attribute('filter_value', $filter_values);
		
		$this->data['list_view'] = $this->mytable->build();
		
		//Batch Actions
		$this->data['batch_actions'] = array(
			'enable' => array(
				'label' => "Enable",
			),
			
			'disable' => array(
				'label' => "Disable",
			),
			
			'delete' => array(
				'label' => "Delete",
			),
		);
		
		$url = $this->url->get_query('filter', 'sort', 'order', 'page');
		
		$this->data['batch_update'] = $this->url->link('design/navigation/batch_update', $url);
		
		//Action Buttons
		$this->data['insert'] = $this->url->link('design/navigation/insert', $url);
		
		$url = $this->url->get_query('filter', 'sort', 'order');
		
		$this->pagination->init();
		$this->pagination->total = $navigation_groups_total;
		$this->data['pagination'] = $this->pagination->render();
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}

	private function getForm()
	{
		$this->language->load('design/navigation');
		
		$this->template->load('design/navigation_form');

		$navigation_group_id = isset($_GET['navigation_group_id']) ? $_GET['navigation_group_id'] : null;
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('design/navigation'));
		
		if (!$navigation_group_id) {
			$this->data['action'] = $this->url->link('design/navigation/insert');
		} else {
			$this->data['action'] = $this->url->link('design/navigation/update', 'navigation_group_id=' . $navigation_group_id);
		}
		
		$this->data['cancel'] = $this->url->link('design/navigation');

		if ($navigation_group_id && ($_SERVER['REQUEST_METHOD'] != 'POST')) {
			$navigation_group_info = $this->Model_Design_Navigation->getNavigationGroup($navigation_group_id);
		}
		
		//initialize the values in order of Post, Database, Default
		$defaults = array(
			'name' => '',
			'links' => array(),
			'stores' => array(0),
			'status' => 1,
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($navigation_group_info[$key])) {
				$this->data[$key] = $navigation_group_info[$key];
			} elseif (!$navigation_group_id) {
				$this->data[$key] = $default;
			}
		}
		
		$admin_store = array('admin' => array('store_id' => 0, 'name' => $this->_('text_admin_panel')));
		
		$this->data['data_stores'] = $admin_store + $this->Model_Setting_Store->getStores();
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}

	private function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'design/navigation')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		$navigation_group_id = isset($_GET['navigation_group_id']) ? (int)$_GET['navigation_group_id'] : 0;
		
		if (!isset($_POST['stores'])) {
			$_POST['stores'] = array('');
		}
		
		if (!$this->validation->text($_POST['name'], 3, 64)) {
			$this->error['name'] = $this->_('error_name');
		}
		
		//unset the fake link
		unset($_POST['links']['%link_num%']);
		
		foreach ($_POST['links'] as $key => $link) {
			if (!$this->validation->text($link['display_name'], 1, 255)) {
				$link_name = !empty($link['name']) ? $link['name'] : ( !empty($link['display_name']) ? $link['display_name'] : $key );
				$this->error["links[$key][display_name]"] = $this->language->format('error_display_name', $link_name);
			}
		
			//If name already exists in database, append _n to the name
			if (empty($link['name'])) {
				$name = $this->tool->get_slug($link['display_name']);
			}
			else {
				$name = $this->db->escape($this->tool->get_slug($link['name']));
			}
			
			$count = 0;
			do{
				$check_name = $count ? $name . '_' . $count : $name;
				
				$result = $this->db->query("SELECT COUNT(*) as total FROM " . DB_PREFIX . "navigation_group WHERE name = '$check_name' AND navigation_group_id != $navigation_group_id");
				
				$count++;
			}while($result->row['total']);
			
			$_POST['links'][$key]['name'] = $check_name;
		}
		
		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->hasPermission('modify', 'design/navigation')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		return $this->error ? false : true;
	}
}
