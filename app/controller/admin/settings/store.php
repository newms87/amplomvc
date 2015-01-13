<?php

class App_Controller_Admin_Settings_Store extends Controller
{
	public function index($data = array())
	{
		//Page Head
		set_page_info('title', _l("Settings"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Settings"), site_url('admin/settings'));

		//Settings Items
		$data['widgets'] = $this->Model_Settings->getWidgets();

		//Action Buttons
		$data['insert'] = site_url('admin/settings/store/form');

		//Render
		output($this->render('settings/store/list', $data));
	}

	public function listing()
	{
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
		$filter = _get('filter', array());

		$store_total = $this->Model_Setting_Store->getTotalStores($filter);
		$stores      = $this->Model_Setting_Store->getStores($sort + $filter);

		$image_width  = option('admin_thumb_width');
		$image_height = option('admin_thumb_height');

		foreach ($stores as &$store) {
			$store['actions'] = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => site_url('admin/settings/store/form', 'store_id=' . $store['store_id']),
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => site_url('admin/settings/store/remove', 'store_id=' . $store['store_id']),
				)
			);

			$theme          = $this->config->load('general', 'site_theme');
			$image          = DIR_SITE . 'app/view/theme/' . $theme . '/' . $theme . '.png';
			$store['thumb'] = image($image, $image_width, $image_height);

		}
		unset($store);

		$listing = array(
			'row_id'         => 'store_id',
			'columns'        => $columns,
			'rows'           => $stores,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total_listings' => $store_total,
			'listing_path'   => 'admin/settings/store/listing',
		);

		$output = block('widget/listing', null, $listing);

		//Response
		if ($this->is_ajax) {
			output($output);
		}

		return $output;
	}

	public function form()
	{
		//Page Head
		set_page_info('title', _l("Store Settings"));

		//Insert or Update
		$store_id = _get('store_id', 0);

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Settings"), site_url('admin/settings'));
		breadcrumb(_l("Store"), site_url('admin/settings/store/form', 'store_id=' . $store_id));

		//Store Data
		$store = $_POST;

		if ($store_id && !IS_POST) {
			$store = $this->Model_Setting_Store->getStore($store_id);

			$store_config = $this->config->loadGroup('config');

			$store += $store_config;
		}

		$defaults = array(
			'name'                         => 'Store ' . $store_id,
			'url'                          => '',
			'ssl'                          => '',
			'site_owner'                   => '',
			'site_address'                 => '',
			'site_email'                   => '',
			'site_phone'                   => '',
			'config_fax'                   => '',
			'config_title'                 => '',
			'site_meta_description'        => '',
			'config_default_layout_id'     => '',
			'site_theme'                   => '',
			'config_country_id'            => option('config_country_id'),
			'config_zone_id'               => option('config_zone_id'),
			'config_language'              => option('config_language'),
			'config_currency'              => option('config_currency'),
			'site_list_limit'              => '12',
			'config_customer_group_id'     => '',
			'config_customer_approval'     => '',
			'config_account_terms_page_id' => '',
			'site_logo'                    => '',
			'site_logo_srcset'             => 1,
			'site_icon'                    => null,
			'site_logo_width'              => 0,
			'site_logo_height'             => 0,
			'site_email_logo_width'        => 300,
			'site_email_logo_height'       => 0,
			'config_image_thumb_width'     => 228,
			'config_image_thumb_height'    => 228,
			'config_image_popup_width'     => 500,
			'config_image_popup_height'    => 500,
			'config_use_ssl'               => '',
			'config_contact_page_id'       => '',
		);

		$store += $defaults;

		//Additional Info
		$store['data_layouts']         = $this->Model_Design_Layout->getLayouts();
		$store['data_themes']          = $this->theme->getThemes();
		$store['data_countries']       = $this->Model_Localisation_Country->getCountries();
		$store['data_languages']       = $this->Model_Localisation_Language->getLanguages();
		$store['data_currencies']      = $this->Model_Localisation_Currency->getCurrencies();
		$store['data_customer_groups'] = $this->Model_Customer->getCustomerGroups();
		$store['data_pages']           = array('' => _l(" --- None --- ")) + $this->Model_Page->getPages();
		$store['data_pages']           = array('' => _l(" --- Please Select --- ")) + $this->Model_Page->getPages();

		$store['data_yes_no'] = array(
			1 => _l("Yes"),
			0 => _l("No"),
		);

		//Website Icon Sizes
		if (!is_array($store['site_icon'])) {
			$store['site_icon'] = array(
				'orig' => '',
				'ico'  => '',
			);
		}

		$store['data_icon_sizes'] = array(
			'152' => array(
				152,
				152
			),
			'120' => array(
				120,
				120
			),
			'76'  => array(
				76,
				76
			),
		);

		foreach ($store['data_icon_sizes'] as $size) {
			$key = $size[0] . 'x' . $size[1];

			if (!isset($store['site_icon'][$key])) {
				$store['site_icon'][$key] = '';
			}
		}

		foreach ($store['site_icon'] as &$icon) {
			$icon = array(
				'thumb' => $this->image->get($icon),
				'src'   => $icon,
			);
		}
		unset($icon);

		//Action Buttons
		$store['save'] = site_url('admin/settings/store/save', 'store_id=' . $store_id);

		//Render
		output($this->render('settings/store/form', $store));
	}

	public function save()
	{
		if ($this->Model_Setting_Store->save(_get('store_id'), $_POST)) {
			$this->config->saveGroup('config', $_POST);

			if ($this->theme->install($_POST['site_theme'])) {
				message('error', $this->theme->getError());
			}

			message('success', _l("The Store settings have been saved."));
		} else {
			message('error', $this->Model_Setting_Store->getError());
		}

		if ($this->is_ajax) {
			output_message();
		} elseif ($this->message->has('error')) {
			post_redirect('admin/settings/store/form', 'store_id=' . _get('store_id'));
		} else {
			redirect('admin/settings');
		}
	}

	public function remove()
	{
		$this->Model_Setting_Store->remove($_GET['store_id']);

		if ($this->Model_Setting_Store->hasError()) {
			message('error', $this->Model_Setting_Store->getError());
		} else {
			message('notify', _l("User was deleted!"));
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/settings');
		}
	}
}
