<?php
class Admin_Controller_Setting_Store extends Controller
{
	public function index()
	{
		$this->language->load('setting/store');

		$this->getList();
	}

	public function update()
	{
		$this->language->load('setting/store');

		$this->document->setTitle($this->_('head_title'));

		if ($this->request->isPost() && $this->validateForm()) {
			//Insert
			if (empty($_GET['store_id'])) {
				$store_id = $this->Model_Setting_Store->addStore($_POST);
				$this->Model_Setting_Setting->editSetting('config', $_POST, $store_id);
			} //Update
			else {
				$this->Model_Setting_Store->editStore($_GET['store_id'], $_POST);
				$this->Model_Setting_Setting->editSetting('config', $_POST, $_GET['store_id']);
			}

			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));

				$this->url->redirect($this->url->link('setting/store'));
			}
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->language->load('setting/store');

		//TODO: Change Permissions to include a query parameter (eg: store_id=$store_id). (by default this can be *, so no code breaking necessary)
		if (!empty($_GET['store_id']) && $this->user->hasPermission('modify', 'setting/store') && $this->canDelete($_GET['store_id'])) {
			$this->Model_Setting_Store->deleteStore($_GET['store_id']);
			$this->Model_Setting_Setting->deleteSetting('config', $_GET['store_id']);

			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
			}
		}

		$this->url->redirect($this->url->link('setting/store'));
	}

	private function getList()
	{
		//Page Head
		$this->document->setTitle($this->_('head_title'));

		//The Template
		$this->template->load('setting/store_list');

		$url = $this->url->getQuery('page');

		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('setting/store'));

		//The Table Columns
		$columns = array();

		$columns['thumb'] = array(
			'type'         => 'image',
			'display_name' => $this->_('column_image'),
		);

		$columns['name'] = array(
			'type'         => 'text',
			'display_name' => $this->_('column_name'),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['url'] = array(
			'type'         => 'text',
			'display_name' => $this->_('column_url'),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['status'] = array(
			'type'         => 'select',
			'display_name' => $this->_('column_status'),
			'filter'       => true,
			'build_data'   => $this->_('data_statuses'),
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
					'text' => $this->_('text_edit'),
					'href' => $this->url->link('setting/store/update', 'store_id=' . $store['store_id']),
				),
			);

			if ($this->canDelete($store['store_id'], true)) {
				$store['actions']['delete'] = array(
					'text' => $this->_('text_delete'),
					'href' => $this->url->link('setting/store/delete', 'store_id=' . $store['store_id']),
				);
			}

			$theme          = $this->config->load('config', 'config_theme', $store['store_id']);
			$image          = DIR_CATALOG . 'view/theme/' . $theme . '/' . $theme . '.png';
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
		$this->data['limits'] = $this->sort->render_limit();

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $store_total;

		$this->data['pagination'] = $this->pagination->render();

		//Urls
		$this->data['admin_settings'] = $this->url->link('setting/setting');
		$this->data['system_update']  = $this->url->link('setting/update');

		//Action Buttons
		$this->data['save'] = $this->url->link('setting/store/update');
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
		$this->document->setTitle($this->_('head_title'));

		//The Template
		$this->template->load('setting/store_form');

		//Insert or Update
		$store_id = isset($_GET['store_id']) ? $_GET['store_id'] : 0;

		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('setting/store'));

		if (!$store_id) {
			$this->data['action'] = $this->url->link('setting/store/update');
		} else {
			$this->data['action'] = $this->url->link('setting/store/update', 'store_id=' . $store_id);
		}

		$this->data['cancel'] = $this->url->link('setting/store');

		if ($store_id && !$this->request->isPost()) {
			$store = $this->Model_Setting_Store->getStore($store_id);

			if (!$store) {
				$this->message->add('warning', $this->_('error_store_invalid'));
				$this->url->redirect($this->url->link('setting/store'));
			}

			$store_config = $this->Model_Setting_Setting->getSetting('config', $store_id);

			if (empty($store_config)) {
				$store_config = $this->Model_Setting_Setting->getSetting('config', 0);
			}

			$store_info = $store + $store_config;
		}

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
			'config_icon'                     => '',
			'config_image_category_height'    => 80,
			'config_image_thumb_width'        => 228,
			'config_image_thumb_height'       => 228,
			'config_image_popup_width'        => 500,
			'config_image_popup_height'       => 500,
			'config_image_product_width'      => 80,
			'config_image_product_height'     => 80,
			'config_image_category_width'     => 80,
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
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($store_info[$key])) {
				$this->data[$key] = $store_info[$key];
			} else {
				$this->data[$key] = $default;
			}
		}

		//Current Page Breadcrumb
		$this->breadcrumb->add($this->data['name'], $this->url->link('setting/store/update', 'store_id=' . $store_id));

		//Additional Info
		$this->data['layouts']             = $this->Model_Design_Layout->getLayouts();
		$this->data['themes']              = $this->theme->getThemes();
		$this->data['geo_zones']           = array_merge(array(0 => "--- All Zones ---"), $this->Model_Localisation_GeoZone->getGeoZones());
		$this->data['countries']           = $this->Model_Localisation_Country->getCountries();
		$this->data['languages']           = $this->Model_Localisation_Language->getLanguages();
		$this->data['currencies']          = $this->Model_Localisation_Currency->getCurrencies();
		$this->data['customer_groups']     = $this->Model_Sale_CustomerGroup->getCustomerGroups();
		$this->data['informations']        = $this->Model_Catalog_Information->getInformations();
		$this->data['data_order_statuses'] = $this->order->getOrderStatuses();

		//Ajax Urls
		$this->data['load_theme_img'] = $this->url->ajax('setting/setting/theme');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	private function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'setting/store')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if (!$this->validation->text($_POST['name'], 1, 64)) {
			$this->error['name'] = $this->_('error_name');
		}

		if (!$this->validation->url($_POST['url'])) {
			$this->error['url'] = $this->_('error_url');
		}

		if (!$this->validation->url($_POST['ssl'])) {
			$this->error['ssl'] = $this->_('error_ssl');
		}

		if ((strlen($_POST['config_owner']) < 3) || (strlen($_POST['config_owner']) > 64)) {
			$this->error['config_owner'] = $this->_('error_owner');
		}

		if ((strlen($_POST['config_address']) < 3) || (strlen($_POST['config_address']) > 256)) {
			$this->error['config_address'] = $this->_('error_address');
		}

		if ((strlen($_POST['config_email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $_POST['config_email'])) {
			$this->error['config_email'] = $this->_('error_email');
		}

		if ((strlen($_POST['config_telephone']) < 3) || (strlen($_POST['config_telephone']) > 32)) {
			$this->error['config_telephone'] = $this->_('error_telephone');
		}

		if (!$_POST['config_title']) {
			$this->error['config_title'] = $this->_('error_title');
		}

		$image_sizes = array(
			'image_category',
			'image_thumb',
			'image_popup',
			'image_product',
			'image_additional',
			'image_related',
			'image_compare',
			'image_wishlist',
			'image_cart',
		);

		foreach ($image_sizes as $image_size) {
			$image_width  = 'config_' . $image_size . '_width';
			$image_height = 'config_' . $image_size . '_height';

			if ((int)$_POST[$image_width] <= 0 || (int)$_POST[$image_height] <= 0) {
				$this->error[$image_height] = $this->_('error_' . $image_size);
			}
		}

		if ((int)$_POST['config_catalog_limit'] <= 0) {
			$this->error['config_catalog_limit'] = $this->_('error_limit');
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->hasPermission('modify', 'setting/store')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		foreach ($_GET['selected'] as $store_id) {
			$this->canDelete($store_id);
		}

		return $this->error ? false : true;
	}

	private function canDelete($store_id, $silent = false)
	{
		if ((int)$store_id < 1) {
			$error[$store_id]['warning'] = $this->_('error_default');
		} else {
			$filter = array(
				'store_ids' => array($store_id),
			);

			$store_total = $this->System_Model_Order->getTotalOrders($filter);

			if ($store_total) {
				$error[$store_id]['warning'] = $this->_('error_store', $store_total);
			}
		}

		if (!$silent) {
			$this->error += $error;
		}

		return !isset($error[$store_id]);
	}
}