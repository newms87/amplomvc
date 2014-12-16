<?php

class App_Controller_Admin_Settings_Store extends Controller
{
	public function index($data = array())
	{
		//Page Head
		$this->document->setTitle(_l("Settings"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Settings"), site_url('admin/settings/store'));

		//Settings Items
		$data['widgets'] = $this->Model_Setting_Setting->getWidgets();

		//Action Buttons
		$data['insert'] = site_url('admin/settings/store/form');

		//Render
		output($this->render('settings/store_list', $data));
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

		$image_width  = option('config_image_admin_thumb_width');
		$image_height = option('config_image_admin_thumb_height');

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

			$theme          = $this->config->load('config', 'config_theme');
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
		if (IS_AJAX) {
			output($output);
		}

		return $output;
	}

	public function form()
	{
		//Page Head
		$this->document->setTitle(_l("Store Settings"));

		//Insert or Update
		$store_id = _get('store_id', 0);

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Settings"), site_url('admin/settings/store'));
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
			'config_owner'                 => '',
			'config_address'               => '',
			'config_email'                 => '',
			'config_telephone'             => '',
			'config_fax'                   => '',
			'config_title'                 => '',
			'config_meta_description'      => '',
			'config_default_layout_id'     => '',
			'config_theme'                 => '',
			'config_country_id'            => option('config_country_id'),
			'config_zone_id'               => option('config_zone_id'),
			'config_language'              => option('config_language'),
			'config_currency'              => option('config_currency'),
			'config_catalog_limit'         => '12',
			'config_customer_group_id'     => '',
			'config_customer_approval'     => '',
			'config_account_terms_page_id' => '',
			'config_logo'                  => '',
			'config_logo_srcset'           => 1,
			'config_icon'                  => null,
			'config_logo_width'            => 0,
			'config_logo_height'           => 0,
			'config_email_logo_width'      => 300,
			'config_email_logo_height'     => 0,
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
		if (!is_array($store['config_icon'])) {
			$store['config_icon'] = array(
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

			if (!isset($store['config_icon'][$key])) {
				$store['config_icon'][$key] = '';
			}
		}

		foreach ($store['config_icon'] as &$icon) {
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

			if ($this->theme->install($_POST['config_theme'])) {
				message('error', $this->theme->getError());
			}

			message('success', _l("The Store settings have been saved."));
		} else {
			message('error', $this->Model_Setting_Store->getError());
		}

		if ($this->is_ajax) {
			output_json($this->message->fetch());
		} elseif ($this->message->has('error')) {
			post_redirect('admin/settings/store/form', 'store_id=' . _get('store_id'));
		} else {
			redirect('admin/settings/store');
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

		if (IS_AJAX) {
			output_json($this->message->fetch());
		} else {
			redirect('admin/settings/store');
		}
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
				$url = image_save($_POST['icon'], null, $size[0], $size[1]);

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

			output(json_encode($icon_files));
		}
	}
}