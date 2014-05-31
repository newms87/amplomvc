<?php

class App_Controller_Admin_Setting_Store extends Controller
{
	public function index()
	{
		$this->getList();
	}

	public function update()
	{
		$this->document->setTitle(_l("Settings"));

		if ($this->request->isPost() && $this->validateForm()) {
			//Insert
			if (empty($_GET['store_id'])) {
				$store_id = $this->Model_Setting_Store->addStore($_POST);
				$this->config->saveGroup('config', $_POST, $store_id);
			} //Update
			else {
				$this->Model_Setting_Store->editStore($_GET['store_id'], $_POST);
				$this->config->saveGroup('config', $_POST, $_GET['store_id']);
			}

			if (!$this->message->has('error', 'warning')) {
				$this->message->add('success', _l("The Store settings have been saved!"));

				redirect('admin/setting/store');
			}
		}

		$this->getForm();
	}

	public function delete()
	{
		//TODO: Change Permissions to include a query parameter (eg: store_id=$store_id). (by default this can be *, so no code breaking necessary)
		if (!empty($_GET['store_id']) && user_can('modify', 'setting/store') && $this->canDelete($_GET['store_id'])) {
			$this->Model_Setting_Store->deleteStore($_GET['store_id']);
			$this->config->deleteGroup('config', $_GET['store_id']);

			if (!$this->message->has('error', 'warning')) {
				$this->message->add('success', _l("Success: You have modified settings!"));
			}
		}

		redirect('admin/setting/store');
	}

