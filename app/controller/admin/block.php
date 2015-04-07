<?php

class App_Controller_Admin_Block extends Controller
{
	public function index()
	{
		//Page Head
		set_page_info('title', _l("Blocks"));

		//Page Title
		$data = array(
			'page_title' => _l("Blocks"),
		);

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Blocks"), site_url('admin/block'));

		//The Listing
		$data['listing'] = $this->listing();

		//Actions
		$data['insert'] = site_url('admin/block/add-block');

		//Render
		output($this->render('block/list', $data));
	}

	public function listing()
	{
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
		$sort   = $this->sort->getQueryDefaults('name', 'ASC');
		$filter = _get('filter', array());

		//Table Row Data
		$block_total = $this->block->getTotalBlocks($filter);
		$blocks      = $this->block->getBlocks($sort + $filter);

		foreach ($blocks as &$block) {
			$actions = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => site_url('admin/block/form', 'path=' . $block['path'])
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => site_url('admin/block/delete', 'path=' . $block['path']),
				),
			);

			$block['actions'] = $actions;

			$block['name'] = $this->block->getName($block['path']);
		}
		unset($block);

		$listing = array(
			'row_id'         => 'path',
			'columns'        => $columns,
			'rows'           => $blocks,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total_listings' => $block_total,
			'listing_path'   => 'admin/block/listing',
		);

		$output = block('widget/listing', null, $listing);

		if ($this->is_ajax) {
			output($output);
		}

		return $output;
	}

	public function delete()
	{
		$path = _get('path', '');

		$action     = new Action('block/' . $path);
		$controller = $action->getController();

		$controller->delete();
	}

	public function save()
	{
		$path = _get('path', '');

		$action     = new Action('block/' . $path);
		$controller = $action->getController();

		$controller->save();
	}

	public function form()
	{
		$path = _get('path', '');

		//Page Head
		set_page_info('title', _l("Edit Block"));
		$this->document->addStyle(theme_dir('block/' . $path . '/style.less'));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Blocks"), site_url('admin/block'));
		breadcrumb($path, site_url('admin/block/form', 'path=' . $path));

		//Entry Data
		$block = $_POST;

		if (!IS_POST && $path) {
			$block = $this->block->get($path);

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
		$action     = new Action('block/' . $path . '/settings');
		$controller = $action->getController();

		$block['block_settings']  = $controller->settings($block);
		$block['block_instances'] = $controller->instances($block['instances'], $block);

		//Action Buttons
		$block['save'] = site_url('admin/block/save', 'path=' . $path);

		//Render
		output($this->render('block/block', $block));
	}

	public function add()
	{
		echo "NOT IMPLEMENTED";
		exit;
		if (!$this->block->add($_POST)) {
			message('error', $this->block->fetchError());
			redirect('admin/block/add');
		}

		message('success', _l("The Block %s was created successfully!", $_POST['name']));
		redirect('admin/block', 'path=' . $_POST['path']);
	}

	public function add_block()
	{
		echo "NOT YET IMPLEMENTED!";
		exit;

		//Notify User this is oly for developers
		message('notify', _l("Adding a Block will simply setup the files in the system on the front end and back end. If you are not a developer this is worthless!"));

		//Page Title
		set_page_info('title', _l("New Block"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Blocks"), site_url('admin/block'));
		breadcrumb(_l("New Block"), site_url('admin/block/add'));

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
		$data['save']   = site_url('admin/block/add');
		$data['cancel'] = site_url('admin/block');

		//Render
		output($this->render('block/add', $data));
	}
}
