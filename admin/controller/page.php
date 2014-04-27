<?php

class Admin_Controller_Page extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("Page"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Page"), $this->url->link('page'));

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
			'path'    => 'page/batch_update',
		);

		//The Listing
		$data['listing'] = $this->listing();

		//Action Buttons
		$data['insert'] = $this->url->link('page/update');

		//Render
		$this->response->setOutput($this->render('page/list', $data));
	}

	public function update()
	{
		if ($this->request->isPost() && $this->validateForm()) {
			//Insert
			if (empty($_GET['page_id'])) {
				$page_id = $this->Model_Page_Page->addPage($_POST);

				if ($this->request->isAjax()) {
					$this->response->setOutput(json_encode(array('redirect' => $this->url->link('page', 'page_id=' . $page_id))));
					return;
				}
			} //Update
			else {
				$this->Model_Page_Page->editPage($_GET['page_id'], $_POST);
			}

			if (!$this->message->has('error', 'warning')) {
				$this->message->add('success', _l("Page was updated successfully!"));

				if (!$this->request->isAjax()) {
					$this->url->redirect('page');
				}
			}
		}

		if ($this->request->isAjax()) {
			$json = $this->error ? array('error' => $this->error) : $this->message->fetch();
			$this->response->setOutput(json_encode($json));
		} else {
			$this->getForm();
		}
	}

	public function delete()
	{
		if (!$this->user->can('modify', 'page')) {
			$this->message->add('warning', _l("You do not have permission to modify Pages!"));
			$this->url->redirect('common/home');
		}

		if (isset($_GET['page_id'])) {
			$this->Model_Page_Page->deletePage($_GET['page_id']);

			if (!$this->message->has('error', 'warning')) {
				$this->message->add('notify', _l("Page was deleted!"));
			}
		}

		$this->url->redirect('page');
	}

	public function batch_update()
	{
		if (!$this->user->can('modify', 'page')) {
			$this->message->add('warning', _l("You do not have permission to modify Pages!"));
		} else {

			if (!empty($_POST['batch']) && isset($_POST['action'])) {
				foreach ($_POST['batch'] as $page_id) {
					switch ($_POST['action']) {
						case 'enable':
							$this->Model_Page_Page->update_field($page_id, array('status' => 1));
							break;
						case 'disable':
							$this->Model_Page_Page->update_field($page_id, array('status' => 0));
							break;
						case 'delete':
							$this->Model_Page_Page->deletePage($page_id);
							break;
						case 'copy':
							$this->Model_Page_Page->copyPage($page_id);
							break;
						default:
							break 2; //Exit the For Loop
					}
				}

				if (!$this->message->has('error', 'warning')) {
					$this->message->add('success', _l("Success: You have modified Pages!"));
				}
			}
		}

		if (!$this->request->isAjax()) {
			$this->url->redirect('page');
		}

		if ($this->message->has('error', 'warning')) {
			echo $this->message->toJSON();
			exit;
		}

		$this->listing();
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

		$page_total = $this->Model_Page_Page->getTotalPages($filter);
		$pages      = $this->Model_Page_Page->getPages($sort + $filter);

		$url_query = $this->url->getQueryExclude('page_id');

		foreach ($pages as &$page) {
			$page['actions'] = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => $this->url->link('page/update', 'page_id=' . $page['page_id'])
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => $this->url->link('page/delete', 'page_id=' . $page['page_id'] . '&' . $url_query)
				)
			);

			$page['stores'] = $this->Model_Page_Page->getPageStores($page['page_id']);
		}
		unset($page);

		$listing = array(
			'row_id'         => 'page_id',
			'columns'        => $columns,
			'rows'           => $pages,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total_listings' => $page_total,
			'listing_path'   => 'page/listing',
		);

		$output = _block('widget/listing', null, $listing);

		if (!$this->request->isAjax()) {
			return $output;
		}

		$this->response->setOutput($output);
	}

	private function getForm()
	{
		//Page Head
		$this->document->setTitle(_l("Page"));

		//Insert or Update
		$page_id = isset($_GET['page_id']) ? $_GET['page_id'] : null;

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Page"), $this->url->link('page'));

		if ($page_id) {
			$this->breadcrumb->add(_l("Edit"), $this->url->link('page/update', 'page_id=' . $page_id));
		} else {
			$this->breadcrumb->add(_l("Add"), $this->url->link('page/update'));
		}

		//Load Information from POST or DB
		if ($this->request->isPost()) {
			$page_info = $_POST;
		} elseif ($page_id) {
			$page_info = $this->Model_Page_Page->getPage($page_id);

			$page_info['stores'] = $this->Model_Page_Page->getPageStores($page_id);
		} else {
			$page_info = array();
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

		$data += $page_info + $defaults;

		$_POST = $data;
		//$this->loadBlocks();

		//Template Data
		$data['data_stores']  = $this->Model_Setting_Store->getStores();
		$data['data_layouts'] = $this->Model_Design_Layout->getLayouts();

		$data['url_blocks']        = $this->url->link('block/block');
		$data['url_create_layout'] = $this->url->link('page/create_layout');
		$data['url_load_blocks']   = $this->url->link('page/loadBlocks');

		$store_front          = current($data['stores']);
		$data['page_preview'] = $this->url->store($store_front['store_id'], 'page/preview', 'page_id=' . $page_id);

		$data['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		//Action Buttons
		$data['save']   = $this->url->link('page/update', 'page_id=' . $page_id);
		$data['cancel'] = $this->url->link('page');

		//Render
		$this->response->setOutput($this->render('page/form', $data));
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

			$data_positions = $this->theme->getSetting('data_positions');

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

	private function validateForm()
	{
		if (!$this->user->can('modify', 'page')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify Pages!");
		}

		if (!$this->validation->text($_POST['title'], 3, 64)) {
			$this->error['title'] = _l("Page Title must be between 3 and 64 characters!");
		}

		if (empty($_POST['display_title'])) {
			$_POST['display_title'] = 0;
		}

		return empty($this->error);
	}

	private function validateDelete()
	{


		return empty($this->error);
	}
}
