<?php

class App_Controller_Admin_Block_Block extends Controller
{
	protected $path;

	public function __construct()
	{
		parent::__construct();

		if ($this->route->getSegment(1) !== 'block') {
			$this->path = $this->route->getSegment(1) . '/' . $this->route->getSegment(2);
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
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Blocks"), site_url('block/block'));

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
					'href' => site_url('block/' . $block['path'])
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => site_url('block/' . $block['path'] . '/delete'),
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
		$data['insert'] = site_url('block/block/add');

		//Render limit Menu
		$data['limits'] = $this->sort->renderLimits();

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $block_total;

		$data['pagination'] = $this->pagination->render();

		//Render
		$this->response->setOutput($this->render('block/list', $data));
	}

	public function add()
	{
		if (!$this->user->can('modify', 'block/add')) {
			$this->error['warning'] = _l("You do not have permission to Add Blocks");
			redirect('block/block');
		}

		//Notify User this is oly for developers
		$this->message->add('notify', _l("Adding a Block will simply setup the files in the system on the front end and back end. If you are not a developer this is worthless!"));

		//Page Title
		$this->document->setTitle(_l("New Block"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Blocks"), site_url('block/block'));
		$this->breadcrumb->add(_l("New Block"), site_url('block/add'));

		$defaults = array(
			'name'                => '',
			'path'                => '',
			'language_file'       => true,
			'settings_file'       => true,
			'profiles_file'       => true,
			'themes'              => array('default'),
			'front_language_file' => true,
		);

		$data = $_POST + $defaults;

		$data['data_themes'] = $this->theme->getThemes();

		//Actions
		$data['save']   = site_url('block/add_block');
		$data['cancel'] = site_url('block/block');

		//Render
		$this->response->setOutput($this->render('block/add', $data));
	}

	public function add_block()
	{
		if (!$this->user->can('modify', 'block/add')) {
			$this->error['warning'] = _l("You do not have permission to Add Blocks");
			redirect('block/block');
		}

		if (!$this->block->add($_POST)) {
			$this->message->add('error', $this->block->getError());
			redirect('block/block/add');
		}

		$this->message->add('success', _l("The Block %s was created successfully!", $_POST['name']));
		redirect('block/block', 'name=' . $_POST['path']);
	}

	public function delete()
	{
		if (!$this->user->can('modify', 'block/block')) {
			$this->message->add('warning', _l("You do not have permission to modify Blocks"));
			redirect('block/block');
		}

		if (!$this->block->remove($this->path)) {
			$this->message->add('error', $this->block->getError());
		} else {
			$this->message->add('success', _l("The Block %s was removed successfully!", $this->path));
		}

		if (!$this->request->isAjax()) {
			redirect('block/block', $this->url->getQuery());
		}

		$this->response->setOutput($this->message->toJSON());
	}

	public function save()
	{
		if (!$this->user->can('modify', 'block/block')) {
			$this->message->add('warning', _l("You do not have permission to modify Blocks"));
			redirect('block/block');
		}

		if (!$this->block->edit($this->path, $_POST)) {
			$this->message->add('error', $this->block->getError());
		} else {
			$this->message->add('success', _l("The Block %s was saved successfully!", $this->path));
		}

		if (!$this->request->isAjax()) {
			redirect('block/block');
		}

		$this->response->setOutput($this->message->toJSON());
	}

	private function form()
	{
		//Page Head
		$this->document->setTitle(_l("Edit Block"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Blocks"), site_url('block/block'));
		$this->breadcrumb->add($this->path, site_url('block/block/' . $this->path));

		//Entry Data
		$block = array();

		if ($this->request->isPost()) {
			$block = $_POST;
		} elseif ($this->path) {
			$block = $this->block->get($this->path);

			//Cast associative array to indexed array for __ac_template__
			$block['instances'] = array_values($block['instances']);
		}

		$defaults = array(
			'settings'  => array(),
			'instances' => array(),
			'status'    => 1,
		);

		$block += $defaults;

		//Extended Data
		$block['block_settings']  = $this->settings($block);
		$block['block_instances'] = $this->instances($block['instances'], $block);

		//Action Buttons
		$block['save']   = site_url('block/' . $this->path . '/save');
		$block['cancel'] = site_url('block/block');

		//Render
		$this->response->setOutput($this->render('block/block', $block));
	}

	//override this method to add custom settings
	protected function settings(&$data)
	{
		$data['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		return $this->render('block/block/settings', $data, true);
	}

	//Override this method to add custom instances
	protected function instances(&$instances)
	{
		$default_instance = array(
			'name'       => _l("default"),
			'title'      => _l("Default"),
			'show_title' => 1,
			'settings'   => array(),
		);

		//AC Templates
		$instances['__ac_template__']         = $default_instance;
		$instances['__ac_template__']['name'] = 'instance-__ac_template__';

		$count = 1;
		foreach ($instances as $row => &$instance) {
			$instance['template'] = $this->instance($row, $instance, $count++ === count($instances));
		}
		unset($instance);

		$instance_data = array(
			'instances' => $instances,
		);

		return $this->render('block/block/instances', $instance_data, true);
	}

	//Override this method to add custom instance settings
	protected function instance($row, $instance)
	{
		$data = array(
			'row'      => $row,
			'instance' => $instance,
		);

		$data['data_yes_no'] = array(
			0 => _l("No"),
			1 => _l("Yes"),
		);

		return $this->render('block/block/instance', $data, true);
	}
}
