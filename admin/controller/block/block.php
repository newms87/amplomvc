<?php

class Admin_Controller_Block_Block extends Controller
{
	protected $path;

	public function __construct($registry)
	{
		parent::__construct($registry);

		if ($this->route->getSegment(1) !== 'block') {
			$this->path = $this->route->getSegment(1) . '/' . $this->route->getSegment(2);

			//Technically the router should never allow this to happen.
			if (!$this->block->exists($this->path)) {
				$this->message->add('warning', _l("Attempted to access unknown block!"));

				$this->url->redirect('block/block');
			}
		}
	}

	public function index()
	{
		if ($this->path) {
			return $this->form();
		}

		//Page Head
		$this->document->setTitle(_l("Blocks"));

		//Page Title
		$data = array(
			'page_title' => _l("Blocks"),
		);

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
		$block_total = $this->block->getTotalBlocks($filter);
		$blocks      = $this->block->getBlocks($sort + $filter);

		foreach ($blocks as &$block) {
			$actions = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => $this->url->link('block/' . $block['path'])
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => $this->url->link('block/' . $block['path'] .'/delete'),
				),
			);

			$block['actions'] = $actions;

			$block['name'] = $this->block->getName($block['path']);
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

		$data['list_view'] = $this->table->render();

		//Action Buttons
		$data['insert'] = $this->url->link('block/add');

		//Render limit Menu
		$data['limits'] = $this->sort->renderLimits();

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $block_total;

		$data['pagination'] = $this->pagination->render();

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//Render
		$this->response->setOutput($this->render('block/list', $data));
	}

	public function delete()
	{
		if (!$this->user->can('modify', 'block/block')) {
			$this->message->add('warning', _l("You do not have permission to modify Blocks"));
			$this->url->redirect('block/block');
		}

		if (!$this->block->remove($this->path)) {
			$this->message->add('error', $this->block->getError());
		} else {
			$this->message->add('success', _l("The Block %s was removed successfully!", $this->path));
		}

		if (!$this->request->isAjax()) {
			$this->url->redirect('block/block', $this->url->getQuery());
		}

		$this->response->setOutput($this->message->toJSON());
	}

	public function save()
	{
		if (!$this->user->can('modify', 'block/block')) {
			$this->message->add('warning', _l("You do not have permission to modify Blocks"));
			$this->url->redirect('block/block');
		}

		if (!$this->block->edit($this->path, $_POST)) {
			$this->message->add('error', $this->block->getError());
		} else {
			$this->message->add('success', _l("The Block %s was saved successfully!", $this->path));
		}

		if (!$this->request->isAjax()) {
			$this->url->redirect('block/block');
		}

		$this->response->setOutput($this->message->toJSON());
	}

	private function form()
	{
		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Blocks"), $this->url->link('block/block'));
		$this->breadcrumb->add($this->path, $this->url->link('block/block/' . $this->path));

		//Entry Data
		$block = array();

		if ($this->request->isPost()) {
			$block = $_POST;
		} elseif ($this->path) {
			$block = $this->block->get($this->path);
		}

		$default_instance = array(
			'name'             => _l("default"),
			'title'            => _l("Default"),
			'show_title' => 1,
		);

		$default_profile = array(
			'name'        => _l("New Profile"),
			'instance_id' => 0,
			'store_ids'   => array($this->config->get('config_default_store')),
			'layout_ids'  => array(),
			'position'    => '',
			'status'      => 1,
		);

		$defaults = array(
			'settings'  => array(),
			'instances' => array(),
			'profiles'  => array(),
			'status'    => 1,
		);

		$data = $block + $defaults;

		//Load Defaults for Settings, Profile Settings and Profiles
		if (empty($data['instances'])) {
			$data['instances'][0] = $default_instance;
		}

		//AC Templates
		$data['instances']['__ac_template__']         = $default_instance;
		$data['instances']['__ac_template__']['name'] = 'Profile __ac_template__';

		$data['profiles']['__ac_template__']         = $default_profile;
		$data['profiles']['__ac_template__']['name'] = 'Profile __ac_template__';

		foreach ($data['profiles'] as &$profile) {
			if (empty($profile['instance_id']) || !in_array($profile['instance_id'], array_keys($data['instances']))) {
				$profile['instance_id'] = key(current($data['instances']));
			}
		}
		unset($profile);

		$data['data_instances'] = array_diff_key($data['instances'], array('__ac_template__' => false));

		$sort_store = array(
			'sort'  => 'name',
			'order' => 'ASC',
		);

		$data['data_stores'] = $this->Model_Setting_Store->getStores($sort_store);

		$sort_layout = array(
			'sort'  => 'name',
			'order' => 'ASC',
		);

		$data['data_layouts'] = $this->Model_Design_Layout->getLayouts($sort_layout);

		$data['data_positions'] = array('' => _l(" --- None --- ")) + $this->theme->getSetting('data_positions');

		$data['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		$data['data_yes_no'] = array(
			1 => _l("Yes"),
			0 => _l("No"),
		);


		//Extended Data
		$data['extend_settings']  = $this->settings($data);
		$data['extend_profiles']  = $this->profiles($data);
		$data['extend_instances'] = $this->instances($data['instances']);

		//Action Buttons
		$data['save']   = $this->url->link('block/block/' . $this->path . '/save');
		$data['cancel'] = $this->url->link('block/block');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//Render
		$this->response->setOutput($this->render('block/block', $data));
	}

	protected function settings(&$data)
	{
		//override this method to add custom settings
		return '';
	}

	protected function profiles(&$data)
	{
		//Override this method to add custom profiles
		return '';
	}

	protected function instances(&$data)
	{
		//Override this method to add custom instances
		return '';
	}
}
