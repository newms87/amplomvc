<?php

class App_Controller_Admin_Page extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("Page"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('admin/common/home'));
		$this->breadcrumb->add(_l("Page"), site_url('admin/page'));

		//Batch Actions
		$actions = array(
			'enable'  => array(
				'label' => _l("Enable")
			),
			'disable' => array(
				'label' => _l("Disable"),
			),
			'delete'  => array(
				'label' => _l("Delete"),
			),
		);

		$data['batch_action'] = array(
			'actions' => $actions,
			'path'    => site_url('admin/page/batch_action'),
		);

		//Action Buttons
		$data['insert'] = site_url('admin/page/update');

		//Render
		$this->response->setOutput($this->render('page/list', $data));
	}

	public function listing()
	{
		//The Table Columns
		$columns = array();

		$columns['title'] = array(
			'type'         => 'text',
			'display_name' => _l("Page Title"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['stores'] = array(
			'type'         => 'multiselect',
			'display_name' => _l("Stores"),
			'filter'       => true,
			'build_config' => array(
				'store_id',
				'name'
			),
			'build_data'   => $this->Model_Setting_Store->getStores(),
			'sortable'     => false,
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

		//Get Sorted / Filtered Data
		$sort   = $this->sort->getQueryDefaults('title', 'ASC');
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		$page_total = $this->Model_Page->getTotalPages($filter);
		$pages      = $this->Model_Page->getPages($sort + $filter);

		$url_query = $this->url->getQueryExclude('page_id');

		foreach ($pages as &$page) {
			$page['actions'] = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => site_url('admin/page/form', 'page_id=' . $page['page_id'])
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => site_url('admin/page/delete', 'page_id=' . $page['page_id'] . '&' . $url_query)
				)
			);

			$page['stores'] = $this->Model_Page->getPageStores($page['page_id']);
		}
		unset($page);

		$listing = array(
			'row_id'         => 'page_id',
			'columns'        => $columns,
			'rows'           => $pages,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total_listings' => $page_total,
			'listing_path'   => 'admin/page/listing',
		);

		$output = block('widget/listing', null, $listing);

		if ($this->request->isAjax()) {
			$this->response->setOutput($output);
		}

		return $output;
	}

	public function form()
	{
		//Page Head
		$this->document->setTitle(_l("Page"));

		//Insert or Update
		$page_id = isset($_GET['page_id']) ? $_GET['page_id'] : null;

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('admin/common/home'));
		$this->breadcrumb->add(_l("Page"), site_url('admin/page'));
		$this->breadcrumb->add($page_id ? _l("Edit") : _l("Add"), site_url('admin/page/form', 'page_id=' . $page_id));

		//Load Information from POST or DB
		$page = $_POST;

		if ($page_id && !$this->request->isPost()) {
			$page = $this->Model_Page->getPage($page_id);

			$page['stores'] = $this->Model_Page->getPageStores($page_id);
		}

		//Set Values or Defaults
		$defaults = array(
			'title'            => 'New Page',
			'alias'            => '',
			'content'          => '',
			'css'              => '',
			'meta_keywords'    => '',
			'meta_description' => '',
			'display_title'    => 1,
			'layout_id'        => 0,
			'stores'           => array(0),
			'blocks'           => array(),
			'status'           => 1,
			'translations'     => array(),
		);

		$page += $defaults;

		//Template Data
		$page['data_stores']  = $this->Model_Setting_Store->getStores();
		$page['data_layouts'] = $this->Model_Design_Layout->getLayouts();

		$page['url_blocks']        = site_url('admin/block');
		$page['url_create_layout'] = site_url('admin/page/create_layout');
		$page['url_load_blocks']   = site_url('admin/page/loadBlocks');

		$store_front          = current($page['stores']);
		$page['page_preview'] = $this->url->store($store_front['store_id'], 'page/preview', 'page_id=' . $page_id);

		$page['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		//Action Buttons
		$page['save']   = site_url('admin/page/update', 'page_id=' . $page_id);

		//Render
		$this->response->setOutput($this->render('page/form', $page));
	}

	public function update()
	{
		//Insert
		if (empty($_GET['page_id'])) {
			$this->Model_Page->addPage($_POST);
		} //Update
		else {
			$this->Model_Page->editPage($_GET['page_id'], $_POST);
		}

		if ($this->Model_Page->hasError()) {
			$this->message->add('error', $this->Model_Page->getError());
		} else {
			$this->message->add('success', _l("The Page has been updated successfully!"));
		}

		if ($this->request->isAjax()) {
			$this->response->setOutput($this->message->toJSON());
		} elseif ($this->message->has('error')) {
			$this->form();
		} else {
			redirect('admin/page');
		}
	}

	public function delete()
	{
		$this->Model_Page->deletePage($_GET['page_id']);

		if ($this->Model_Page->hasError()) {
			$this->message->add('error', $this->Model_Page->getError());
		} else {
			$this->message->add('notify', _l("Page was deleted!"));
		}

		if ($this->request->isAjax()) {
			$this->response->setOutput($this->message->toJSON());
		} else {
			redirect('admin/page');
		}
	}

	public function batch_action()
	{
		foreach ($_POST['batch'] as $page_id) {
			switch ($_POST['action']) {
				case 'enable':
					$this->Model_Page->update_field($page_id, array('status' => 1));
					break;

				case 'disable':
					$this->Model_Page->update_field($page_id, array('status' => 0));
					break;

				case 'delete':
					$this->Model_Page->deletePage($page_id);
					break;

				case 'copy':
					$this->Model_Page->copyPage($page_id);
					break;
			}
		}

		if ($this->Model_Page->hasError()) {
			$this->message->add('error', $this->Model_Page->getError());
		} else {
			$this->message->add('success', _l("Success: You have modified navigation!"));
		}

		if ($this->request->isAjax()) {
			$this->listing();
		} else {
			redirect('admin/design/navigation');
		}
	}

	public function create_layout()
	{
		if (!empty($_POST['name'])) {
			$layout = array(
				'name' => $_POST['name'],
			);

			$result = $this->Model_Design_Layout->getLayouts($layout);

			if (empty($result)) {
				$layout_id = $this->Model_Design_Layout->addLayout($layout);
			} else {
				$result    = current($result);
				$layout_id = $result['layout_id'];
			}
		}

		$sort = array(
			'sort'  => 'name',
			'order' => "ASC",
		);

		$layouts = $this->Model_Design_Layout->getLayouts($sort);

		$this->builder->setConfig('layout_id', 'name');

		$this->response->setOutput($this->builder->build('select', $layouts, 'layout_id', $layout_id));
	}

	public function loadBlocks()
	{
		$blocks = array();

		if (!empty($_POST['layout_id']) && !empty($_POST['stores'])) {
			$filter = array(
				'layouts' => array($_POST['layout_id']),
				'stores'  => $_POST['stores'],
				'status'  => 1,
			);

			$block_list = $this->block->getBlocks($filter);

			$blocks = array();

			$data_positions = $this->theme->getPositions();

			foreach ($block_list as $block) {
				foreach ($block['profiles'] as $profile) {
					foreach ($profile['store_ids'] as $store_id) {
						$blocks[] = array(
							'path'       => $block['path'],
							'name'       => $block['name'],
							'position'   => $data_positions[$profile['position']],
							'store_id'   => $store_id,
							'store_name' => $this->Model_Setting_Store->getStoreName($store_id),
						);
					}
				}
			}
		}

		$this->response->setOutput(json_encode($blocks));
	}
}
