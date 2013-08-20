<?php
class Admin_Controller_Catalog_Product extends Controller
{
	public function index()
	{
		$this->language->load('catalog/product');

		$this->getList();
	}

	public function update()
	{
		$this->language->load('catalog/product');

		if ($this->request->isPost() && $this->validateForm()) {
			//Insert
			if (empty($_GET['product_id'])) {
				$this->Model_Catalog_Product->addProduct($_POST);
			} //Update
			else {
				$this->Model_Catalog_Product->editProduct($_GET['product_id'], $_POST);
			}

			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));

				$this->url->redirect($this->url->link('catalog/product'));
			}
		}

		$this->getForm();
	}

	public function change_class()
	{
		$this->language->load('catalog/product');

		if (!empty($_GET['product_id']) && $this->request->isPost() && $this->user->hasPermission('modify', 'catalog/product')) {
			$this->Model_Catalog_Product->editProduct($_GET['product_id'], $_POST, true);

			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success_class'));
			}
		}

		$this->url->redirect($this->url->link('catalog/product/update', $this->url->getQuery()));
	}

	public function delete()
	{
		$this->language->load('catalog/product');

		$this->document->setTitle($this->_('head_title'));

		if (!empty($_GET['product_id']) && $this->validateDelete()) {
			$this->Model_Catalog_Product->deleteProduct($_GET['product_id']);

			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));

				$this->url->redirect($this->url->link('catalog/product'));
			}
		}

		$this->getList();
	}

	public function copy()
	{
		$this->language->load('catalog/product');

		$this->document->setTitle($this->_('head_title'));

		if (!empty($_GET['product_id']) && $this->validateCopy()) {
			$this->Model_Catalog_Product->copyProduct($_GET['product_id']);

			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));

				$this->url->redirect($this->url->link('catalog/product'));
			}
		}

		$this->getList();
	}

	public function batch_update()
	{
		$this->language->load('catalog/product');

		if (!empty($_GET['selected']) && !empty($_GET['action'])) {
			if (($_GET['action'] === 'copy' && !$this->validateCopy()) ||
				($_GET['action'] === 'delete' && !$this->validateDelete())
			) {
				//Action not allowed
				$this->message->add("warning", $this->_('error_batch_action'));
			} else {
				foreach ($_GET['selected'] as $product_id) {
					$data = array();

					switch ($_GET['action']) {
						case 'enable':
							$data['status'] = 1;
							break;
						case 'disable':
							$data['status'] = 0;
							break;
						case 'product_class_id':
						case 'date_expires':
						case 'shipping_policy_id':
						case 'return_policy_id':
							$data[$_GET['action']] = $_GET['action_value'];
							break;
						case 'add_cat':
						case 'remove_cat':
							$categories = $this->Model_Catalog_Product->getProductCategories($product_id);

							if ($_GET['action'] === 'add_cat') {
								$categories[]             = $_GET['action_value'];
								$data['product_category'] = $categories;
							} else {
								$data['product_category'] = array_diff($categories, array($_GET['action_value']));
							}
							break;
						case 'copy':
							$this->Model_Catalog_Product->copyProduct($product_id);
							break;
						case 'delete':
							$this->Model_Catalog_Product->deleteProduct($product_id);
							break;
						default:
							$this->message->add('warning', $this->_('error_batch_action'));
							break 2; //break the for loop
					}

					if ($data) {
						$this->Model_Catalog_Product->editProduct($product_id, $data, true);
					}
				}
			}

			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
			}
		}

		$this->url->redirect($this->url->link('catalog/product', $this->url->getQueryExclude('action', 'action_value')));
	}

	private function getList()
	{
		//Page Head
		$this->document->setTitle($this->_('head_title'));

		//The Template
		$this->template->load('catalog/product_list');

		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('catalog/product'));

		//The Table Columns
		$categories      = $this->Model_Catalog_Category->getCategoriesWithParents();
		$product_classes = $this->Model_Catalog_ProductClass->getProductClasses();

		$columns = array();

		$columns['thumb'] = array(
			'type'         => 'image',
			'display_name' => $this->_('column_image'),
			'filter'       => false,
			'sortable'     => true,
			'sort_value'   => '__image_sort__image',
		);

		$columns['product_class_id'] = array(
			'type'         => 'select',
			'display_name' => $this->_('column_class'),
			'filter'       => true,
			'build_config' => array(
				'product_class_id',
				'name'
			),
			'build_data'   => $product_classes,
			'sortable'     => true,
			'sort_value'   => 'pc.name',
		);

		$columns['name'] = array(
			'type'         => 'text',
			'display_name' => $this->_('column_name'),
			'filter'       => true,
			'sortable'     => true,
			'sort_value'   => 'p.name',
		);

		$columns['model'] = array(
			'type'         => 'text',
			'display_name' => $this->_('column_model'),
			'filter'       => true,
			'sortable'     => true,
			'sort_value'   => 'p.model',
		);

		$columns['price'] = array(
			'type'         => 'int',
			'display_name' => $this->_('column_price'),
			'filter'       => true,
			'sortable'     => true,
			'sort_value'   => 'p.price',
		);

		$columns['cost'] = array(
			'type'         => 'int',
			'display_name' => $this->_('column_cost'),
			'filter'       => true,
			'sortable'     => true,
			'sort_value'   => 'p.cost',
		);

		$columns['categories'] = array(
			'type'         => 'multiselect',
			'display_name' => $this->_('column_category'),
			'filter'       => true,
			'build_config' => array(
				'category_id',
				'pathname'
			),
			'build_data'   => $categories,
			'sortable'     => false,
		);

		$columns['quantity'] = array(
			'type'         => 'int',
			'display_name' => $this->_('column_quantity'),
			'filter'       => true,
			'sortable'     => true,
			'sort_value'   => 'p.quantity',
		);

		$columns['status'] = array(
			'type'         => 'select',
			'display_name' => $this->_('column_status'),
			'filter'       => true,
			'build_data'   => $this->_('data_statuses'),
			'sortable'     => true,
			'sort_value'   => 'p.status',
		);

		//Get Sorted / Filtered Data
		$sort   = $this->sort->getQueryDefaults('p.name', 'ASC');
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		$product_total = $this->Model_Catalog_Product->getTotalProducts($filter);
		$products      = $this->Model_Catalog_Product->getProducts($sort + $filter);

		$url_query = $this->url->getQueryExclude('product_id');

		foreach ($products as &$product) {
			$product['actions'] = array(
				'edit' => array(
					'text' => $this->_('text_edit'),
					'href' => $this->url->link('catalog/product/update', 'product_id=' . $product['product_id'])
				),
				'copy' => array(
					'text' => $this->_('text_copy'),
					'href' => $this->url->link('catalog/product/copy', 'product_id=' . $product['product_id'])
				)
			);

			if (!$this->order->productInConfirmedOrder($product['product_id'])) {
				$product['actions']['delete'] = array(
					'text' => $this->_('text_delete'),
					'href' => $this->url->link('catalog/product/delete', 'product_id=' . $product['product_id'] . '&' . $url_query),
				);
			}

			$product['thumb'] = $this->image->resize($product['image'], $this->config->get('config_image_admin_list_width'), $this->config->get('config_image_admin_list_height'));

			$product['categories'] = $this->Model_Catalog_Product->getProductCategories($product['product_id']);

			$product['price'] = $this->currency->format($product['price']);
			$product['cost']  = $this->currency->format($product['cost']);

			$special            = $this->Model_Catalog_Product->getProductActiveSpecial($product['product_id']);
			$product['special'] = !empty($special) ? $this->currency->format($special['price']) : '';

			if ($product['special']) {
				$product['price'] = "<span class =\"product_retail\">$product[price]</span><br /><span class=\"product_special\">$product[special]</span>";
			}

			//The # in front of the key signifies we want to output the raw string for the value when rendering the table
			if ($product['date_expires'] === DATETIME_ZERO) {
				$product['#date_expires'] = $this->_('text_no_expiration');
			}

			if (!(int)$product['subtract']) {
				$product['quantity'] = $this->_('text_unlimited');
			}
		}
		unset($product);

		//Build The Table
		$tt_data = array(
			'row_id' => 'product_id',
		);

		$this->table->init();
		$this->table->setTemplate('table/list_view');
		$this->table->setColumns($columns);
		$this->table->setRows($products);
		$this->table->setTemplateData($tt_data);
		$this->table->mapAttribute('filter_value', $filter);

		$this->data['list_view'] = $this->table->render();

		//Additional Data
		if (count($product_classes) > 1) {
			foreach ($product_classes as &$product_class) {
				$product_class['insert'] = $this->url->link('catalog/product/update', 'product_class_id=' . $product_class['product_class_id']);
			}
			unset($product_class);

			$this->data['product_classes'] = $product_classes;
		}

		//Batch actions
		$this->data['batch_actions'] = array(
			'enable'             => array(
				'label' => $this->_('text_enable'),
			),

			'disable'            => array(
				'label' => $this->_('text_disable'),
			),

			'product_class_id'   => array(
				'label'        => "Change Class",
				'type'         => 'select',
				'build_config' => array(
					'product_class_id',
					'name'
				),
				'build_data'   => $product_classes,
				'default'      => '',
			),

			'date_expires'       => array(
				'label'   => "Product Expiration Date",
				'type'    => 'datetime',
				'default' => $this->date->add('30 days'),
			),

			'shipping_policy_id' => array(
				'label'        => $this->_('text_shipping_policy'),
				'type'         => 'select',
				'build_data'   => $this->cart->getShippingPolicies(),
				'build_config' => array(
					false,
					'title'
				),
				'default'      => $this->config->get('config_default_shipping_policy'),
			),

			'return_policy_id'   => array(
				'label'        => $this->_('text_return_policy'),
				'type'         => 'select',
				'build_data'   => $this->cart->getReturnPolicies(),
				'build_config' => array(
					false,
					'title'
				),
				'default'      => $this->config->get('config_default_return_policy'),
			),

			'add_cat'            => array(
				'label'        => "Add Category",
				'type'         => 'select',
				'build_data'   => $categories,
				'build_config' => array(
					'category_id',
					'name'
				),
			),

			'remove_cat'         => array(
				'label'        => "Remove Category",
				'type'         => 'select',
				'build_data'   => $categories,
				'build_config' => array(
					'category_id',
					'name'
				),
			),

			'copy'               => array(
				'label' => $this->_('text_copy'),
			),

			'delete'             => array(
				'label' => $this->_('text_delete'),
			),
		);

		$this->data['batch_update'] = 'catalog/product/batch_update';

		//Render Limit Menu
		$this->data['limits'] = $this->sort->render_limit();

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $product_total;

		$this->data['pagination'] = $this->pagination->render();

		//Action Buttons
		$this->data['insert'] = $this->url->link('catalog/product/update');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	private function getForm()
	{
		//Page Head
		$this->document->setTitle($this->_('head_title'));

		//Insert or Update
		$product_id = $this->data['product_id'] = isset($_GET['product_id']) ? $_GET['product_id'] : 0;

		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('catalog/product'));

		if (!$product_id) {
			$this->breadcrumb->add($this->_('text_insert'), $this->url->link('catalog/product/update'));
		} else {
			$this->breadcrumb->add($this->_('text_edit'), $this->url->link('catalog/product/update', 'product_id=' . $product_id));
		}

		//Load Information
		if ($product_id && !$this->request->isPost()) {
			$product_info = $this->Model_Catalog_Product->getProduct($product_id);

			$product_info['date_available']     = $this->date->format($product_info['date_available'], 'Y-m-d');
			$product_info['product_tags']       = $this->Model_Catalog_Product->getProductTags($product_id);
			$product_info['product_store']      = $this->Model_Catalog_Product->getProductStores($product_id);
			$product_info['product_attributes'] = $this->Model_Catalog_Product->getProductAttributes($product_id);
			$product_info['product_options']    = $this->Model_Catalog_Product->getProductOptions($product_id);
			$product_info['product_discounts']  = $this->Model_Catalog_Product->getProductDiscounts($product_id);
			$product_info['product_specials']   = $this->Model_Catalog_Product->getProductSpecials($product_id);
			$product_info['product_images']     = $this->Model_Catalog_Product->getProductImages($product_id);
			$product_info['product_category']   = $this->Model_Catalog_Product->getProductCategories($product_id);
			$product_info['product_download']   = $this->Model_Catalog_Product->getProductDownloads($product_id);
			$product_info['product_reward']     = $this->Model_Catalog_Product->getProductRewards($product_id);
			$product_info['product_layout']     = $this->Model_Catalog_Product->getProductLayouts($product_id);
			$product_info['product_template']   = $this->Model_Catalog_Product->getProductTemplates($product_id);
			$product_info['product_related']    = $this->Model_Catalog_Product->getProductRelated($product_id);
		}

		//Apply Product Class
		$product_classes          = $this->Model_Catalog_ProductClass->getProductClasses();
		$default_product_class_id = isset($_GET['product_class_id']) ? $_GET['product_class_id'] : $this->config->get('config_default_product_class_id');

		//Set Values or Defaults
		$defaults = array(
			'product_class_id'   => $default_product_class_id,
			'model'              => '',
			'sku'                => '',
			'upc'                => '',
			'location'           => '',
			'alias'              => '',
			'product_store'      => array($this->config->get('config_default_store_id')),
			'name'               => '',
			'description'        => '',
			'teaser'             => '',
			'information'        => '',
			'meta_keywords'      => '',
			'meta_description'   => '',
			'image'              => '',
			'manufacturer_id'    => 0,
			'shipping'           => 1,
			'price'              => '',
			'cost'               => '',
			'return_policy_id'   => $this->config->get('config_default_return_policy'),
			'shipping_policy_id' => $this->config->get('config_default_shipping_policy'),
			'tax_class_id'       => $this->config->get('config_tax_default_id'),
			'date_available'     => $this->date->now(),
			'date_expires'       => '',
			'editable'           => 1,
			'quantity'           => 1,
			'minimum'            => 1,
			'subtract'           => 1,
			'sort_order'         => 1,
			'stock_status_id'    => $this->config->get('config_stock_status_id'),
			'status'             => 1,
			'weight'             => '',
			'weight_class_id'    => $this->config->get('config_weight_class_id'),
			'length'             => '',
			'width'              => '',
			'height'             => '',
			'length_class_id'    => $this->config->get('config_length_class_id'),
			'product_options'    => array(),
			'product_discounts'  => array(),
			'product_specials'   => array(),
			'product_attributes' => array(),
			'product_images'     => array(),
			'product_download'   => array(),
			'product_category'   => array(),
			'product_related'    => array(),
			'product_tags'       => array(),
			'points'             => '',
			'product_reward'     => array(),
			'product_layout'     => array(),
			'product_template'   => array(),
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($product_info[$key])) {
				$this->data[$key] = $product_info[$key];
			} else {
				$this->data[$key] = $default;
			}
		}

		//TODO: Make tags into a list of tag inputs (with js)
		if (is_string($this->data['product_tags'])) {
			$this->data['product_tags'] = explode(',', $this->data['product_tags']);
		}

		//Additional Data
		$this->data['data_product_classes']   = $product_classes;
		$this->data['data_manufacturers']     = array('' => $this->_('text_none')) + $this->Model_Catalog_Manufacturer->getManufacturers(array('sort' => 'name'));
		$this->data['data_tax_classes']       = array('' => $this->_('text_none')) + $this->Model_Localisation_TaxClass->getTaxClasses();
		$this->data['data_weight_classes']    = $this->Model_Localisation_WeightClass->getWeightClasses();
		$this->data['data_length_classes']    = $this->Model_Localisation_LengthClass->getLengthClasses();
		$this->data['data_customer_groups']   = $this->Model_Sale_CustomerGroup->getCustomerGroups();
		$this->data['data_downloads']         = $this->Model_Catalog_Download->getDownloads();
		$this->data['data_categories']        = $this->Model_Catalog_Category->getCategoriesWithParents();
		$this->data['data_stores']            = $this->Model_Setting_Store->getStores();
		$this->data['data_layouts']           = array('' => '') + $this->Model_Design_Layout->getLayouts();
		$this->data['data_templates']         = $this->template->getTemplatesFrom('product', false, '');
		$this->data['data_shipping_policies'] = $this->cart->getShippingPolicies();
		$this->data['data_return_policies']   = $this->cart->getReturnPolicies();

		$this->_('text_add_shipping_policy', $this->url->link('setting/shipping_policy'));
		$this->_('text_add_return_policy', $this->url->link('setting/return_policy'));
		$this->_('text_option_help', $this->config->get('config_email'));
		$this->_('text_not_editable', $this->data['name'], $this->config->get('config_email'));

		//TODO: do we really need ths here?
		$this->data['no_image'] = $this->image->resize('no_image.png', $this->config->get('config_image_admin_thumb_width'), $this->config->get('config_image_admin_thumb_height'));

		//Translations
		$this->data['translations'] = $this->Model_Catalog_Product->getProductTranslations($product_id);

		$image_width  = $this->config->get('config_image_product_option_width');
		$image_height = $this->config->get('config_image_product_option_height');

		//Product Attribute Template Defaults
		$this->data['product_attributes']['__ac_template__'] = array(
			'attribute_id' => '',
			'name'         => '',
			'image'        => '',
			'text'         => '',
			'sort_order'   => 0,
		);

		//Product Options Unused Option Values
		foreach ($this->data['product_options'] as &$product_option) {
			if (!empty($product_option['product_option_values'])) {
				$filter = array(
					'!option_value_ids' => array_column($product_option['product_option_values'], 'option_value_id'),
				);
			} else {
				$filter = array();
			}

			$option_values = $this->Model_Catalog_Option->getOptionValues($product_option['option_id'], $filter);

			$product_option['unused_option_values'] = $option_values;

		}
		unset($product_option);

		//Product Options Template Defaults
		$this->data['product_options']['__ac_template__'] = array(
			'product_option_id'     => 0,
			'option_id'             => '',
			'name'                  => '',
			'display_name'          => '',
			'type'                  => '',
			'required'              => 1,
			'sort_order'            => 0,
			'unused_option_values'  => array(
				'__ac_template__' => array(
					'option_value_id' => '',
					'value'           => '',
				)
			),
			'product_option_values' => array(
				'__ac_template__' => array(
					'product_option_value_id' => 0,
					'option_value_id'         => '',
					'default'                 => 0,
					'value'                   => '',
					'image'                   => '',
					'quantity'                => 1,
					'subtract'                => 0,
					'cost'                    => 0,
					'price'                   => 0,
					'points'                  => 0,
					'weight'                  => 0,
					'sort_order'              => 0,
					'restrictions'            => array(
						'__ac_template__' => array(
							'product_option_value_id'  => 0,
							'restrict_option_value_id' => 0,
							'quantity'                 => 0,
						)
					),

				)
			),
		);

		//Product Discount Template Defaults
		$this->data['product_discounts']['__ac_template__'] = array(
			'customer_group_id' => $this->config->get('config_customer_group_id'),
			'quantity'          => 0,
			'priority'          => 0,
			'price'             => 0,
			'date_start'        => $this->date->now(),
			'date_end'          => $this->date->add('30 days'),
		);

		//Product Special Template Defaults
		$this->data['product_specials']['__ac_template__'] = array(
			'customer_group_id' => $this->config->get('config_customer_group_id'),
			'priority'          => 0,
			'price'             => 0,
			'date_start'        => $this->date->now(),
			'date_end'          => $this->date->add('30 days'),
		);

		//Product Additional Images Template Defaults
		$this->data['product_images']['__ac_template__'] = array(
			'image'      => '',
			'thumb'      => '',
			'sort_order' => 0,
		);

		//Ajax Urls
		$this->data['url_generate_url']           = $this->url->ajax('catalog/product/generate_url');
		$this->data['url_generate_model']         = $this->url->ajax('catalog/product/generate_model');
		$this->data['url_autocomplete']           = $this->url->ajax('catalog/product/autocomplete');
		$this->data['url_attribute_autocomplete'] = $this->url->ajax('catalog/attribute_group/autocomplete');
		$this->data['url_option_autocomplete']    = $this->url->ajax('catalog/option/autocomplete');

		//Action Buttons
		$this->data['save']         = $this->url->link('catalog/product/update', 'product_id=' . $product_id);
		$this->data['cancel']       = $this->url->link('catalog/product');
		$this->data['change_class'] = $this->url->link('catalog/product/change_class', 'product_id=' . $product_id);

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//The Template
		$this->template->load($this->Model_Catalog_ProductClass->getTemplate($this->data['product_class_id']));

		//Render
		$this->response->setOutput($this->render());
	}

	private function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if (!$this->validation->text($_POST['name'], 3, 254)) {
			$this->error['name'] = $this->_('error_name');
		}

		//We do not allow changing the Model ID for products that have been ordered
		//A new product must be created
		if (!empty($_GET['product_id']) && $this->order->productInConfirmedOrder($_GET['product_id'])) {
			$model = $this->db->queryVar("SELECT model FROM " . DB_PREFIX . "product WHERE product_id = " . (int)$_GET['product_id']);

			if ($model !== $_POST['model']) {
				$this->error['model'] = $this->_('error_model_confirmed_order_product');
				$_POST['model']       = $model;
			}
		} else {
			if (!$_POST['model']) {
				$_POST['model'] = $this->Model_Catalog_Product->generateModel(0, $_POST['name']);
			}

			if (!$this->validation->text($_POST['model'], 1, 64)) {
				$this->error['model'] = $this->_('error_model');
			}

			$product_id = isset($_GET['product_id']) ? "AND product_id != '" . (int)$_GET['product_id'] . "'" : '';

			$exists = $this->db->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "product WHERE model='" . $this->db->escape($_POST['model']) . "' $product_id");

			if ($exists) {
				$this->error['model'] = $this->_('error_dup_model');
			}
		}

		if (empty($_POST['alias']) && $this->config->get('config_seo_url')) {
			$_POST['alias'] = $this->Model_Setting_UrlAlias->getUniqueAlias($_POST['name'], 'product/product', 'product_id=' . $product_id);
		}

		if (isset($_POST['product_images'])) {
			foreach ($_POST['product_images'] as $key => $image) {
				if (strtolower($image['image']) == 'data/no_image.png' || !$image['image']) {
					unset($_POST['product_images'][$key]);
				}
			}
		}

		//validate the quantities
		if (!empty($_POST['product_options'])) {
			foreach ($_POST['product_options'] as $option_id => $product_option) {
				if (empty($product_option['product_option_values'])) {
					$this->error["option_value$option_id"] = $this->_('error_no_option_value', $product_option['name']);
					continue;
				}

				//Validate Product Option Value Restrictions
				foreach ($product_option['product_option_values'] as $product_option_value_id => $product_option_value) {
					if (!empty($product_option_value['restrictions'])) {
						foreach ($product_option_value['restrictions'] as $r_key => $restriction) {
							if ($restriction['restrict_option_value_id'] == $product_option_value_id) {
								$this->error["product_options[$option_id][product_option_value][$product_option_value_id][restrictions][$r_key][restrict_option_value_id]"] = $this->_('error_pov_restrict_same');
							}

							foreach ($product_option_value['restrictions'] as $r_key2 => $restriction2) {
								if ($r_key != $r_key2) {
									if ($restriction['restrict_option_value_id'] == $restriction2['restrict_option_value_id']) {
										$this->error["product_options[$option_id][product_option_value][$product_option_value_id][restrictions][$r_key][restrict_option_value_id]"] = $this->_('error_dup_restrict');
									}
								}
							}

							if (isset($product_option['product_option_values'][$restriction['restrict_option_value_id']])) {
								$this->error["product_options[$option_id][product_option_value][$product_option_value_id][restrictions][$r_key][restrict_option_value_id]"] = sprintf($this->_('error_restrict_same_option_id'), ucfirst($product_option['type']));
							}
						}
					}
				}
			}
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		$product_ids = array();

		if (!empty($_GET['selected'])) {
			$product_ids = $_GET['selected'];
		}

		if (!empty($_GET['product_id'])) {
			$product_ids[] = $_GET['product_id'];
		}

		foreach ($product_ids as $product_id) {
			if ($this->order->productInConfirmedOrder($product_id)) {
				$this->error['product' . $product_id] = $this->_('error_confirmed_order_product', $this->Model_Catalog_Product->getProductField($product_id, 'name'));
			}
		}

		return $this->error ? false : true;
	}

	private function validateCopy()
	{
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		return $this->error ? false : true;
	}

	public function select()
	{
		if (!isset($_POST['filter'])) {
			return;
		}

		$filter     = $_POST['filter'];
		$select     = isset($_POST['select']) ? $_POST['select'] : array();
		$field_keys = isset($_POST['fields']) ? $_POST['fields'] : array();

		$fields = array();
		foreach (explode(',', $field_keys) as $key) {
			$fields[$key] = 1;
		}

		$products = $this->Model_Catalog_Product->getProducts($filter);

		$html = '';

		foreach ($products as $product) {
			$data[$product['product_id']] = array_intersect_key($product, $fields);

			$selected = $product['product_id'] == $select ? 'selected="selected"' : '';

			$html .= "<option value=\"$product[product_id]\" " . $selected . ">$product[name]</option>";
		}

		$json = array(
			'option_data' => $data,
			'html'        => $html,
		);

		$this->response->setOutput(json_encode($json));
	}

	public function autocomplete()
	{
		//Sort
		$sort = $this->sort->getQueryDefaults('name', 'ASC', $this->config->get('config_autocomplete_limit'));

		//Filter
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		//Label and Value
		$label = !empty($_GET['label']) ? $_GET['label'] : 'name';
		$value = !empty($_GET['value']) ? $_GET['value'] : 'product_id';

		//Load Sorted / Filtered Data
		$products = $this->Model_Catalog_Product->getProducts($sort + $filter);

		foreach ($products as &$product) {
			$product['label'] = $product[$label];
			$product['value'] = $product[$value];

			$product['name']  = html_entity_decode($product['name'], ENT_QUOTES, 'UTF-8');
			$product['thumb'] = $this->image->resize($product['image'], 100, 100);

			$product_options = $this->Model_Catalog_Product->getProductOptions($product['product_id']);

			foreach ($product_options as &$product_option) {
				foreach ($product_option['product_option_values'] as &$product_option_value) {
					$product_option_value['price'] = $this->currency->format($product_option_value['price']);
				}
			}
			unset($product_option);

		}
		unset($product);

		//JSON response
		$this->response->setOutput(json_encode($products));
	}

	public function generate_url()
	{
		if (!empty($_POST['name'])) {
			$product_id = !empty($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

			$url = $this->Model_Setting_UrlAlias->getUniqueAlias($_POST['name'], 'product/product', 'product_id=' . $product_id);
		} else {
			$url = '';
		}

		$this->response->setOutput($url);
	}

	public function generate_model()
	{
		if (!empty($_POST['name'])) {
			$product_id = !empty($_POST['product_id']) ? $_POST['product_id'] : 0;

			$this->response->setOutput($this->Model_Catalog_Product->generateModel($product_id, $_POST['name']));
		}
	}
}
