<?php

class App_Controller_Admin_Block extends Controller
{
	public function index()
	{
		//Page Head
		set_page_info('title', _l("Blocks"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Blocks"), site_url('admin/block'));

		//Render
		output($this->render('block/list'));
	}

	public function listing()
	{
		$sort    = (array)_request('sort', array('name' => 'ASC'));
		$filter  = (array)_request('filter');
		$options = array(
			'index'   => 'path',
			'columns' => $this->block->getColumns(),
			'page'    => _get('page'),
			'limit'   => _get('limit', option('admin_list_limit', 20)),
		);

		//Table Row Data
		list($blocks, $total) = $this->block->getBlocks($sort, $filter, $options, true);

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
			'records'        => $blocks,
			'sort'           => $sort,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total_listings' => $total,
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

		if ($path) {
			$action = new Action('block/' . $path);
			$action->getController()->delete();
		}
	}

	public function save()
	{
		$path = _get('path', '');

		if ($path) {
			$action = new Action('block/' . $path);
			$action->getController()->save();
		}
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
