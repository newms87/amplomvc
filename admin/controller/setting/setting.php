<?php
/**
 * Class Admin_Controller_Setting_Setting
 *
 * Title: General Settings
 * Icon: admin_settings.php
 *
 */
class Admin_Controller_Setting_Setting extends Controller
{
	public function index()
	{
		//The Template and Language
		$this->view->load('setting/setting');
		//Page Head
		$this->document->setTitle(_l("General Settings"));

		//Handle Post
		if ($this->request->isPost() && $this->validate()) {
			$this->config->saveGroup('config', $_POST, 0, true);

			//TODO: Move this to Cron
			if ($this->config->get('config_currency_auto')) {
				$this->Model_Localisation_Currency->updateCurrencies();
			}

			$this->message->add('success', _l("Success: You have modified settings!"));

			$this->url->redirect('setting/store');
		}

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Settings"), $this->url->link('setting/store'));
		$this->breadcrumb->add(_l("General Settings"), $this->url->link('setting/setting'));

		//Load Information
		if (!$this->request->isPost()) {
			$config_data = $this->config->loadGroup('config', 0);
		} else {
			$config_data = $_POST;
		}

		$defaults = array(
			'config_name'                             => '',
			'config_owner'                            => '',
			'config_address'                          => '',
			'config_email'                            => '',
			'config_email_support'                    => '',
			'config_email_error'                      => '',
			'config_telephone'                        => '',
			'config_fax'                              => '',
			'config_title'                            => '',
			'config_default_store'                    => '',
			'config_meta_description'                 => '',
			'config_debug'                            => 0,
			'config_cron_status'                      => 1,
			'config_allow_close_message_box'          => 1,
			'config_default_layout_id'                => '',
			'config_theme'                            => 'default',
			'config_address_format'                   => '',
			'config_country_id'                       => 223,
			'config_zone_id'                          => 8,
			'config_language'                         => 1,
			'config_admin_language'                   => 1,
			'config_use_macro_languages'              => 0,
			'config_currency'                         => '',
			'config_currency_auto'                    => '',
			'config_length_class_id'                  => 0,
			'config_weight_class_id'                  => 0,
			'config_catalog_limit'                    => 10,
			'config_admin_limit'                      => 20,
			'config_autocomplete_limit'               => 10,
			'config_performance_log'                  => 0,
			'config_default_return_policy'            => 0,
			'config_default_shipping_policy'          => 0,
			'config_shipping_return_info_id'          => 0,
			'config_cache_ignore'                     => '',
			'config_cart_show_return_policy'          => 1,
			'config_show_price_with_tax'              => '',
			'config_tax_default_id'                   => '',
			'config_tax_default'                      => '',
			'config_tax_customer'                     => '',
			'config_invoice_prefix'                   => 'INV-%Y-M%',
			'config_order_edit'                       => 7,
			'config_customer_group_id'                => '',
			'config_customer_hide_price'              => 0,
			'config_customer_approval'                => 0,
			'config_guest_checkout'                   => '',
			'config_account_terms_info_id'            => 0,
			'config_checkout_terms_info_id'           => 0,
			'config_breadcrumb_display'               => 1,
			'config_breadcrumb_separator'             => '/',
			'config_breadcrumb_separator_admin'       => '/',
			'config_show_category_image'              => 1,
			'config_show_category_description'        => 1,
			'config_show_product_list_hover_image'    => 0,
			'config_stock_display'                    => 1,
			'config_stock_warning'                    => 1,
			'config_stock_checkout'                   => 0,
			'config_stock_status_id'                  => 0,
			'config_show_product_related'             => 1,
			'config_order_received_status_id'         => 0,
			'config_order_complete_status_id'         => 0,
			'config_order_blacklist_status_id'        => 0,
			'config_order_fraud_status_id'            => 0,
			'config_return_status_id'                 => 0,
			'config_review_status'                    => 1,
			'config_share_status'                     => 1,
			'config_show_product_attributes'          => 1,
			'config_download'                         => 1,
			'config_upload_allowed'                   => 1,
			'config_upload_images_allowed'            => '',
			'config_upload_images_mime_types_allowed' => '',
			'config_cart_weight'                      => 1,
			'config_admin_bar'                        => 1,
			'config_admin_logo'                       => '',
			'config_icon'                             => '',
			'config_image_admin_thumb_width'          => 120,
			'config_image_admin_thumb_height'         => 120,
			'config_image_admin_list_width'           => 60,
			'config_image_admin_list_height'          => 60,
			'config_image_category_width'             => 240,
			'config_image_category_height'            => 240,
			'config_image_manufacturer_width'         => 240,
			'config_image_manufacturer_height'        => 240,
			'config_image_thumb_width'                => 120,
			'config_image_thumb_height'               => 120,
			'config_image_popup_width'                => 1024,
			'config_image_popup_height'               => 1024,
			'config_image_product_width'              => 420,
			'config_image_product_height'             => 420,
			'config_image_product_option_width'       => 50,
			'config_image_product_option_height'      => 50,
			'config_image_additional_width'           => 120,
			'config_image_additional_height'          => 120,
			'config_image_related_width'              => 120,
			'config_image_related_height'             => 120,
			'config_image_compare_width'              => 120,
			'config_image_compare_height'             => 120,
			'config_image_wishlist_width'             => 120,
			'config_image_wishlist_height'            => 120,
			'config_image_cart_width'                 => 80,
			'config_image_cart_height'                => 80,
			'config_mail_protocol'                    => 'smpt',
			'config_mail_parameter'                   => '',
			'config_smtp_host'                        => '',
			'config_smtp_username'                    => '',
			'config_smtp_password'                    => '',
			'config_smtp_port'                        => 25,
			'config_smtp_timeout'                     => 5,
			'config_alert_mail'                       => '',
			'config_account_mail'                     => '',
			'config_alert_emails'                     => '',
			'config_mail_logging'                     => 1,
			'config_fraud_detection'                  => '',
			'config_fraud_key'                        => '',
			'config_fraud_score'                      => '',
			'config_use_ssl'                          => 0,
			'config_seo_url'                          => 1,
			'config_maintenance'                      => 0,
			'config_image_max_mem'                    => '2G',
			'config_encryption'                       => '',
			'config_compression'                      => '',
			'config_jquery_cdn'                       => 0,
			'config_log_filename'                     => '',
			'config_debug_send_emails'                => '',
			'config_error_display'                    => '',
			'config_error_log'                        => '',
			'config_error_filename'                   => '',
			'config_google_analytics'                 => '',
			'config_statcounter'                      => '',
			'config_default_file_mode'                => 644,
			'config_default_dir_mode'                 => 755,
			'config_image_file_mode'                  => 644,
			'config_image_dir_mode'                   => 755,
			'config_plugin_file_mode'                 => 644,
			'config_plugin_dir_mode'                  => 755,
		);

		$this->data += $config_data + $defaults;

		$octals = array(
			'config_default_file_mode',
			'config_default_dir_mode',
			'config_image_file_mode',
			'config_image_dir_mode',
			'config_plugin_file_mode',
			'config_plugin_dir_mode',
		);

		//convert octals in strings back to regular integers
		foreach ($octals as $oct) {
			$this->data[$oct] = intval($this->data[$oct]);
		}

		//Template Data
		$this->data['data_layouts']           = $this->Model_Design_Layout->getLayouts();
		$this->data['themes']                 = $this->theme->getThemes();
		$this->data['stores']                 = $this->Model_Setting_Store->getStores();
		$this->data['countries']              = $this->Model_Localisation_Country->getCountries();
		$this->data['languages']              = $this->Model_Localisation_Language->getLanguages();
		$this->data['currencies']             = $this->Model_Localisation_Currency->getCurrencies();
		$this->data['length_classes']         = $this->Model_Localisation_LengthClass->getLengthClasses();
		$this->data['weight_classes']         = $this->Model_Localisation_WeightClass->getWeightClasses();
		$this->data['tax_classes']            = $this->Model_Localisation_TaxClass->getTaxClasses();
		$this->data['customer_groups']        = $this->Model_Sale_CustomerGroup->getCustomerGroups();
		$this->data['data_informations']      = array('' => _l(" --- None --- ")) + $this->Model_Catalog_Information->getInformations();
		$this->data['data_stock_statuses']    = $this->Model_Localisation_StockStatus->getStockStatuses();
		$this->data['data_order_statuses']    = $this->order->getOrderStatuses();
		$this->data['data_return_statuses']   = $this->order->getReturnStatuses();
		$this->data['data_return_policies']   = $this->cart->getReturnPolicies();
		$this->data['data_shipping_policies'] = $this->cart->getShippingPolicies();

		$this->data['data_mail_protocols'] = array(
			'smtp' => "SMTP",
			'mail' => _l("PHP Mail"),
		);

		$this->data['data_stock_display_types'] = array(
			'hide'   => _l("Do not display stock"),
			'status' => _l("Only show stock status"),
			-1       => _l("Display stock quantity available"),
			10       => _l("Display quantity up to 10"),
		);

		$this->data['data_show_product_related'] = array(
			0 => _l('Never'),
			1 => _l('Only When Unavailable'),
			2 => _l('Always'),
		);

		$this->data['load_theme_img'] = $this->url->link('setting/setting/theme');

		$this->data['text_add_return_policy']   = _l("Add a new <a href=\"%s\" target=\"_blank\">Return Policy</a>", $this->url->link('setting/return_policy'));
		$this->data['text_add_shipping_policy'] = _l("Add a new <a href=\"%s\" target=\"_blank\">Shipping Policy</a>", $this->url->link('setting/shipping_policy'));

		$this->data['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		$this->data['data_yes_no'] = array(
			1 => _l("Yes"),
			0 => _l("No"),
		);

		//Action Buttons
		$this->data['save']   = $this->url->link('setting/setting');
		$this->data['cancel'] = $this->url->link('setting/store');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	public function theme()
	{
		if (empty($_GET['theme'])) {
			$this->response->setOutput('No Theme Requested.');
			return false;
		}

		$image = DIR_SITE . 'catalog/view/theme/' . $_GET['theme'] . '/' . $_GET['theme'] . '.png';

		$width  = 300; //$this->config->get('config_image_admin_thumb_width');
		$height = 300; //$this->config->get('config_image_admin_thumb_height');

		if ($image) {
			$image = $this->image->resize($image, $width, $height);
		}

		if (!$image) {
			$image = $this->image->resize('no_image', $width, $height);
		}

		$this->response->setOutput("<img src=\"$image\" class =\"theme_preview\" />");
	}

	public function validate()
	{
		if (!$this->user->can('modify', 'setting/setting')) {
			$this->error['permission'] = _l("Warning: You do not have permission to modify settings!");
		}

		if (!$_POST['config_name']) {
			$this->error['config_name'] = _l("Store Name must be between 3 and 32 characters!");
		}

		if ((strlen($_POST['config_owner']) < 3) || (strlen($_POST['config_owner']) > 64)) {
			$this->error['config_owner'] = _l("Store Owner must be between 3 and 64 characters!");
		}

		if ((strlen($_POST['config_address']) < 3) || (strlen($_POST['config_address']) > 256)) {
			$this->error['config_address'] = _l("Store Address must be between 10 and 256 characters!");
		}

		if (!$this->validation->email($_POST['config_email'])) {
			$this->error['config_email'] = _l("E-Mail Address does not appear to be valid!");
		}

		if (!$this->validation->email($_POST['config_email_error'])) {
			$this->error['config_email_error'] = _l("E-Mail Address does not appear to be valid!");
		}

		if (!$this->validation->email($_POST['config_email_support'])) {
			$this->error['config_email_support'] = _l("E-Mail Address does not appear to be valid!");
		}

		if ((strlen($_POST['config_telephone']) < 3) || (strlen($_POST['config_telephone']) > 32)) {
			$this->error['config_telephone'] = _l("Telephone must be between 3 and 32 characters!");
		}

		if (!$_POST['config_title']) {
			$this->error['config_title'] = _l("Title must be between 3 and 32 characters!");
		}

		if (!$_POST['config_image_admin_thumb_width'] || !$_POST['config_image_admin_thumb_height']) {
			$this->error['image_admin_thumb'] = _l("Product Image Admin Thumb Size dimensions required!");
		}

		if (!$_POST['config_image_admin_list_width'] || !$_POST['config_image_admin_list_height']) {
			$this->error['image_admin_list'] = _l("Product Image Admin List Size dimensions required!");
		}

		if (!$_POST['config_image_category_width'] || !$_POST['config_image_category_height']) {
			$this->error['image_category'] = _l("Category List Size dimensions required!");
		}

		if (!$_POST['config_image_manufacturer_width'] || !$_POST['config_image_manufacturer_height']) {
			$this->error['image_manufacturer'] = _l("Designer List Size dimensions required!");
		}

		if (!$_POST['config_image_thumb_width'] || !$_POST['config_image_thumb_height']) {
			$this->error['image_thumb'] = _l("Product Image Thumb Size dimensions required!");
		}

		if (!$_POST['config_image_popup_width'] || !$_POST['config_image_popup_height']) {
			$this->error['image_popup'] = _l("Product Image Popup Size dimensions required!");
		}

		if (!$_POST['config_image_product_width'] || !$_POST['config_image_product_height']) {
			$this->error['image_product'] = _l("Product List Size dimensions required!");
		}

		if (!$_POST['config_image_additional_width'] || !$_POST['config_image_additional_height']) {
			$this->error['image_additional'] = _l("Additional Product Image Size dimensions required!");
		}

		if (!$_POST['config_image_related_width'] || !$_POST['config_image_related_height']) {
			$this->error['image_related'] = _l("Related Product Image Size dimensions required!");
		}

		if (!$_POST['config_image_compare_width'] || !$_POST['config_image_compare_height']) {
			$this->error['image_compare'] = _l("Compare Image Size dimensions required!");
		}

		if (!$_POST['config_image_wishlist_width'] || !$_POST['config_image_wishlist_height']) {
			$this->error['image_wishlist'] = _l("Wish List Image Size dimensions required!");
		}

		if (!$_POST['config_image_cart_width'] || !$_POST['config_image_cart_height']) {
			$this->error['image_cart'] = _l("Cart Image Size dimensions required!");
		}

		if (!$_POST['config_log_filename']) {
			$this->error['config_log_filename'] = _l("Log Filename Required!");
		}

		if (!$_POST['config_error_filename']) {
			$this->error['config_error_filename'] = _l("Error Log Filename required!");
		}

		if (!$_POST['config_admin_limit']) {
			$this->error['config_admin_limit'] = _l("Limit required!");
		}

		if (!$_POST['config_catalog_limit']) {
			$this->error['config_catalog_limit'] = _l("Limit required!");
		}

		$octals = array(
			'config_default_file_mode',
			'config_default_dir_mode',
			'config_image_file_mode',
			'config_image_dir_mode',
			'config_plugin_file_mode',
			'config_plugin_dir_mode'
		);
		foreach ($octals as $oct) {
			if ($_POST[$oct]) {
				$oct_val     = $_POST[$oct];
				$_POST[$oct] = '0' . "$oct_val";
			}
		}

		return $this->error ? false : true;
	}
}
