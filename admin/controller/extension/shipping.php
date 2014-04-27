<?php
class Admin_Controller_Extension_Shipping extends Controller
{
	private $extension_controller;

	public function index()
	{
		if (!empty($_GET['code'])) {
			if (!$this->System_Extension_Shipping->has($_GET['code'])) {
				$this->message->add('warning', _l("The extension %s does not exist!", $_GET['code']));

				$this->url->redirect('extension/shipping');
			}

			$this->getForm();
		} else {
			$this->getList();
		}
	}

	//TODO: Implement the Add / Delete functionality for Extensions
	public function delete()
	{
		if ($this->user->can('modify', 'extension/shipping')) {
			if (!empty($_GET['code'])) {
				$this->System_Extension_Shipping->deleteExtension($_GET['code']);

				if (!$this->message->has('error', 'warning')) {
					$this->message->add('success', _l("You have successfully removed the %s extension.", $_GET['code']));
				}

				$this->url->redirect('extension/shipping', $this->url->getQueryExclude('name'));
			}
		} else {
			$this->error['permission'] = _l("You do not have permission to modify the Shipping extensions");
		}

		$this->index();
	}

	private function getList()
	{
		//Page Head
		$this->document->setTitle(_l("Shipping Extensions"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Shipping Extensions"), $this->url->link('extension/shipping'));

		//The Table Columns
		$columns = array();

		$columns['title'] = array(
			'type'         => 'text',
			'display_name' => _l("Shipping Title"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['code'] = array(
			'type'         => 'text',
			'display_name' => _l("Code"),
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
		$extension_total = $this->System_Extension_Model->getTotal('shipping', $filter);
		$extensions      = $this->System_Extension_Model->getExtensions('shipping', $sort + $filter);

		foreach ($extensions as &$extension) {
			if ($extension['installed']) {
				$actions = array(
					'edit'      => array(
						'text' => _l("Edit"),
						'href' => $this->url->link('extension/shipping/edit', 'code=' . $extension['code'])
					),
					'settings'  => array(
						'text' => _l("Settings"),
						'href' => $this->url->link('extension/shipping', 'code=' . $extension['code'])
					),
					'uninstall' => array(
						'text' => _l("Uninstall"),
						'href' => $this->url->link('extension/shipping/uninstall', 'code=' . $extension['code']),
					),
				);
			} else {
				$actions = array(
					'install' => array(
						'text' => _l("Install"),
						'href' => $this->url->link('extension/shipping/install', 'code=' . $extension['code'])
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
		$this->response->setOutput($this->render('extension/shipping_list', $data));
	}

	private function getForm()
	{
		$code = $_GET['code'];

		$this->loadExtensionController($code);

		//Handle POST
		if ($this->request->isPost() && $this->validate()) {
			//If Extension needs to customize the way data is stored
			if (method_exists($this->extension_controller, 'saveSettings')) {
				$this->extension_controller->saveSettings($_POST['settings']);
			}

			$this->System_Extension_Model->updateExtension('shipping', $code, $_POST);

			$this->message->add('success', _l("You have successfully modified the %s extension!", $code));

			$this->url->redirect('extension/shipping');
		}

		$shipping_extension = $this->System_Extension_Shipping->get($code);

		//Page Head
		$this->document->setTitle($shipping_extension->info('title'));

		//Page Title
		$data['page_title'] = $shipping_extension->info('title');

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Shipping Extensions"), $this->url->link('extension/shipping'));
		$this->breadcrumb->add($shipping_extension->info('title'), $this->url->link('extension/shipping', 'code=' . $code));

		//Load Information
		if ($this->request->isPost()) {
			$extension_info = $_POST;
		} else {
			$extension_info = $shipping_extension->info();

			$extension_info['settings']= $shipping_extension->settings();
		}

		//NOTE: 'settings', 'sort_order', and 'status' defaults are never used (in theory. Defaults are set in System_Extension_Model.
		$defaults = array(
			'settings'   => array(),
			'sort_order' => 0,
			'status'     => 1,
		);

		$data += $extension_info + $defaults;

		$settings_defaults = array(
			'min_total'                => 0,
			'complete_order_status_id' => $this->config->get('config_order_complete_status_id'),
			'geo_zone_id'              => 0,
		);

		$data['settings'] += $settings_defaults;

		//Get additional extension settings and profile data (this is the plugin part)
		if (method_exists($this->extension_controller, 'settings')) {
			$this->extension_controller->settings($data['settings']);
			$data['extend_settings'] = $this->extension_controller->output;
		}

		//Template Data
		$data['data_order_statuses'] = $this->order->getOrderStatuses();
		$data['data_geo_zones']      = array(0 => _l("All Zones")) + $this->Model_Localisation_GeoZone->getGeoZones();

		$data['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		//Action Buttons
		$data['save']   = $this->url->link('extension/shipping', 'code=' . $code);
		$data['cancel'] = $this->url->link('extension/shipping');

		//Render
		$this->response->setOutput($this->render('extension/shipping', $data));
	}

	public function edit()
	{
		$code = !empty($_GET['code']) ? $_GET['code'] : '';

		//Verify File
		$file = DIR_SYSTEM . "extension/shipping/" . $code . '.php';

		if (!is_file($file)) {
			$this->message->add('warning', _l("Unable to locate %s for editing!", $file));
			$this->url->redirect('extension/shipping');
		}

		//Handle POST
		if ($this->request->isPost()) {
			if (file_put_contents($file, html_entity_decode($_POST['contents']))) {
				$this->message->add('success', _l("Successfully modified %s", $file));
			} else {
				$this->message->add('warning', _l("Failed to save changes to %s", $file));
			}

			$this->url->redirect('extension/shipping');
		}

		//Load extension
		$shipping_extension = $this->System_Extension_Shipping->get($code);

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Shipping Extensions"), $this->url->link('extension/shipping'));
		$this->breadcrumb->add($shipping_extension->info('title'), $this->url->link('extension/shipping/edit', 'code=' . $code));

		//Load Contents
		$data['contents'] = file_get_contents($file);

		//Template Data
		$data['page_title'] = $shipping_extension->info('title');
		$data['edit_file']  = $file;

		//Action Buttons
		$data['save']   = $this->url->link('extension/shipping/edit', 'code=' . $code);
		$data['cancel'] = $this->url->link('extension/shipping');

		//Render
		$this->response->setOutput($this->render('extension/edit', $data));
	}

	private function loadExtensionController($code)
	{
		if (!$this->extension_controller && !empty($code)) {
			$action = new Action('extension/shipping/' . $code);

			if ($action->isValid()) {
				$this->extension_controller = $action->getController();
			}
		}
	}

	private function validate()
	{
		if (!$this->user->can('modify', 'extension/shipping')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify shipping!");
		}

		if (method_exists($this->extension_controller, 'validate')) {
			if (!$this->extension_controller->validate()) {
				return false;
			}
		}

		return empty($this->error);
	}

	public function install()
	{
		if ($this->System_Extension_Model->install('shipping', $_GET['code'])) {
			$this->loadExtensionController($_GET['code']);

			$settings = array(
				'min_total'   => 0,
				'geo_zone_id' => 0,
			);

			//Save Default Settings From Extension
			if (method_exists($this->extension_controller, 'settings')) {
				$this->extension_controller->settings($settings);
			}

			$this->System_Extension_Model->updateExtension('shipping', $_GET['code'], array('settings' => $settings));

			$this->message->add('success', _l("You have successfully installed %s!", $_GET['code']));
		} elseif ($this->System_Extension_Model->hasError()) {
			$this->message->add('error', $this->System_Extension_Model->getError());
		}

		$this->url->redirect('extension/shipping');
	}

	public function uninstall()
	{
		if ($this->System_Extension_Model->uninstall('shipping', $_GET['code'])) {
			$this->message->add('notify', _l("%s has been uninstalled.", $_GET['code']));
		} elseif ($this->System_Extension_Model->hasError()) {
			$this->message->add('error', $this->System_Extension_Model->getError());
		}

		$this->url->redirect('extension/shipping');
	}
}
