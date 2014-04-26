<?php
class Admin_Controller_Extension_Payment extends Controller
{
	private $extension_controller;

	public function index()
	{
		if (!empty($_GET['code'])) {
			if (!$this->System_Extension_Payment->has($_GET['code'])) {
				$this->message->add('warning', _l("The extension %s does not exist!", $_GET['code']));

				$this->url->redirect('extension/payment');
			}

			$this->getForm();
		} else {
			$this->getList();
		}
	}

	private function getList()
	{
		//Page Head
		$this->document->setTitle(_l("Payment"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Payment"), $this->url->link('extension/payment'));

		//The Table Columns
		$columns = array();

		$columns['title'] = array(
			'type'         => 'text',
			'display_name' => _l("Extension Name"),
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
		$extension_total = $this->System_Extension_Model->getTotal('payment', $filter);
		$extensions      = $this->System_Extension_Model->getExtensions('payment', $sort + $filter);

		foreach ($extensions as &$extension) {
			if ($extension['installed']) {
				$actions = array(
					'edit'      => array(
						'text' => _l("Edit"),
						'href' => $this->url->link('extension/payment/edit', 'code=' . $extension['code'])
					),
					'settings'  => array(
						'text' => _l("Settings"),
						'href' => $this->url->link('extension/payment', 'code=' . $extension['code'])
					),
					'uninstall' => array(
						'text' => _l("Uninstall"),
						'href' => $this->url->link('extension/payment/uninstall', 'code=' . $extension['code']),
					),
				);
			} else {
				$actions = array(
					'install' => array(
						'text' => _l("Install"),
						'href' => $this->url->link('extension/payment/install', 'code=' . $extension['code'])
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
		$this->response->setOutput($this->render('extension/payment_list', $data));
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

			$this->System_Extension_Model->updateExtension('payment', $code, $_POST);

			$this->message->add('success', _l("Successfully updated the settings for %s", $code));

			$this->url->redirect('extension/payment');
		}

		$payment_extension = $this->System_Extension_Payment->get($code);

		//Page Head
		$this->document->setTitle($payment_extension->info('title'));

		//Page Title
		$data['page_title'] = $payment_extension->info('title');

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Payment Extensions"), $this->url->link('extension/payment'));
		$this->breadcrumb->add($payment_extension->info('title'), $this->url->link('extension/payment', 'code=' . $code));

		//Entry Data
		if ($this->request->isPost()) {
			$extension_info = $_POST;
		} else {
			$extension_info = $payment_extension->info();

			$extension_info['settings'] = $payment_extension->settings();
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
		$data['save']   = $this->url->link('extension/payment', 'code=' . $code);
		$data['cancel'] = $this->url->link('extension/payment');

		//Render
		$this->response->setOutput($this->render('extension/payment', $data));
	}

	public function edit()
	{
		$code = !empty($_GET['code']) ? $_GET['code'] : '';

		//Verify File
		$file = DIR_SYSTEM . "extension/payment/" . $code . '.php';

		if (!is_file($file)) {
			$this->message->add('warning', _l("The extension file %s does not exist!", $file));
			$this->url->redirect('extension/payment');
		}

		//Handle POST
		if ($this->request->isPost()) {
			if (file_put_contents($file, html_entity_decode($_POST['contents']))) {
				$this->message->add('success', _l("Saved the extension file %s!", $file));
			} else {
				$this->message->add('warning', _l("There was a problem while saving the file %s!", $file));
			}

			$this->url->redirect('extension/payment');
		}

		//Load extension
		$payment_extension = $this->System_Extension_Payment->get($code);

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Payment Extensions"), $this->url->link('extension/payment'));
		$this->breadcrumb->add($payment_extension->info('title'), $this->url->link('extension/payment/edit', 'code=' . $code));

		//Load Contents
		$data['contents'] = file_get_contents($file);

		//Template Data
		$data['page_title'] = $payment_extension->info('title');
		$data['edit_file']  = $file;

		//Action Buttons
		$data['save']   = $this->url->link('extension/payment/edit', 'code=' . $code);
		$data['cancel'] = $this->url->link('extension/payment');

		//Render
		$this->response->setOutput($this->render('extension/edit', $data));
	}

	private function loadExtensionController($code)
	{
		if (!$this->extension_controller && !empty($code)) {
			$action = new Action('extension/payment/' . $code);

			if ($action->isValid()) {
				$this->extension_controller = $action->getController();
			}
		}
	}

	private function validate()
	{
		if (!$this->user->can('modify', 'extension/payment')) {
			$this->error['warning'] = _l("You do not have permission to modify the Payments system extension!");
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
		if ($this->System_Extension_Model->install('payment', $_GET['code'])) {
			$this->loadExtensionController($_GET['code']);

			$settings = array(
				'min_total'                => 0,
				'complete_order_status_id' => $this->config->get('config_order_complete_status_id'),
				'geo_zone_id'              => 0,
			);

			//Save Default Settings From Extension
			if (method_exists($this->extension_controller, 'settings')) {
				$this->extension_controller->settings($settings);
			}

			$this->System_Extension_Model->updateExtension('payment', $_GET['code'], array('settings' => $settings));

			$this->message->add('success', _l("Successfully installed the %s extension for Payments", $_GET['code']));
		} elseif ($this->System_Extension_Model->hasError()) {
			$this->message->add('error', $this->System_Extension_Model->getError());
		}

		$this->url->redirect('extension/payment');
	}

	public function uninstall()
	{
		if ($this->System_Extension_Model->uninstall('payment', $_GET['code'])) {
			$this->message->add('notify', _l("Uninstalled the %s extension for Payments", $_GET['code']));
		}


		$this->url->redirect('extension/payment');
	}
}
