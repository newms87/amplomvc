<?php
class Admin_Controller_Extension_Total extends Controller
{
	private $extension_controller;

	public function index()
	{
		if (!empty($_GET['code'])) {
			if (!$this->System_Extension_Total->has($_GET['code'])) {
				$this->message->add('warning', _l("The extension %s does not exist!", $_GET['code']));

				$this->url->redirect('extension/total');
			}

			$this->getForm();
		} else {
			$this->getList();
		}
	}

	private function getList()
	{
		//Page Head
		$this->document->setTitle(_l("Order Totals"));

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Order Totals"), $this->url->link('extension/total'));

		//The Table Columns
		$columns = array();

		$columns['title'] = array(
			'type'         => 'text',
			'display_name' => _l("Label"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['code'] = array(
			'type'         => 'text',
			'display_name' => _l("Extension Code"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['sort_order'] = array(
			'type'         => 'int',
			'display_name' => _l("Sort Order"),
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
		$sort   = $this->sort->getQueryDefaults('sort_order', 'ASC');
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		//Table Row Data
		$extension_total = $this->System_Extension_Model->getTotal('total', $filter);
		$extensions      = $this->System_Extension_Model->getExtensions('total', $sort + $filter);

		foreach ($extensions as &$extension) {
			if ($extension['installed']) {
				$actions = array(
					'edit'      => array(
						'text' => _l("Edit"),
						'href' => $this->url->link('extension/total/edit', 'code=' . $extension['code'])
					),
					'settings'  => array(
						'text' => _l("Settings"),
						'href' => $this->url->link('extension/total', 'code=' . $extension['code'])
					),
					'uninstall' => array(
						'text' => _l("Uninstall"),
						'href' => $this->url->link('extension/total/uninstall', 'code=' . $extension['code']),
					),
				);
			} else {
				$actions = array(
					'install' => array(
						'text' => _l("Install"),
						'href' => $this->url->link('extension/total/install', 'code=' . $extension['code'])
					),
				);
			}

			$extension['actions'] = $actions;
		}
		unset($extension);

		//Build The Table

		$this->table->init();
		$this->table->setTemplate('table/list_view');
		$this->table->setColumns($columns);
		$this->table->setRows($extensions);
		$this->table->mapAttribute('filter_value', $filter);

		$data['list_view'] = $this->table->render();

		//Action Buttons
		$data['insert'] = $this->url->link('extension/add');

		//Render limit Menu
		$data['limits'] = $this->sort->renderLimits();

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $extension_total;

		$data['pagination'] = $this->pagination->render();

		//Render
		$this->response->setOutput($this->render('extension/total_list', $data));
	}

	private function getForm()
	{
		$code = $_GET['code'];

		$this->loadExtensionController($code);

		if ($this->request->isPost() && $this->validate()) {
			//If Extension needs to customize the way data is stored
			if (method_exists($this->extension_controller, 'saveSettings')) {
				$this->extension_controller->saveSettings($_POST['settings']);
			}

			$this->System_Extension_Model->updateExtension('total', $code, $_POST);

			$this->message->add('success', _l("Successfully saved settings for %s!", $code));

			$this->url->redirect('extension/total');
		}

		$total_extension = $this->System_Extension_Total->get($code);

		//Page Head
		$this->document->setTitle($total_extension->info('title'));

		//Page Title
		$data['page_title'] = $total_extension->info('title');

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Order Totals Extensions"), $this->url->link('extension/total'));
		$this->breadcrumb->add($total_extension->info('title'), $this->url->link('extension/total', 'code=' . $code));

		//Entry Data
		if ($this->request->isPost()) {
			$extension = $_POST;
		} else {
			$extension = $total_extension->info();
		}

		$defaults = array(
			'title'      => '',
			'settings'   => array(),
			'sort_order' => 0,
			'status'     => 1,
		);

		$data += $extension + $defaults;

		//Get additional extension settings and profile data (this is the plugin part)
		if (method_exists($this->extension_controller, 'settings')) {
			$this->extension_controller->settings($data['settings']);
			$data['extend_settings'] = $this->extension_controller->output;
		}

		$data['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		//Action Buttons
		$data['save']   = $this->url->link('extension/total', 'code=' . $code);
		$data['cancel'] = $this->url->link('extension/total');

		//Render
		$this->response->setOutput($this->render('extension/total', $data));
	}

	public function edit()
	{
		$code = !empty($_GET['code']) ? $_GET['code'] : '';

		//Verify File
		$file = DIR_SYSTEM . "extension/total/" . $code . '.php';

		if (!is_file($file)) {
			$this->message->add('warning', _l("The extension file %s does not exist!", $file));
			$this->url->redirect('extension/total');
		}

		//Handle POST
		if ($this->request->isPost()) {
			if (file_put_contents($file, html_entity_decode($_POST['contents']))) {
				$this->message->add('success', _l("Saved the extension file %s!", $file));
			} else {
				$this->message->add('warning', _l("There was a problem while saving the file %s!", $file));
			}

			$this->url->redirect('extension/total');
		}

		//Load extension
		$extension = $this->System_Extension_Total->get($code)->info();

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Order Totals Extensions"), $this->url->link('extension/total'));
		$this->breadcrumb->add($extension['title'], $this->url->link('extension/total/edit', 'code=' . $code));

		//Load Contents
		$data['contents'] = file_get_contents($file);

		//Template Data
		$data['page_title'] = $extension['title'];
		$data['edit_file']  = $file;

		//Action Buttons
		$data['save']   = $this->url->link('extension/total/edit', 'code=' . $code);
		$data['cancel'] = $this->url->link('extension/total');

		//Render
		$this->response->setOutput($this->render('extension/edit', $data));
	}

	private function loadExtensionController($code)
	{
		if (!$this->extension_controller && !empty($code)) {
			$action = new Action($this->registry, 'extension/total/' . $code);

			if ($action->isValid()) {
				$this->extension_controller = $action->getController();
			}
		}
	}

	private function validate()
	{
		if (!$this->user->can('modify', 'extension/total')) {
			$this->error['warning'] = _l("You do not have permission to modify the Totals system extension!");
		}

		if (method_exists($this->extension_controller, 'validate')) {
			if (!$this->extension_controller->validate()) {
				return false;
			}
		}

		return $this->error ? false : true;
	}

	public function install()
	{
		if ($this->System_Extension_Model->install('total', $_GET['code'])) {
			$this->loadExtensionController($_GET['code']);

			$settings = array();

			//Save Default Settings
			if (method_exists($this->extension_controller, 'settings')) {
				$this->extension_controller->settings($settings);
			}

			$this->System_Extension_Model->updateExtension('total', $_GET['code'], array('settings' => $settings));

			$this->message->add('success', _l("Successfully installed the %s extension for Order Totals", $_GET['code']));
		} elseif ($this->System_Extension_Model->hasError()) {
			$this->message->add('warning', $this->System_Extension_Model->getError());
		}

		$this->url->redirect('extension/total');
	}

	public function uninstall()
	{
		if ($this->System_Extension_Model->uninstall('total', $_GET['code'])) {
			$this->message->add('notify', _l("Uninstalled the %s extension for Order Totals.", $_GET['code']));
		}


		$this->url->redirect('extension/total');
	}
}