	private function getList()
	{
		//Page Head
		$this->document->setTitle(_l("Settings"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('admin/common/home'));
		$this->breadcrumb->add(_l("Settings"), site_url('admin/setting/store'));

		//The Table Columns
		$columns = array();

		$columns['thumb'] = array(
			'type'         => 'image',
			'display_name' => _l("Store Preview"),
		);

		$columns['name'] = array(
			'type'         => 'text',
			'display_name' => _l("Store Name"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['url'] = array(
			'type'         => 'text',
			'display_name' => _l("Store URL"),
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

		//Get Sorted / Filtered Data
		$sort   = $this->sort->getQueryDefaults('name', 'ASC');
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		$store_total = $this->Model_Setting_Store->getTotalStores($filter);
		$stores      = $this->Model_Setting_Store->getStores($sort + $filter);

		$image_width  = option('config_image_admin_thumb_width');
		$image_height = option('config_image_admin_thumb_height');

		foreach ($stores as &$store) {
			$store['actions'] = array(
				'edit' => array(
					'text' => _l("Edit"),
					'href' => site_url('admin/setting/store/update', 'store_id=' . $store['store_id']),
				),
			);

			if ($this->canDelete($store['store_id'], true)) {
				$store['actions']['delete'] = array(
					'text' => _l("Delete"),
					'href' => site_url('admin/setting/store/delete', 'store_id=' . $store['store_id']),
				);
			}

			$theme          = $this->config->load('config', 'config_theme', $store['store_id']);
			$image          = DIR_SITE . 'app/view/theme/' . $theme . '/' . $theme . '.png';
			$store['thumb'] = image($image, $image_width, $image_height);

		}
		unset($store);

		//Build The Table
		$tt_data = array(
			'row_id' => 'store_id',
		);

		$this->table->init();
		$this->table->setTemplate('table/list_view');
		$this->table->setColumns($columns);
		$this->table->setRows($stores);
		$this->table->setTemplateData($tt_data);
		$this->table->mapAttribute('filter_value', $filter);

		//Template Data
		$data = array();

		$data['list_view'] = $this->table->render();

		//Render Limit Menu
		$data['limits'] = $this->sort->renderLimits();

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $store_total;

		$data['pagination'] = $this->pagination->render();

		//Settings Items
		$data['widgets'] = $this->Model_Setting_Setting->getWidgets();

		//Action Buttons
		$data['insert'] = site_url('admin/setting/store/update');
		$data['delete'] = site_url('admin/setting/store/delete');

		//Render
		$this->response->setOutput($this->render('setting/store_list', $data));
	}

	public function getForm()
	{
		//Page Head
		$this->document->setTitle(_l("Settings"));

		//Insert or Update
		$store_id = isset($_GET['store_id']) ? $_GET['store_id'] : 0;

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('admin/common/home'));
		$this->breadcrumb->add(_l("Settings"), site_url('admin/setting/store'));

		if ($store_id && !$this->request->isPost()) {
			$store = $this->Model_Setting_Store->getStore($store_id);

			if (!$store) {
				$this->message->add('warning', _l("You attempted to access a store that does not exist!"));
				redirect('admin/setting/store');
			}

			$store_config = $this->config->loadGroup('config', $store_id);

			if (empty($store_config)) {
				$store_config = $this->config->loadGroup('config', 0);
			}

			$store_info = $store + $store_config;
		} else {
			$store_info = $_POST;
		}

		$data = array();

		$defaults = array(
			'name'                            => 'Store ' . $store_id,
			'url'                             => '',
			'ssl'                             => '',
			'config_owner'                    => '',
			'config_address'                  => '',
			'config_email'                    => '',
			'config_telephone'                => '',
			'config_fax'                      => '',
			'config_title'                    => '',
			'config_meta_description'         => '',
			'config_default_layout_id'        => '',
			'config_theme'                    => '',
			'config_country_id'               => option('config_country_id'),
			'config_zone_id'                  => option('config_zone_id'),
			'config_language'                 => option('config_language'),
			'config_currency'                 => option('config_currency'),
			'config_catalog_limit'            => '12',
			'config_customer_group_id'        => '',
			'config_customer_approval'        => '',
			'config_account_terms_page_id'               => '',
			'config_logo'                     => '',
			'config_icon'                     => null,
			'config_logo_width'               => 0,
			'config_logo_height'              => 0,
			'config_email_logo_width'         => 300,
			'config_email_logo_height'        => 0,
			'config_image_thumb_width'        => 228,
			'config_image_thumb_height'       => 228,
			'config_image_popup_width'        => 500,
			'config_image_popup_height'       => 500,
			'config_use_ssl'                  => '',
			'config_contact_page_id'          => '',
		);

		$data += $store_info + $defaults;

		//Current Page Breadcrumb
		$this->breadcrumb->add($data['name'], site_url('admin/setting/store/update', 'store_id=' . $store_id));

		//Additional Info
		$data['layouts']              = $this->Model_Design_Layout->getLayouts();
		$data['data_themes']               = $this->theme->getThemes();
		$data['geo_zones']            = array_merge(array(0 => "--- All Zones ---"), $this->Model_Localisation_GeoZone->getGeoZones());
		$data['countries']            = $this->Model_Localisation_Country->getCountries();
		$data['languages']            = $this->Model_Localisation_Language->getLanguages();
		$data['currencies']           = $this->Model_Localisation_Currency->getCurrencies();
		$data['data_customer_groups'] = $this->Model_Customer->getCustomerGroups();
		$data['data_pages']             = array('' => _l(" --- None --- ")) + $this->Model_Page_Page->getPages();
		$data['data_pages']           = array('' => _l(" --- Please Select --- ")) + $this->Model_Page_Page->getPages();

		$data['data_yes_no'] = array(
			1 => _l("Yes"),
			0 => _l("No"),
		);

		//Logo Sizing
		$data['logo_thumb'] = $this->image->get($data['config_logo']);

		//Website Icon Sizes
		if (!is_array($data['config_icon'])) {
			$data['config_icon'] = array(
				'orig' => '',
				'ico'  => '',
			);
		}

		$data['data_icon_sizes'] = array(
			array(
				152,
				152
			),
			array(
				120,
				120
			),
			array(
				76,
				76
			),
		);

		foreach ($data['data_icon_sizes'] as $size) {
			$key = $size[0] . 'x' . $size[1];

			if (!isset($data['config_icon'][$key])) {
				$data['config_icon'][$key] = '';
			}
		}

		foreach ($data['config_icon'] as &$icon) {
			$icon = array(
				'thumb' => $this->image->get($icon),
				'src'   => $icon,
			);
		}
		unset($icon);

		//Action Buttons
		$data['save']               = site_url('admin/setting/store/update', 'store_id=' . $store_id);
		$data['cancel']             = site_url('admin/setting/store');
		$data['url_generate_icons'] = site_url('admin/setting/store/generate_icons');

		//Render
		$this->response->setOutput($this->render('setting/store_form', $data));
	}

	public function generate_icons()
	{
		if (!empty($_POST['icon'])) {
			$sizes = array(
				array(
					152,
					152
				),
				array(
					120,
					120
				),
				array(
					76,
					76
				),
			);

			$icon_files = array();

			foreach ($sizes as $size) {
				$url = $this->image->resize($_POST['icon'], $size[0], $size[1]);

				$icon_files[$size[0] . 'x' . $size[1]] = array(
					'url'     => $url,
					'relpath' => str_replace(URL_IMAGE, '', $url),
				);
			}

			$url = $this->image->ico($_POST['icon']);

			$icon_files['ico'] = array(
				'relpath' => str_replace(URL_IMAGE, '', $url),
				'url'     => $url,
			);

			$this->response->setOutput(json_encode($icon_files));
		}
	}

	private function validateForm()
	{
		if (!user_can('modify', 'setting/store')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify stores!");
		}

		if (!validate('text', $_POST['name'], 1, 64)) {
			$this->error['name'] = _l("Store Name must be between 1 and 64 characters!");
		}

		if (!validate('url', $_POST['url'])) {
			$this->error['url'] = _l("Store URL invalid! Please provide a properly formatted URL (eg: http://yourstore.com)");
		}

		if (!validate('url', $_POST['ssl'])) {
			$this->error['ssl'] = _l("Store SSL invalid!  Please provide a properly formatted URL (eg: http://yourstore.com). NOTE: you may set this to the same value as URL, does not have to be HTTPS protocol.");
		}

		if ((strlen($_POST['config_owner']) < 3) || (strlen($_POST['config_owner']) > 64)) {
			$this->error['config_owner'] = _l("Store Owner must be between 3 and 64 characters!");
		}

		if ((strlen($_POST['config_address']) < 3) || (strlen($_POST['config_address']) > 256)) {
			$this->error['config_address'] = _l("Store Address must be between 10 and 256 characters!");
		}

		if ((strlen($_POST['config_email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $_POST['config_email'])) {
			$this->error['config_email'] = _l("E-Mail Address does not appear to be valid!");
		}

		if ((strlen($_POST['config_telephone']) < 3) || (strlen($_POST['config_telephone']) > 32)) {
			$this->error['config_telephone'] = _l("Telephone must be between 3 and 32 characters!");
		}

		if (!$_POST['config_title']) {
			$this->error['config_title'] = _l("Title must be between 3 and 32 characters!");
		}

		$image_sizes = array(
			'image_category'   => "Category List",
			'image_thumb'      => "Product Thumb",
			'image_popup'      => "Product Popup",
		);

		foreach ($image_sizes as $image_key => $image_size) {
			$image_width  = 'config_' . $image_key . '_width';
			$image_height = 'config_' . $image_key . '_height';

			if ((int)$_POST[$image_width] <= 0 || (int)$_POST[$image_height] <= 0) {
				$this->error[$image_height] = _l("%s image dimensions are required.", $image_size);
			}
		}

		if ((int)$_POST['config_catalog_limit'] <= 0) {
			$this->error['config_catalog_limit'] = _l("Limit required!");
		}

		return empty($this->error);
	}

	private function canDelete($store_id, $silent = false)
	{
		if ((int)$store_id < 1) {
			$error[$store_id]['warning'] = _l("Warning: You can not delete your default store!");
		}

		if (!$silent) {
			$this->error += $error;
		}

		return !isset($error[$store_id]);
	}
}
