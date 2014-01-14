<?php
class Admin_Controller_Block_Block extends Controller
{
	private $block_controller;

	public function index()
	{
		if (!empty($_GET['path'])) {
			if (!$this->Model_Block_Block->isBlock($_GET['path'])) {
				$this->message->add('warning', _l("Attempted to access unknown block!"));

				$this->url->redirect('block/block');
			}

			$this->getForm();
		} else {
			$this->getList();
		}
	}

	public function delete()
	{
		if (!empty($_GET['path']) && $this->validateDelete()) {
			$this->Model_Block_Block->deleteBlock($_GET['path']);

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("The Block was removed successfully!"));
			}

			$this->url->redirect('block/block', $this->url->getQueryExclude('path'));
		}

		$this->index();
	}

	private function getList()
	{
		//Page Head
		$this->document->setTitle(_l("Blocks"));

		//Page Title
		$this->data['page_title'] = _l("Blocks");

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Blocks"), $this->url->link('block/block'));

		//The Table Columns
		$columns = array();

		$columns['name'] = array(
			'type'         => 'text',
			'display_name' => _l("Name"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['path'] = array(
			'type'         => 'text',
			'display_name' => _l("Path"),
			'filter'       => true,
			'sortable'     => true,
		);


		$columns['status'] = array(
			'type'         => 'select',
			'display_name' => _l("Status"),
			'filter'       => true,
			'build_data'   => array(
				0 => _l("Disabled"),
				1 => _l("Enabled"),
			),
			'sortable'     => true,
		);

		//The Sort & Filter Data
		$sort   = $this->sort->getQueryDefaults('path', 'ASC');
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		//Table Row Data
		$block_total = $this->Model_Block_Block->getTotalBlocks($filter);
		$blocks      = $this->Model_Block_Block->getBlocks($sort + $filter);

		foreach ($blocks as &$block) {
			$actions = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => $this->url->link('block/block', 'path=' . $block['path'])
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => $this->url->link('block/block/delete', 'path=' . $block['path']),
				),
			);

			$block['actions'] = $actions;

			$block['name'] = $this->Model_Block_Block->getBlockName($block['path']);
		}

		//Build The Table
		$tt_data = array(
			'row_id' => 'path',
		);

		$this->table->init();
		$this->table->setTemplate('table/list_view');
		$this->table->setColumns($columns);
		$this->table->setRows($blocks);
		$this->table->setTemplateData($tt_data);
		$this->table->mapAttribute('filter_value', $filter);

		$this->data['list_view'] = $this->table->render();

		//Action Buttons
		$this->data['insert'] = $this->url->link('block/add');

		//Render limit Menu
		$this->data['limits'] = $this->sort->renderLimits();

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $block_total;

		$this->data['pagination'] = $this->pagination->render();

		//The Template
		$this->template->load('block/list');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	private function getForm()
	{
		$path = $_GET['path'];

		//Handle POST
		if ($this->request->isPost() && $this->validate()) {
			//If plugins have additional Data
			$this->saveBlockData();

			$this->Model_Block_Block->updateBlock($path, $_POST);

			$this->message->add('success', _l("The Block was saved successfully!"));

			$this->url->redirect('block/block');
		}

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Blocks"), $this->url->link('block/block'));
		$this->breadcrumb->add($path, $this->url->link('block/block', 'path=' . $path));

		//Entry Data
		$block = array();

		if ($this->request->isPost()) {
			$block = $_POST;
		} elseif ($path) {
			$block = $this->Model_Block_Block->getBlock($path);
		}

		$default_profile_settings = array(
			'name'             => _l("Default"),
			'show_block_title' => 1,
		);

		$default_profile = array(
			'name'               => _l("New Profile"),
			'profile_setting_id' => 0,
			'store_ids'          => array($this->config->get('config_default_store')),
			'layout_ids'         => array(),
			'position'           => '',
			'status'             => 1,
		);

		$defaults = array(
			'settings'         => array(),
			'profile_settings' => array(),
			'profiles'         => array(),
			'status'           => 1,
		);

		$this->data += $block + $defaults;

		//Load Defaults for Settings, Profile Settings and Profiles
		if (empty($this->data['profile_settings'])) {
			$this->data['profile_settings'][0] = $default_profile_settings;
		}

		//AC Templates
		$this->data['profile_settings']['__ac_template__']         = $default_profile_settings;
		$this->data['profile_settings']['__ac_template__']['name'] = 'Profile __ac_template__';

		$this->data['profiles']['__ac_template__']         = $default_profile;
		$this->data['profiles']['__ac_template__']['name'] = 'Profile __ac_template__';

		foreach ($this->data['profiles'] as &$profile) {
			if (empty($profile['profile_setting_id']) || !in_array($profile['profile_setting_id'], array_keys($this->data['profile_settings']))) {
				$profile['profile_setting_id'] = key(current($this->data['profile_settings']));
			}
		}
		unset($profile);

		$this->data['data_profile_settings'] = array_diff_key($this->data['profile_settings'], array('__ac_template__' => false));

		//Get additional Block settings and profile data (this is the plugin part)
		$this->loadBlockData();

		$sort_store = array(
			'sort'  => 'name',
			'order' => 'ASC',
		);

		$this->data['data_stores'] = $this->Model_Setting_Store->getStores($sort_store);

		$sort_layout = array(
			'sort'  => 'name',
			'order' => 'ASC',
		);

		$this->data['data_layouts'] = $this->Model_Design_Layout->getLayouts($sort_layout);

		$this->data['data_positions'] = array('' => _l(" --- None --- ")) + $this->theme->getSetting('data_positions');

		$this->data['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		$this->data['data_yes_no'] = array(
			1 => _l("Yes"),
			0 => _l("No"),
		);

		//Action Buttons
		$this->data['action'] = $this->url->link('block/block', 'path=' . $path);
		$this->data['cancel'] = $this->url->link('block/block');

		//The Template
		$this->template->load('block/block');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	private function loadBlockController()
	{
		if ($this->block_controller || empty($_GET['path'])) {
			return;
		}

		$action = new Action($this->registry, 'block/' . $_GET['path']);

		$this->block_controller = $action->getController();
	}

	private function loadBlockData()
	{
		$this->loadBlockController();

		if (method_exists($this->block_controller, 'settings')) {
			$this->block_controller->settings($this->data['settings']);
			$this->data['extend_settings'] = $this->block_controller->output;
		}

		if (method_exists($this->block_controller, 'profile_settings')) {
			$this->block_controller->profile_settings($this->data['profile_settings']);
			$this->data['extend_profile_settings'] = $this->block_controller->output;
		}

		if (method_exists($this->block_controller, 'profile')) {
			$this->block_controller->profile($this->data['profiles']);
			$this->data['extend_profile'] = $this->block_controller->output;
		}
	}

	private function saveBlockData()
	{
		$this->loadBlockController();

		if (method_exists($this->block_controller, 'saveSettings')) {
			$this->block_controller->saveSettings($_POST['settings']);
		}

		if (method_exists($this->block_controller, 'saveProfile')) {
			$this->block_controller->saveProfile($_POST['profiles']);
		}
	}

	private function validate()
	{
		if (!$this->user->can('modify', 'block/block')) {
			$this->error['warning'] = _l("You do not have permission to modify Blocks");
		}

		$this->validate_block_data();

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'block/block')) {
			$this->error['warning'] = _l("You do not have permission to modify Blocks");
		}

		return $this->error ? false : true;
	}

	private function validate_block_data()
	{
		$this->loadBlockController();

		if (method_exists($this->block_controller, 'validate')) {
			$this->error += $this->block_controller->validate();
		}
	}
}
