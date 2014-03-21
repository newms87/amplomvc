<?php

class Admin_Controller_Setting_Store extends Controller
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

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified settings!"));

				$this->url->redirect('setting/store');
			}
		}

		$this->getForm();
	}

	public function delete()
	{
		//TODO: Change Permissions to include a query parameter (eg: store_id=$store_id). (by default this can be *, so no code breaking necessary)
		if (!empty($_GET['store_id']) && $this->user->can('modify', 'setting/store') && $this->canDelete($_GET['store_id'])) {
			$this->Model_Setting_Store->deleteStore($_GET['store_id']);
			$this->config->deleteGroup('config', $_GET['store_id']);

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified settings!"));
			}
		}

		$this->url->redirect('setting/store');
	}

	private function getList()
	{
		//Page Head
		$this->document->setTitle(_l("Settings"));

		//The Template
		$this->view->load('setting/store_list');

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Settings"), $this->url->link('setting/store'));

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

		$image_width  = $this->config->get('config_image_admin_thumb_width');
		$image_height = $this->config->get('config_image_admin_thumb_height');

		foreach ($stores as &$store) {
			$store['actions'] = array(
				'edit' => array(
					'text' => _l("Edit"),
					'href' => $this->url->link('setting/store/update', 'store_id=' . $store['store_id']),
				),
			);

			if ($this->canDelete($store['store_id'], true)) {
				$store['actions']['delete'] = array(
					'text' => _l("Delete"),
					'href' => $this->url->link('setting/store/delete', 'store_id=' . $store['store_id']),
				);
			}

			$theme          = $this->config->load('config', 'config_theme', $store['store_id']);
			$image          = DIR_SITE . 'catalog/view/theme/' . $theme . '/' . $theme . '.png';
			$store['thumb'] = $this->image->resize($image, $image_width, $image_height);

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

		$this->data['list_view'] = $this->table->render();

		//Render Limit Menu
		$this->data['limits'] = $this->sort->renderLimits();

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $store_total;

		$this->data['pagination'] = $this->pagination->render();

		//Settings Items
		$this->data['widgets'] = $this->Model_Setting_Setting->getWidgets();

		//Action Buttons
		$this->data['insert'] = $this->url->link('setting/store/update');
		$this->data['delete'] = $this->url->link('setting/store/delete');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	public function getForm()
	{
		//Page Head
		$this->document->setTitle(_l("Settings"));

		//Insert or Update
		$store_id = isset($_GET['store_id']) ? $_GET['store_id'] : 0;

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Settings"), $this->url->link('setting/store'));

		if ($store_id && !$this->request->isPost()) {
			$store = $this->Model_Setting_Store->getStore($store_id);

			if (!$store) {
				$this->message->add('warning', _l("You attempted to access a store that does not exist!"));
				$this->url->redirect('setting/store');
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
			'config_country_id'               => $this->config->get('config_country_id'),
			'config_zone_id'                  => $this->config->get('config_zone_id'),
			'config_language'                 => $this->config->get('config_language'),
			'config_currency'                 => $this->config->get('config_currency'),
			'config_catalog_limit'            => '12',
			'config_allowed_shipping_zone'    => 0,
			'config_show_price_with_tax'      => '',
			'config_tax_default'              => '',
			'config_tax_customer'             => '',
			'config_customer_group_id'        => '',
			'config_customer_hide_price'      => '',
			'config_show_product_model'       => 1,
			'config_customer_approval'        => '',
			'config_guest_checkout'           => '',
			'config_account_id'               => '',
			'config_checkout_id'              => '',
			'config_stock_display'            => '',
			'config_stock_checkout'           => '',
			'config_order_complete_status_id' => '',
			'config_cart_weight'              => '',
			'config_logo'                     => '',
			//Icon defaults set below
			'config_icon'                     => null,
			'config_logo_width'               => 0,
			'config_logo_height'              => 0,
			'config_email_logo_width'         => 300,
			'config_email_logo_height'        => 0,
			'config_image_thumb_width'        => 228,
			'config_image_thumb_height'       => 228,
			'config_image_popup_width'        => 500,
			'config_image_popup_height'       => 500,
			'config_image_product_width'      => 80,
			'config_image_product_height'     => 80,
			'config_image_category_width'     => 80,
			'config_image_category_height'    => 80,
			'config_image_additional_width'   => 74,
			'config_image_additional_height'  => 74,
			'config_image_related_width'      => 80,
			'config_image_related_height'     => 80,
			'config_image_compare_width'      => 90,
			'config_image_compare_height'     => 90,
			'config_image_wishlist_width'     => 50,
			'config_image_wishlist_height'    => 50,
			'config_image_cart_width'         => 80,
			'config_image_cart_height'        => 80,
			'config_use_ssl'                  => '',
			'config_contact_page_id'          => '',
		);

		$data += $store_info + $defaults;

		//Current Page Breadcrumb
		$this->breadcrumb->add($data['name'], $this->url->link('setting/store/update', 'store_id=' . $store_id));

		//Additional Info
		$data['layouts']              = $this->Model_Design_Layout->getLayouts();
		$data['themes']               = $this->theme->getThemes();
		$data['geo_zones']            = array_merge(array(0 => "--- All Zones ---"), $this->Model_Localisation_GeoZone->getGeoZones());
		$data['countries']            = $this->Model_Localisation_Country->getCountries();
		$data['languages']            = $this->Model_Localisation_Language->getLanguages();
		$data['currencies']           = $this->Model_Localisation_Currency->getCurrencies();
		$data['data_customer_groups'] = $this->Model_Sale_CustomerGroup->getCustomerGroups();
		$data['informations']         = $this->Model_Catalog_Information->getInformations();
		$data['data_order_statuses']  = $this->order->getOrderStatuses();
		$data['data_pages']           = array('' => _l(" --- Please Select --- ")) + $this->Model_Page_Page->getPages();

		$data['data_stock_display_types'] = array(
			'hide'   => _l("Do not display stock"),
			'status' => _l("Only show stock status"),
			-1       => _l("Display stock quantity available"),
			10       => _l("Display quantity up to 10"),
		);

		$data['data_yes_no'] = array(
			1 => _l("Yes"),
			0 => _l("No"),
		);

		//Website Icon Sizes
		if (!is_array($data['config_icon'])) {
			$data['config_icon'] = array(
				'orig' => '',
				'ico'  => '',
			);
		}

		$data['data_icon_sizes'] = array(
			array(152,152),
			array(120,120),
			array(76,76),
		);

		foreach ($data['data_icon_sizes'] as $size) {
			$key = $size[0] . 'x' . $size[1];

			if (!isset($data['config_icon'][$key])) {
				$data['config_icon'][$key] = '';
			}
		}

		//Action Buttons
		$data['save']               = $this->url->link('setting/store/update', 'store_id=' . $store_id);
		$data['cancel']             = $this->url->link('setting/store');
		$data['url_generate_icons'] = $this->url->link('setting/store/generate_icons');

		//Ajax Urls
		$data['load_theme_img'] = $this->url->link('setting/setting/theme');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

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
		if (!$this->user->can('modify', 'setting/store')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify stores!");
		}

		if (!$this->validation->text($_POST['name'], 1, 64)) {
			$this->error['name'] = _l("Store Name must be between 1 and 64 characters!");
		}

		if (!$this->validation->url($_POST['url'])) {
			$this->error['url'] = _l("Store URL invalid! Please provide a properly formatted URL (eg: http://yourstore.com)");
		}

		if (!$this->validation->url($_POST['ssl'])) {
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
			'image_product'    => "Product List",
			'image_additional' => "Product Additional",
			'image_related'    => "Product Related",
			'image_compare'    => "Product Compare",
			'image_wishlist'   => "Product Wishlist",
			'image_cart'       => "Cart",
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

		return $this->error ? false : true;
	}

	private function canDelete($store_id, $silent = false)
	{
		if ((int)$store_id < 1) {
			$error[$store_id]['warning'] = _l("Warning: You can not delete your default store!");
		} else {
			$filter = array(
				'store_ids' => array($store_id),
			);

			$store_total = $this->System_Model_Order->getTotalOrders($filter);

			if ($store_total) {
				$error[$store_id]['warning'] = _l("Warning: This Store cannot be deleted as it is currently assigned to %s orders!", $store_total);
			}
		}

		if (!$silent) {
			$this->error += $error;
		}

		return !isset($error[$store_id]);
	}
}
